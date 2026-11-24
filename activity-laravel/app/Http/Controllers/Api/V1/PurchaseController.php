<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Purchase CRUD
    |--------------------------------------------------------------------------
    */

    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Purchase::query()
                ->with(['user', 'products'])
                ->orderByDesc('purchase_time')
                ->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'total_price' => ['required', 'numeric', 'min:0'],
            'purchase_time' => ['required', 'date'],
            'payment_id' => ['required', 'integer', 'min:1'],
            'email' => ['required', 'email', 'exists:users,email'],
            'items' => ['sometimes', 'array'],
            'items.*.code' => ['required', 'string', 'exists:products,code'],
            'items.*.product_quantity' => ['required', 'integer', 'min:1'],
        ]);

        $purchase = DB::transaction(function () use ($validated) {
            $items = $validated['items'] ?? [];
            unset($validated['items']);

            $purchase = Purchase::create($validated);

            foreach ($items as $item) {
                $product = Product::query()->findOrFail($item['code']);

                $purchase->items()->create([
                    'code' => $product->code,
                    'product_price' => $product->price,
                    'product_quantity' => $item['product_quantity'],
                    'product_name' => $product->name,
                ]);
            }

            return $purchase;
        });

        return response()->json([
            'message' => 'Purchase created successfully.',
            'data' => $purchase->load(['items', 'user']),
        ], 201);
    }

    public function show(Purchase $purchase): JsonResponse
    {
        return response()->json([
            'data' => $purchase->load(['user', 'products']),
        ]);
    }

    public function update(Request $request, Purchase $purchase): JsonResponse
    {
        $validated = $request->validate([
            'total_price' => ['sometimes', 'numeric', 'min:0'],
            'purchase_time' => ['sometimes', 'date'],
            'payment_id' => ['sometimes', 'integer', 'min:1'],
            'email' => ['sometimes', 'email', 'exists:users,email'],
        ]);

        $purchase->update($validated);

        return response()->json([
            'message' => 'Purchase updated successfully.',
            'data' => $purchase->fresh()->load(['user', 'products']),
        ]);
    }

    public function destroy(Purchase $purchase): JsonResponse
    {
        $purchase->delete();

        return response()->json(['message' => 'Purchase deleted successfully.']);
    }

    /*
    |--------------------------------------------------------------------------
    | B. Filtering data (purchases)
    |--------------------------------------------------------------------------
    */

    // B. Get all the purchases where total_price is more than or equal to 100.
    public function totalPriceAtLeastHundred(): JsonResponse
    {
        return response()->json([
            'data' => Purchase::query()->where('total_price', '>=', 100)->get(),
        ]);
    }

    // B. Get all the purchases made by the user 'john@example.com'.
    public function byUser(string $email): JsonResponse
    {
        return response()->json([
            'data' => Purchase::query()
                ->with(['user', 'products'])
                ->where('email', $email)
                ->get(),
        ]);
    }

    // B. Get all the purchase products where product_quantity is lower than or equal to 2.
    public function lowQuantityItems(): JsonResponse
    {
        return response()->json([
            'data' => PurchaseProduct::query()
                ->with(['purchase', 'product'])
                ->where('product_quantity', '<=', 2)
                ->get(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | D. Years with more than 100 purchases
    |--------------------------------------------------------------------------
    */

    public function yearsWithOverHundredPurchases(): JsonResponse
    {
        return response()->json([
            'data' => Purchase::query()
                ->selectRaw('YEAR(purchase_time) as year, COUNT(*) as total_purchases')
                ->groupByRaw('YEAR(purchase_time)')
                ->havingRaw('COUNT(*) > 100')
                ->orderBy('year')
                ->get(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | E. Joining the 4 tables
    |--------------------------------------------------------------------------
    */

    // E. Get every purchase with the buyer's full name and each product bought.
    public function withBuyerAndProducts(): JsonResponse
    {
        $purchases = Purchase::query()
            ->with(['user', 'products'])
            ->orderByDesc('purchase_time')
            ->get()
            ->map(fn (Purchase $purchase) => [
                'purchase_id' => $purchase->purchase_id,
                'buyer_full_name' => $purchase->user?->full_name ?? 'Unknown buyer',
                'buyer_email' => $purchase->email,
                'total_price' => $purchase->total_price,
                'purchase_time' => $purchase->purchase_time,
                'products' => $purchase->products->map(fn (Product $product) => [
                    'code' => $product->code,
                    'name' => $product->pivot->product_name,
                    'quantity' => $product->pivot->product_quantity,
                    'price' => $product->pivot->product_price,
                ])->values(),
            ]);

        return response()->json(['data' => $purchases]);
    }

    // E. Get the total quantity sold per product, from best seller to lowest.
    public function quantitySoldPerProduct(): JsonResponse
    {
        return response()->json([
            'data' => PurchaseProduct::query()
                ->select(
                    'purchase_products.code',
                    'purchase_products.product_name',
                    DB::raw('SUM(purchase_products.product_quantity) as total_quantity_sold'),
                )
                ->groupBy('purchase_products.code', 'purchase_products.product_name')
                ->orderByDesc('total_quantity_sold')
                ->get(),
        ]);
    }

    // E. Get the total amount spent per user.
    public function amountSpentPerUser(): JsonResponse
    {
        return response()->json([
            'data' => Purchase::query()
                ->join('users', 'purchases.email', '=', 'users.email')
                ->select(
                    'users.email',
                    'users.first_name',
                    'users.last_name',
                    DB::raw('SUM(purchases.total_price) as total_amount_spent'),
                )
                ->groupBy('users.email', 'users.first_name', 'users.last_name')
                ->orderByDesc('total_amount_spent')
                ->get(),
        ]);
    }
}
