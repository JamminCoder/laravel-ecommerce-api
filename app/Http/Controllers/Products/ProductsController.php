<?php

namespace App\Http\Controllers\Products;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Product;
use App\Models\ProductImage;
use App\Http\Controllers\FilesController;

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
        $catagory = $request->catagory;


        $product = new Product([
            "name" => $name,
            "description" => $description,
            "price" => $price,
            "sku" => Product::generateSKU($name),
            "catagory" => $catagory,
        ]);

        $product->save(); // Product must be saved before saving images to it.
        
        $uploadedImageNames = FilesController::uploadFilesFromRequest($request, "images", "product_images");
        self::saveImagesToProduct($product, $uploadedImageNames);
        

        return [
            "images" => $uploadedImageNames,
            "catagory" => $catagory,
            "name" => $name,
            "description" => $description,
            "price" => $price,
            "sku" => $sku,
        ];
    }

    public static function all() {
        return Product::allWithImageNames();
    }

    public static function delete(Request $request) {
        $sku = $request->sku;
        $result = Product::where("sku", $sku)->get();
        
        if (count($result) === 0) {
            return "Product does not exist";
        }

        $product = $result[0];
        
        // Delete the images from the file system
        $images = $product->imageNames();
        foreach ($images as $img) {
            $file_path = public_path("product_images/$img");
            if (file_exists($file_path)) {
                error_log("Deleting file $file_path");
                unlink($file_path);
            }
        }
        
        ProductImage::where("product_id", $product->id)->delete();
        $product->delete();

        return "OK";
        
    }

    private static function saveImagesToProduct($product, $imageNames) {
        foreach ($imageNames as $imageName) {
            $productImage = new ProductImage([
                "image_name" => $imageName
            ]);

            $product->images()->save($productImage);
        }
    }

    
}