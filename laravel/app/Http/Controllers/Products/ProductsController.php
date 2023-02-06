<?php

namespace App\Http\Controllers\Products;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Product;
use App\Models\ProductImage;
use App\Http\Controllers\FilesController;
use App\Models\Category;

class ProductsController extends Controller
{
    /**
     * Creates new Product
     * 
     * @param Request $request  
     * Request parameters {  
     *      images: image file(s),  
     *      category: string,  
     *      name: string,  
     *      description: string,  
     *      price: number,  
     *      tax_percent: number,  
     *      stock: number,  
     * }
     */
    public static function new(Request $request) {
        $request->validate([
            "images.*" => "required|image|mimes:png,jpg,jpeg",
            "images.*files" => "required|image|mimes:png,jpg,jpeg",
            "category" => "required|max:64",
            "name" => "required|unique:products|max:64",
            "description" => "required|max:255",
            "price" => "required",
            "tax_percent" => "required",
            "stock" => "required"
        ]);

        $name = $request->name;
        $description = $request->description;
        $price = $request->price;
        $tax_percent = $request->tax_percent;
        $sku = Product::generateSKU($name);
        $stock = $request->stock;
        $categoryName = $request->category;


        $product = new Product([
            "name" => $name,
            "description" => $description,
            "price" => $price,
            "tax_percent" => $tax_percent,
            "sku" => Product::generateSKU($name),
            "stock" => $stock,
        ]);

        $category = Category::firstWhere("category", $categoryName);

        if (!$category) return "Invalid category";

        $category->save();
        $category->products()->save($product);
        
        
        $uploadedImageNames = FilesController::uploadFilesFromRequest($request, "images", "images");
        self::saveImagesToProduct($product, $uploadedImageNames);
        
        return [
            "images" => $uploadedImageNames,
            "name" => $name,
            "description" => $description,
            "price" => $price,
            "tax_percent" => $tax_percent,
            "sku" => $sku,
            "stock" => $stock,
        ];
    }

    /**
     * Updates product using SKU from $request
     * 
     * @param Request $request  
     * Request parameters {  
     *      images: image file(s),  
     *      category: string,  
     *      name: string,  
     *      description: string,    
     *      tax_percent: number,  
     *      stock: number,  
     * } 
     */
    public static function update(Request $request) {
        $request->validate([
            "images.*" => "image|mimes:png,jpg,jpeg",
            "images.*files" => "image|mimes:png,jpg,jpeg",
            "category" => "max:64",
            "name" => "max:64",
            "description" => "max:255",
            "sku" => "required",
            "stock" => "required"
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
            $uploadedImageNames = FilesController::uploadFilesFromRequest($request, "images", "images");
            self::saveImagesToProduct($product, $uploadedImageNames);
        }
        
        if (isset($request->category)) {
            $category = Category::firstWhere("category", $request->category);
            if (!$category) return "Invalid category";

            $category->save();
            $product->category_id = $category->id;
        }

        if (isset($request->tax)) {
            $product->tax_percent = $request->tax_percent;
        }

        $product->stock = $request->stock;

        $product->save();

        return "Updated product";
    }

    /**
     * Delete a product using SKU from request.
     * 
     * @param Request $request  
     * Request parameters {  
     *      sku: string
     * }
     */
    public static function delete(Request $request) {
        if (!isset($request->sku)) 
            return "Please provide the SKU at the end of the URL: /api/products/delete/sku/{SKU}";
        
        $product = Product::getBySKU($request->sku);
        $category = $product->category()->first();
        $product->delete();

        // Delete category if empty
        if ($category->products()->count() === 0) {
            $category->delete();
        }

        return "OK";
        
    }

    /**
     * Get a product using SKU from request.
     * 
     * @param Request $request  
     * Request parameters {  
     *      sku: string  
     * }
     */
    public static function getBySKU(Request $request) {
        if (!isset($request->sku)) 
            return "Please provide the SKU at the end of the URL: /api/products/sku/{SKU}";

        $product = Product::where("sku", $request->sku)->first();
        if (!$product) return "Product does not exist";

        return Product::setImages($product);
    }

    /**
     * Save image names to Product object
     * 
     * @param Product $product
     * @param array $imageNames
     */
    private static function saveImagesToProduct($product, $imageNames) {
        foreach ($imageNames as $imageName) {
            $productImage = new ProductImage([
                "image_name" => $imageName
            ]);

            $product->ownImages()->save($productImage);
        }
    }

    /**
     * Decreases stock of products in $product_skus
     * 
     * @param array $product_skus
     */
    public static function removeStock($product_skus) {
        foreach ($product_skus as $sku) {
            $product = Product::firstWhere("sku", $sku);
            $product->stock -= 1;
            $product->update();
        }
    }
}
