<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOrderRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(
            Order::where('user_id', $request->user()->id)
                ->with(['items', 'payment'])->latest()->paginate(20)
        );
    }

    public function store(CreateOrderRequest $request, OrderService $orders): JsonResponse
    {
        return response()->json($orders->create($request->user(), $request->validated('items')), 201);
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        return response()->json($order->load(['items', 'payment']));
    }
}
