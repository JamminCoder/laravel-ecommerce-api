<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
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
    Route::post("/catagories/delete/{name}", [CatagoriesController::class, "delete"]);
});

Route::get("/products/all", [ProductsController::class, "all"]);
Route::get("/catagories/all/products", [CatagoriesController::class, "allWithProducts"]);
Route::get("/products/sku/{sku}", [ProductsController::class, "getBySKU"]);

