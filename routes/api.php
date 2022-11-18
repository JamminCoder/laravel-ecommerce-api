<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\Products\CatagoriesController;
use App\Http\Controllers\Products\ProductsController;


Route::post("/login", [AuthenticatedSessionController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/test-auth', function (Request $request) {
        return "Congrats! You are authorized!";
    });

    Route::post("/logout", [AuthenticatedSessionController::class, 'destroy']);


    // Product management
    Route::post("/products/new", [ProductsController::class, "new"]);
    Route::post("/products/update", [ProductsController::class, "update"]);
    Route::post("/products/delete/sku/{sku}", [ProductsController::class, "delete"]);

    // catagories
    Route::post("/catagories/delete/{name}", [CatagoriesController::class, "delete"]);
    Route::post("/catagories/new", [CatagoriesController::class, "new"]);
});


/**************
 Public routes
***************/

// Catagories
Route::get("/catagories/all/products", [CatagoriesController::class, "allCatagoriesWithProducts"]);
Route::get("/catagories/{catagory}", [CatagoriesController::class, "productsFromCatagory"]);
Route::get("/catagories/{catagory}/info", [CatagoriesController::class, "info"]);
Route::get("/catagories/info/all", [CatagoriesController::class, "infoAll"]);


// Products
Route::get("/products/sku/{sku}", [ProductsController::class, "getBySKU"]);


// Paypal
Route::post("/orders/create", [OrdersController::class, "new"]);
Route::post("/orders/{orderID}/capture", [PayPalController::class, "capturePayment"]);
