<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\Products\CatagoriesController;
use App\Http\Controllers\Products\ProductsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

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

Route::get("/catagories/all/products", [CatagoriesController::class, "allWithProducts"]);
Route::get("/catagories/{catagory}", [CatagoriesController::class, "getWithProducts"]);
Route::get("/catagories/{catagory}/info", [CatagoriesController::class, "info"]);

Route::get("/catagories/info/all", [CatagoriesController::class, "allInfo"]);
Route::get("/products/sku/{sku}", [ProductsController::class, "getBySKU"]);

// Paypal
Route::get("/paypal/orders/create", [PayPalController::class, "createOrder"]);

Route::get("/paypal/orders/{orderID}/capture", [PayPalController::class, "capturePayment"]);

Route::get("/paypal/client-token", [PayPalController::class, "generateClientToken"]);
Route::get("/paypal/access-token", [PayPalController::class, "generateAccessToken"]);

Route::get("/paypal/id", [PayPalController::class, "identity"]);