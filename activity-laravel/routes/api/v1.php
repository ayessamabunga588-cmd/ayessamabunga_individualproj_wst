<?php

use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\PurchaseController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    /*
    |--------------------------------------------------------------------------
    | Query endpoints
    |--------------------------------------------------------------------------
    | These fixed paths are declared before Route::apiResource so that routes
    | such as /products/manufacturers are not captured by /products/{product}.
    */

    Route::prefix('products')->group(function () {
        // A. Querying tables
        Route::get('manufacturers', [ProductController::class, 'manufacturers']);
        Route::get('manufacturer-and-name', [ProductController::class, 'manufacturerAndName']);
        Route::get('by-inventory-asc', [ProductController::class, 'orderedByInventoryAsc']);
        Route::get('by-inventory-desc', [ProductController::class, 'orderedByInventoryDesc']);
        Route::get('first-five', [ProductController::class, 'firstFive']);
        Route::get('unique-manufacturers', [ProductController::class, 'uniqueManufacturers']);

        // B. Filtering data
        Route::get('cost-at-least-50', [ProductController::class, 'costAtLeastFifty']);
        Route::get('price-below-20', [ProductController::class, 'priceBelowTwenty']);
        Route::get('price-between-10-and-50', [ProductController::class, 'priceBetweenTenAndFifty']);
        Route::get('made-by-apple', [ProductController::class, 'madeByApple']);

        // C. Aggregating data
        Route::get('total-inventory', [ProductController::class, 'totalInventory']);
        Route::get('average-inventory', [ProductController::class, 'averageInventory']);
        Route::get('highest-inventory', [ProductController::class, 'highestInventory']);
        Route::get('lowest-inventory', [ProductController::class, 'lowestInventory']);

        // D. Grouping, filtering, and sorting
        Route::get('inventory-per-manufacturer', [ProductController::class, 'inventoryPerManufacturer']);
        Route::get('average-inventory-per-manufacturer', [ProductController::class, 'averageInventoryPerManufacturer']);
        Route::get('highest-inventory-per-manufacturer', [ProductController::class, 'highestInventoryPerManufacturer']);
        Route::get('lowest-inventory-per-manufacturer', [ProductController::class, 'lowestInventoryPerManufacturer']);
        Route::get('average-per-product-ascending', [ProductController::class, 'averagePerProductAscending']);
        Route::get('average-per-product-sony-apple', [ProductController::class, 'averagePerProductForSonyAndApple']);
        Route::get('distinct-names-per-manufacturer', [ProductController::class, 'distinctNamesPerManufacturer']);
    });

    Route::prefix('purchases')->group(function () {
        // B. Filtering data
        Route::get('total-price-at-least-100', [PurchaseController::class, 'totalPriceAtLeastHundred']);
        Route::get('by-user/{email}', [PurchaseController::class, 'byUser']);
        Route::get('low-quantity-items', [PurchaseController::class, 'lowQuantityItems']);

        // D. Years with more than 100 purchases
        Route::get('years-with-over-100', [PurchaseController::class, 'yearsWithOverHundredPurchases']);

        // E. Joining the 4 tables
        Route::get('with-buyer-and-products', [PurchaseController::class, 'withBuyerAndProducts']);
        Route::get('quantity-sold-per-product', [PurchaseController::class, 'quantitySoldPerProduct']);
        Route::get('amount-spent-per-user', [PurchaseController::class, 'amountSpentPerUser']);
    });

    /*
    |--------------------------------------------------------------------------
    | CRUD resources
    |--------------------------------------------------------------------------
    */

    Route::apiResource('products', ProductController::class);
    Route::apiResource('purchases', PurchaseController::class);

    /*
    |--------------------------------------------------------------------------
    | Dashboard analytics
    |--------------------------------------------------------------------------
    */

    Route::prefix('dashboard')->group(function () {
        Route::get('summary', [DashboardController::class, 'summary']);
        Route::get('inventory', [DashboardController::class, 'inventory']);
        Route::get('manufacturers', [DashboardController::class, 'manufacturers']);
        Route::get('sales', [DashboardController::class, 'sales']);
        Route::get('customers', [DashboardController::class, 'customers']);
        Route::get('purchase-trends', [DashboardController::class, 'purchaseTrends']);
    });
});
