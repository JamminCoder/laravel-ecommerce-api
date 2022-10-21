<?php

namespace App\Http\Controllers\Products;
use App\Http\Controllers\Controller;
use App\Models\Catagory;
use Illuminate\Http\Request;

class CatagoriesController extends Controller
{
    public static function delete(Request $request) {
        if (!isset($request->name))
            return "Requires a catagory name to delete";
        
        $catagory = Catagory::firstWhere("catagory", $request->name);

        // Delete products from catagory
        $products = $catagory->products()->get();
        foreach($products as $product) {
            $product->delete();
        }

        $catagory->delete();

        return "Deleted catagory";
    }

    public static function allWithProducts() {
        return Catagory::allWithProducts();
    }

    public static function info() {
        // Returns info about the catagories like name and product count
        return Catagory::info();
    }
}
