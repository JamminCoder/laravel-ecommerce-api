<?php

namespace App\Http\Controllers\Products;
use App\Http\Controllers\Controller;
use App\Models\Catagory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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

    public static function new(Request $request) {
        $request->validate([
            "catagory" => "required|unique:catagories",
            "image" => "required|image|mimes:png,jpg,jpeg",
        ]);

        $imageName = Str::random();
        $request->image->move("catagory_images", $imageName);

        $catagory = new Catagory([
            "catagory" => $request->catagory,
            "image" => $imageName,
        ]);

        $catagory->save();

        return "Created new catagory";
    }

    public static function getWithProducts(Request $request) {
        if (!isset($request->catagory))
            return "No catagory set";

        $catagory = Catagory::getByName($request->catagory);
        $catagory->products = $catagory->products()->get();
        return $catagory;
    }

    public static function allWithProducts() {
        return Catagory::allWithProducts();
    }

    public static function info(Request $request) {
        if (!isset($request->catagory))
            return "No catagory set";

        $catagory = Catagory::getByName($request->catagory);

        return $catagory->info();
    }

    public static function allInfo() {
        // Returns info about the catagories like name and product count
        return Catagory::allInfo();
    }
}
