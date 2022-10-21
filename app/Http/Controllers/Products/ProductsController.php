<?php

namespace App\Http\Controllers\Products;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Product;
use App\Models\ProductImage;
use App\Http\Controllers\FilesController;
use App\Models\Catagory;

class ProductsController extends Controller
{
    public static function new(Request $request) {
        $request->validate([
            "images.*" => "required|image|mimes:png,jpg,jpeg",
            "images.*files" => "required|image|mimes:png,jpg,jpeg",
            "catagory" => "required|max:64",
            "name" => "required|unique:products|max:64",
            "description" => "required|max:255",
            "price" => "required",
        ]);

        $name = $request->name;
        $description = $request->description;
        $price = $request->price;
        $sku = Product::generateSKU($name);
        $catagoryName = $request->catagory;


        $product = new Product([
            "name" => $name,
            "description" => $description,
            "price" => $price,
            "sku" => Product::generateSKU($name),
        ]);

        $catagory = Catagory::where("catagory", $catagoryName)
        ->firstOr(
            fn () => new Catagory(["catagory" => $request->catagory])
        );

        $catagory->save();
        $catagory->products()->save($product);
        
        
        $uploadedImageNames = FilesController::uploadFilesFromRequest($request, "images", "product_images");
        self::saveImagesToProduct($product, $uploadedImageNames);
        
        return [
            "images" => $uploadedImageNames,
            "name" => $name,
            "description" => $description,
            "price" => $price,
            "sku" => $sku,
        ];
    }

    public static function update(Request $request) {
        $request->validate([
            "images.*" => "image|mimes:png,jpg,jpeg",
            "images.*files" => "image|mimes:png,jpg,jpeg",
            "catagory" => "max:64",
            "name" => "max:64",
            "description" => "max:255",
            "sku" => "required"
        ]);

        $product = Product::getBySKU($request->sku);
        if (!$product) return "Product does not exist";

        if (isset($request->description)) 
            $product->description = $request->description;

        if (isset($request->name))
            $product->name = $request->name;
        
        if (isset($request->price))
            $product->price = $request->price;
        

        if (isset($request->images)) {
            $product->deleteImages();
            $uploadedImageNames = FilesController::uploadFilesFromRequest($request, "images", "product_images");
            self::saveImagesToProduct($product, $uploadedImageNames);
        }
        
        if (isset($request->catagory)) {
            $catagory = Catagory::where("catagory", $request->catagory)
            ->firstOr(
                fn () => new Catagory(["catagory" => $request->catagory])
            );

            $catagory->save();
            $product->catagory_id = $catagory->id;
        }
        
        $product->save();

        return "Updated product";
    }

    public static function all() {
        return Product::allWithImages();
    }

    public static function delete(Request $request) {
        if (!isset($request->sku)) 
            return "Please provide the SKU at the end of the URL: /api/products/delete/sku/{SKU}";
        
        $product = Product::getBySKU($request->sku);
        $catagory = $product->catagory()->first();
        $product->delete();

        // Delete catagory if empty
        if ($catagory->products()->count() === 0) {
            $catagory->delete();
        }

        return "OK";
        
    }

    public static function getBySKU(Request $request) {
        if (!isset($request->sku)) 
            return "Please provide the SKU at the end of the URL: /api/products/sku/{SKU}";

        $product = Product::where("sku", $request->sku)->first();
        if (!$product) return "Product does not exist";
        return $product->withImages();
    }

    private static function saveImagesToProduct($product, $imageNames) {
        foreach ($imageNames as $imageName) {
            $productImage = new ProductImage([
                "image_name" => $imageName
            ]);

            $product->ownImages()->save($productImage);
        }
    }

    
}
