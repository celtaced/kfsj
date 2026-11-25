<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Product::where('active', true)->orderBy('id')->paginate(20));
    }

    public function show(Product $product): JsonResponse
    {
        abort_unless($product->active, 404);

        return response()->json($product);
    }

    public function store(ProductRequest $request): JsonResponse
    {
        return response()->json(Product::create($request->validated()), 201);
    }

    public function update(ProductRequest $request, Product $product): JsonResponse
    {
        $product->update($request->validated());

        return response()->json($product);
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json(['message' => 'Producto eliminado.']);
    }
}
