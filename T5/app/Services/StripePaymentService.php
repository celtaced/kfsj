<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;
use Symfony\Component\HttpKernel\Exception\HttpException;

class StripePaymentService
{
    public function createOrRetrieve(Order $order): array
    {
        $secret = config('services.stripe.secret');
        if (! is_string($secret) || $secret === '') {
            throw new HttpException(503, 'Stripe no esta configurado.');
        }

        try {
            return DB::transaction(function () use ($order, $secret) {
                $order = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
                abort_if($order->status === 'paid', 409, 'La orden ya esta pagada.');
                $payment = $order->payment()->firstOrCreate([], [
                    'amount_cents' => $order->total_cents,
                    'currency' => $order->currency,
                    'status' => 'pending',
                ]);
                $stripe = new StripeClient($secret);
                $intent = $payment->stripe_payment_intent_id
                    ? $stripe->paymentIntents->retrieve($payment->stripe_payment_intent_id)
                    : $stripe->paymentIntents->create([
                        'amount' => $order->total_cents,
                        'currency' => $order->currency,
                        'automatic_payment_methods' => ['enabled' => true],
                        'metadata' => ['order_id' => (string) $order->id],
                    ], ['idempotency_key' => 't5-order-'.$order->uuid]);
                $payment->update(['stripe_payment_intent_id' => $intent->id]);

                return ['payment_id' => $payment->id, 'client_secret' => $intent->client_secret];
            });
        } catch (ApiErrorException $exception) {
            Log::error('Stripe payment request failed', ['exception' => $exception]);
            throw new HttpException(502, 'No fue posible comunicarse con Stripe.', $exception);
        }
    }
}
