<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public static function new(Request $request) {
        error_log($request->name);
        error_log($request->address_street);
        error_log($request->address_city);
        error_log($request->address_state);
        error_log($request->address_zip);
        
        error_log($request->product_skus);

        return "OK";
    }
}
