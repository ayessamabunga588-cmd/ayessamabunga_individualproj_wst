<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseProduct;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Headline numbers for the whole store.
     */
    public function summary(): JsonResponse
    {
        return response()->json([
            'data' => [
                'total_products' => Product::query()->count(),
                'total_users' => User::query()->count(),
                'total_purchases' => Purchase::query()->count(),
                'total_items_sold' => (int) PurchaseProduct::query()->sum('product_quantity'),
                'total_revenue' => (float) Purchase::query()->sum('total_price'),
            ],
        ]);
    }

    /**
     * Inventory totals plus the best and worst stocked products.
     */
    public function inventory(): JsonResponse
    {
        return response()->json([
            'data' => [
                'total_inventory_quantity' => (int) Product::query()->sum('inventory_quantity'),
                'average_inventory_quantity' => round((float) Product::query()->avg('inventory_quantity'), 2),
                'highest_inventory_product' => Product::query()->orderByDesc('inventory_quantity')->first(),
                'lowest_inventory_product' => Product::query()->orderBy('inventory_quantity')->first(),
            ],
        ]);
    }

    /**
     * Every per-manufacturer aggregation from section D.
     */
    public function manufacturers(): JsonResponse
    {
        return response()->json([
            'data' => [
                'total_inventory_per_manufacturer' => Product::query()
                    ->select('manufacturer', DB::raw('SUM(inventory_quantity) as total_inventory_quantity'))
                    ->groupBy('manufacturer')
                    ->orderBy('manufacturer')
                    ->get(),

                'average_inventory_per_manufacturer' => Product::query()
                    ->select('manufacturer', DB::raw('AVG(inventory_quantity) as average_inventory_quantity'))
                    ->groupBy('manufacturer')
                    ->orderBy('manufacturer')
                    ->get(),

                'highest_inventory_product_per_manufacturer' => $this->productsPerManufacturerExtreme('MAX'),

                'lowest_inventory_product_per_manufacturer' => $this->productsPerManufacturerExtreme('MIN'),

                'average_inventory_per_product_ascending' => Product::query()
                    ->select('manufacturer', DB::raw('AVG(inventory_quantity) as average_inventory_quantity'))
                    ->groupBy('manufacturer')
                    ->orderBy('average_inventory_quantity')
                    ->get(),

                'average_inventory_per_product_for_sony_and_apple' => Product::query()
                    ->select('manufacturer', DB::raw('AVG(inventory_quantity) as average_inventory_quantity'))
                    ->whereIn('manufacturer', ['Sony', 'Apple'])
                    ->groupBy('manufacturer')
                    ->orderBy('manufacturer')
                    ->get(),

                'distinct_product_names_per_manufacturer' => Product::query()
                    ->select('manufacturer', DB::raw('COUNT(DISTINCT name) as distinct_product_names'))
                    ->groupBy('manufacturer')
                    ->orderBy('manufacturer')
                    ->get(),
            ],
        ]);
    }

    /**
     * Total quantity sold per product, best seller first.
     */
    public function sales(): JsonResponse
    {
        return response()->json([
            'data' => PurchaseProduct::query()
                ->select(
                    'purchase_products.code',
                    'purchase_products.product_name',
                    DB::raw('SUM(purchase_products.product_quantity) as total_quantity_sold'),
                    DB::raw('SUM(purchase_products.product_price * purchase_products.product_quantity) as total_sales_amount'),
                )
                ->groupBy('purchase_products.code', 'purchase_products.product_name')
                ->orderByDesc('total_quantity_sold')
                ->get(),
        ]);
    }

    /**
     * Total amount spent per user, highest spender first.
     */
    public function customers(): JsonResponse
    {
        return response()->json([
            'data' => Purchase::query()
                ->join('users', 'purchases.email', '=', 'users.email')
                ->select(
                    'users.email',
                    'users.first_name',
                    'users.last_name',
                    DB::raw('COUNT(DISTINCT purchases.purchase_id) as total_purchases'),
                    DB::raw('SUM(purchases.total_price) as total_amount_spent'),
                )
                ->groupBy('users.email', 'users.first_name', 'users.last_name')
                ->orderByDesc('total_amount_spent')
                ->get(),
        ]);
    }

    /**
     * Purchases per year, highlighting the years that passed 100 purchases.
     */
    public function purchaseTrends(): JsonResponse
    {
        $trends = Purchase::query()
            ->selectRaw('YEAR(purchase_time) as year, COUNT(*) as total_purchases')
            ->groupByRaw('YEAR(purchase_time)')
            ->orderBy('year')
            ->get();

        return response()->json([
            'data' => [
                'purchases_per_year' => $trends,
                'years_with_more_than_100_purchases' => $trends
                    ->where('total_purchases', '>', 100)
                    ->pluck('year')
                    ->values(),
            ],
        ]);
    }

    /**
     * Return one product per manufacturer whose inventory_quantity equals the
     * MAX or MIN inventory_quantity recorded for that manufacturer.
     */
    private function productsPerManufacturerExtreme(string $aggregate)
    {
        $extremes = Product::query()
            ->select('manufacturer', DB::raw("{$aggregate}(inventory_quantity) as extreme_quantity"))
            ->groupBy('manufacturer');

        return Product::query()
            ->joinSub($extremes, 'extremes', function ($join) {
                $join->on('products.manufacturer', '=', 'extremes.manufacturer')
                    ->on('products.inventory_quantity', '=', 'extremes.extreme_quantity');
            })
            ->select('products.*')
            ->orderBy('products.manufacturer')
            ->get()
            ->unique('manufacturer')
            ->values();
    }
}
