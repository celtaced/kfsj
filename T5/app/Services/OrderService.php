<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function create(User $user, array $items): Order
    {
        return DB::transaction(function () use ($user, $items) {
            $ids = array_column($items, 'product_id');
            $products = Product::whereIn('id', $ids)->lockForUpdate()->get()->keyBy('id');
            $total = 0;
            $snapshots = [];
            foreach ($items as $item) {
                $product = $products->get($item['product_id']);
                if (! $product || ! $product->active) {
                    throw ValidationException::withMessages([
                        'items' => ['Uno o mas productos ya no estan disponibles.'],
                    ]);
                }
                $total += $product->price_cents * $item['quantity'];
                $snapshots[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit_price_cents' => $product->price_cents,
                    'quantity' => $item['quantity'],
                ];
            }

            $order = Order::create([
                'uuid' => (string) Str::uuid(),
                'user_id' => $user->id,
                'total_cents' => $total,
                'currency' => config('services.stripe.currency', 'usd'),
                'status' => 'pending',
            ]);
            $order->items()->createMany($snapshots);

            return $order->load('items');
        });
    }
}
