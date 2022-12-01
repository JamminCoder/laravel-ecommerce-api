<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\Products\CategoriesController;
use App\Http\Controllers\Products\ProductsController;
use App\Http\Controllers\Content\HomepageSlideController;
use App\Http\Controllers\SquareController;

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

    // categories
    Route::post("/categories/new", [CategoriesController::class, "new"]);
    Route::post("/categories/update/{category}", [CategoriesController::class, "update"]);
    Route::post("/categories/delete/{category}", [CategoriesController::class, "delete"]);

    // Content management
    Route::post("/content/slides/new", [HomepageSlideController::class, "new"]);
});


/**************
 Public routes
***************/

// Categories
Route::get("/categories/all/products", [CategoriesController::class, "allCategoriesWithProducts"]);
Route::get("/categories/{category}", [CategoriesController::class, "productsFromCategory"]);
Route::get("/categories/{category}/info", [CategoriesController::class, "info"]);
Route::get("/categories/info/all", [CategoriesController::class, "infoAll"]);


// Products
Route::get("/products/sku/{sku}", [ProductsController::class, "getBySKU"]);


// Slides
Route::get("/content/slides/", [HomepageSlideController::class, "all"]);


// Square 
Route::group(["prefix" => "square"], function () {
    Route::get("/order-checkout", [SquareController::class, "orderCheckout"]);
    Route::get("/test", [SquareController::class, "testSquare"]);
    Route::get("/catalog", [SquareController::class, "getCatalog"]);
    Route::get("/catalog/{object_id}", [SquareController::class, "getObject"]);
    Route::get("/catalog/info", [SquareController::class, "getCatalogInfo"]);
});


// Paypal
// Route::post("/orders/create", [OrdersController::class, "new"]);
// Route::post("/orders/{orderID}/capture", [PayPalController::class, "capturePayment"]);
