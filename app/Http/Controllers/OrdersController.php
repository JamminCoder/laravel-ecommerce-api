<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Controllers\PayPalController;

class OrdersController extends Controller
{
    public static function new(Request $request) {
        error_log($request->name);
        error_log($request->address_street);
        error_log($request->address_city);
        error_log($request->address_state);
        error_log($request->address_zip);
        error_log($request->product_skus);

        $skus = explode(", ", $request->product_skus);

        $totalPrice = self::calcTotalPrice($skus);
        self::removeStock($skus);
        
        $orderData = PayPalController::createOrder($totalPrice);

        return $orderData;
    }

    private static function calcTotalPrice($product_skus) {
        $total = 0;
        foreach ($product_skus as $sku) {
            $product = Product::firstWhere("sku", $sku);
            $total += $product->price;
        }

        return $total;
    }

    private static function removeStock($product_skus) {
        foreach ($product_skus as $sku) {
            $product = Product::firstWhere("sku", $sku);
            $product->stock -= 1;
            $product->update();
        }
    }
}
