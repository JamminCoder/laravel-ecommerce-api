<?php

namespace App\Http\Controllers\Products;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Str;

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
        
        $uploadedImageNames = self::uploadImagesFromRequest($request, "images", "product_images");
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

    private static function saveImagesToProduct($product, $imageNames) {
        foreach ($imageNames as $imageName) {
            $productImage = new ProductImage([
                "image_name" => $imageName
            ]);

            $product->images()->save($productImage);
        }
    }

    private static function uploadImagesFromRequest($request, $fieldName, $outputDir) {
        // Upload code from by https://stackoverflow.com/a/42643349
        
        $images = array();
        if($files = $request->file($fieldName)) {
            foreach($files as $file){
                $name = Str::random();
                $file->move($outputDir, $name);
                $images[] = $name;
            }
        }

        return $images;
    }
}
