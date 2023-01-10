<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\NewEmailController;
use App\Http\Controllers\Content\HomepageInfoController;
use App\Http\Controllers\Products\CategoriesController;
use App\Http\Controllers\Products\ProductsController;
use App\Http\Controllers\Content\HomepageSlideController;
use App\Http\Controllers\Content\ShopHeaderController;
use App\Http\Controllers\SquareController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Content\CabinSectionController;

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
    Route::post("/content/slides/edit/{slide_id}", [HomepageSlideController::class, "update"]);
    Route::post("/content/slides/delete/{slide_id}", [HomepageSlideController::class, "delete"]);
    Route::post("/content/shop-header/update", [ShopHeaderController::class, "update"]);

    Route::post("/content/homepage-info/update", [HomepageInfoController::class, "update"]);
    Route::post("/content/cabin-section/update", [CabinSectionController::class, "update"]);


    // Admin account management
    Route::get("/admin/verification-status", [VerifyEmailController::class, "isVerified"]);
    Route::get("/admin/verify-email", [EmailVerificationNotificationController::class, "store"]);
    Route::post("/admin/password-update", [NewPasswordController::class, "update"]);
    Route::post("/admin/email-update", [NewEmailController::class, "update"]);
});


/**************
 Public routes
***************/

// Categories
Route::get("/categories/all", [CategoriesController::class, "allCategoriesWithProducts"]);
Route::get("/categories/{category}", [CategoriesController::class, "productsFromCategory"]);
Route::get("/categories/{category}/info", [CategoriesController::class, "info"]);
Route::get("/categories/info/all", [CategoriesController::class, "infoAll"]);


// Products
Route::get("/products/sku/{sku}", [ProductsController::class, "getBySKU"]);


// Content
Route::get("/content/slides/", [HomepageSlideController::class, "all"]);
Route::get("/content/shop-header", [ShopHeaderController::class, "get"]);
Route::get("/content/homepage-info", [HomepageInfoController::class, "get"]);


// Square 
Route::group(["prefix" => "square"], function () {
    Route::get("/order-checkout", [SquareController::class, "orderCheckout"]);
    Route::post("/order-checkout", [SquareController::class, "orderCheckout"]);

    Route::get("/test", [SquareController::class, "testSquare"]);
});
