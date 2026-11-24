<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Product CRUD
    |--------------------------------------------------------------------------
    */

    // A. Get all the columns from the product table.
    public function index(): JsonResponse
    {
        return response()->json(['data' => Product::all()]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:12', 'unique:products,code'],
            'name' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string'],
            'manufacturer' => ['required', 'string', 'max:100'],
            'photo' => ['required', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'between:0,99999.99'],
            'cost' => ['required', 'numeric', 'between:0,99999.99'],
            'inventory_quantity' => ['required', 'integer', 'min:0'],
        ]);

        $product = Product::create($validated);

        return response()->json([
            'message' => 'Product created successfully.',
            'data' => $product,
        ], 201);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json(['data' => $product]);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['sometimes', 'string', 'max:12', Rule::unique('products', 'code')->ignore($product->code, 'code')],
            'name' => ['sometimes', 'string', 'max:100'],
            'description' => ['sometimes', 'string'],
            'manufacturer' => ['sometimes', 'string', 'max:100'],
            'photo' => ['sometimes', 'string', 'max:1000'],
            'price' => ['sometimes', 'numeric', 'between:0,99999.99'],
            'cost' => ['sometimes', 'numeric', 'between:0,99999.99'],
            'inventory_quantity' => ['sometimes', 'integer', 'min:0'],
        ]);

        $product->update($validated);

        return response()->json([
            'message' => 'Product updated successfully.',
            'data' => $product->fresh(),
        ]);
    }

    public function destroy(Product $product): JsonResponse
    {
        if ($product->purchases()->exists()) {
            return response()->json([
                'message' => 'Product cannot be deleted because it is referenced by purchases.',
            ], 409);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully.']);
    }

    /*
    |--------------------------------------------------------------------------
    | A. Querying tables
    |--------------------------------------------------------------------------
    */

    // A. Get all the manufacturers from the product table.
    public function manufacturers(): JsonResponse
    {
        return response()->json([
            'data' => Product::query()->select('manufacturer')->get(),
        ]);
    }

    // A. Get the manufacturer and name columns from the product table.
    public function manufacturerAndName(): JsonResponse
    {
        return response()->json([
            'data' => Product::query()->select('manufacturer', 'name')->get(),
        ]);
    }

    // A. Get the product code and manufacturer, ordered by inventory_quantity ascending.
    public function orderedByInventoryAsc(): JsonResponse
    {
        return response()->json([
            'data' => Product::query()
                ->select('code', 'manufacturer')
                ->orderBy('inventory_quantity')
                ->get(),
        ]);
    }

    // A. Get the product code and manufacturer, ordered by inventory_quantity descending.
    public function orderedByInventoryDesc(): JsonResponse
    {
        return response()->json([
            'data' => Product::query()
                ->select('code', 'manufacturer')
                ->orderByDesc('inventory_quantity')
                ->get(),
        ]);
    }

    // A. Get the first 5 rows from the product table.
    public function firstFive(): JsonResponse
    {
        return response()->json([
            'data' => Product::query()->limit(5)->get(),
        ]);
    }

    // A. Get a unique list of manufacturers that have products.
    public function uniqueManufacturers(): JsonResponse
    {
        return response()->json([
            'data' => Product::query()->distinct()->orderBy('manufacturer')->pluck('manufacturer'),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | B. Filtering data (products)
    |--------------------------------------------------------------------------
    */

    // B. Get all the products where cost is more than or equal to 50.
    public function costAtLeastFifty(): JsonResponse
    {
        return response()->json([
            'data' => Product::query()->where('cost', '>=', 50)->get(),
        ]);
    }

    // B. Get all the products where price is lower than 20.
    public function priceBelowTwenty(): JsonResponse
    {
        return response()->json([
            'data' => Product::query()->where('price', '<', 20)->get(),
        ]);
    }

    // B. Get all the products with a price of 10 to 50.
    public function priceBetweenTenAndFifty(): JsonResponse
    {
        return response()->json([
            'data' => Product::query()->whereBetween('price', [10, 50])->get(),
        ]);
    }

    // B. Get all the products made by the manufacturer 'Apple'.
    public function madeByApple(): JsonResponse
    {
        return response()->json([
            'data' => Product::query()->where('manufacturer', 'Apple')->get(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | C. Aggregating data
    |--------------------------------------------------------------------------
    */

    // C. Get the total inventory_quantity across all products.
    public function totalInventory(): JsonResponse
    {
        return response()->json([
            'data' => ['total_inventory_quantity' => (int) Product::query()->sum('inventory_quantity')],
        ]);
    }

    // C. Get the average inventory_quantity per product across all products.
    public function averageInventory(): JsonResponse
    {
        return response()->json([
            'data' => ['average_inventory_quantity' => round((float) Product::query()->avg('inventory_quantity'), 2)],
        ]);
    }

    // C. Get the product with the highest inventory_quantity.
    public function highestInventory(): JsonResponse
    {
        return response()->json([
            'data' => Product::query()->orderByDesc('inventory_quantity')->first(),
        ]);
    }

    // C. Get the product with the lowest inventory_quantity.
    public function lowestInventory(): JsonResponse
    {
        return response()->json([
            'data' => Product::query()->orderBy('inventory_quantity')->first(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | D. Grouping, filtering, and sorting
    |--------------------------------------------------------------------------
    */

    // D. Get the total inventory_quantity for each manufacturer.
    public function inventoryPerManufacturer(): JsonResponse
    {
        return response()->json([
            'data' => Product::query()
                ->select('manufacturer', DB::raw('SUM(inventory_quantity) as total_inventory_quantity'))
                ->groupBy('manufacturer')
                ->orderBy('manufacturer')
                ->get(),
        ]);
    }

    // D. Get the average inventory_quantity for each manufacturer.
    public function averageInventoryPerManufacturer(): JsonResponse
    {
        return response()->json([
            'data' => Product::query()
                ->select('manufacturer', DB::raw('AVG(inventory_quantity) as average_inventory_quantity'))
                ->groupBy('manufacturer')
                ->orderBy('manufacturer')
                ->get(),
        ]);
    }

    // D. Get the product with the highest inventory_quantity per manufacturer.
    public function highestInventoryPerManufacturer(): JsonResponse
    {
        return response()->json([
            'data' => $this->productsPerManufacturerExtreme('MAX'),
        ]);
    }

    // D. Get the product with the lowest inventory_quantity per manufacturer.
    public function lowestInventoryPerManufacturer(): JsonResponse
    {
        return response()->json([
            'data' => $this->productsPerManufacturerExtreme('MIN'),
        ]);
    }

    // D. For each manufacturer, get the average inventory_quantity per product, sorted ascending.
    public function averagePerProductAscending(): JsonResponse
    {
        return response()->json([
            'data' => Product::query()
                ->select('manufacturer', DB::raw('AVG(inventory_quantity) as average_inventory_quantity'))
                ->groupBy('manufacturer')
                ->orderBy('average_inventory_quantity')
                ->get(),
        ]);
    }

    // D. For 'Sony' and 'Apple', get the average inventory_quantity per product for each manufacturer.
    public function averagePerProductForSonyAndApple(): JsonResponse
    {
        return response()->json([
            'data' => Product::query()
                ->select('manufacturer', DB::raw('AVG(inventory_quantity) as average_inventory_quantity'))
                ->whereIn('manufacturer', ['Sony', 'Apple'])
                ->groupBy('manufacturer')
                ->orderBy('manufacturer')
                ->get(),
        ]);
    }

    // D. Get the number of distinct product names per manufacturer.
    public function distinctNamesPerManufacturer(): JsonResponse
    {
        return response()->json([
            'data' => Product::query()
                ->select('manufacturer', DB::raw('COUNT(DISTINCT name) as distinct_product_names'))
                ->groupBy('manufacturer')
                ->orderBy('manufacturer')
                ->get(),
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
