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

        $catResult = Catagory::where("catagory", $catagoryName)->get();
        if (count($catResult) >= 1) {
            $catagory = $catResult[0];
            $catagory->products()->save($product);

        } else {
            $catagory = new Catagory([
                "catagory" => $catagoryName,
            ]);

            $catagory->products()->save($product);
            $catagory->save();
        }
        
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
            $catResult = Catagory::where("catagory", $request->catagory)->get();
            if (count($catResult) >= 1) {
                $catagory = $catResult[0];
                $catagory->products()->save($product);
    
            } else {
                $catagory = new Catagory([
                    "catagory" => $request->catagory,
                ]);
    
                $catagory->save();
                $product->catagory_id = $catagory->id;
            
            }
        }
        
        $product->save();

        return "Updated product";
    }

    public static function all() {
        return Product::allWithImageNames();
    }

    public static function allCatagories() {
        $catagories = Catagory::all();
        $res_data = [];

        foreach ($catagories as $cat) {
            $products = $cat->products()->get();
            
            foreach ($products as $pro) {
                $pro->images = $pro->imageNames();
            }

            $res_data[$cat->catagory] = $products;
        }

        return $res_data;
    }

    public static function delete(Request $request) {
        if (!isset($request->sku)) 
            return "Please provide the SKU at the end of the URL: /api/products/delete/sku/{SKU}";
        
        $product = Product::getBySKU($request->sku);
        $product->delete();

        return "OK";
        
    }

    public static function getBySKU(Request $request) {
        if (!isset($request->sku)) 
            return "Please provide the SKU at the end of the URL: /api/products/sku/{SKU}";
        
        $sku = $request->sku;
        
        $result = Product::where("sku", $sku)->get();
        if (count($result) === 0) return null;
        $product = $result[0];
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
