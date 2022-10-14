<?php

namespace App\Http\Controllers\Products;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Product;
use App\Models\ProductImage;

class ProductsController extends Controller
{
    public static function new(Request $request) {
        $request->validate([
            "images" => "required|image|mimes:jpeg,jpg,png|max:2024",
            "catagory" => "required|max:64",
            "name" => "required|unique:products|max:64",
            "description" => "required|max:255",
            "price" => "required",
        ]);

        $name = $request->name;
        $description = $request->description;
        $price = $request->price;

        $sku = Product::generateSKU($name);
        $catagory = $request->catagory;

        return [
            "images" => "Not implemented.",
            "catagory" => $catagory,
            "name" => $name,
            "description" => $description,
            "price" => $price,
            "sku" => $sku,
        ];
    }
}
