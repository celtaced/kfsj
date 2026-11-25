<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Services\StripePaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use UnexpectedValueException;

class PaymentController extends Controller
{
    public function create(Request $request, Order $order, StripePaymentService $payments): JsonResponse
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        return response()->json($payments->createOrRetrieve($order));
    }

    public function webhook(Request $request): JsonResponse
    {
        $secret = config('services.stripe.webhook_secret');
        abort_unless(is_string($secret) && $secret !== '', 503, 'El webhook de Stripe no esta configurado.');
        try {
            $event = Webhook::constructEvent(
                $request->getContent(), $request->header('Stripe-Signature', ''), $secret
            );
        } catch (UnexpectedValueException|SignatureVerificationException $exception) {
            return response()->json(['message' => 'Firma o contenido de webhook invalido.'], 400);
        }

        if (! in_array($event->type, ['payment_intent.succeeded', 'payment_intent.payment_failed'], true)) {
            return response()->json(['received' => true]);
        }

        $intent = $event->data->object;
        DB::transaction(function () use ($intent, $event) {
            $payment = Payment::where('stripe_payment_intent_id', $intent->id)->first();
            abort_unless($payment, 503, 'Pago no registrado; reintente el webhook.');
            $order = Order::whereKey($payment->order_id)->lockForUpdate()->firstOrFail();
            $payment = Payment::whereKey($payment->id)->lockForUpdate()->firstOrFail();
            abort_unless(
                $payment->amount_cents === $intent->amount
                && $payment->currency === $intent->currency
                && ($event->type !== 'payment_intent.succeeded'
                    || $payment->amount_cents === $intent->amount_received),
                400,
                'El importe o moneda no coincide con la orden.'
            );
            if ($event->type === 'payment_intent.succeeded') {
                $payment->update(['status' => 'succeeded', 'failure_code' => null]);
                $order->update(['status' => 'paid']);
            } elseif ($payment->status !== 'succeeded') {
                $payment->update([
                    'status' => 'failed',
                    'failure_code' => $intent->last_payment_error->code ?? 'payment_failed',
                ]);
                $order->update(['status' => 'payment_failed']);
            }
        });

        return response()->json(['received' => true]);
    }
}
