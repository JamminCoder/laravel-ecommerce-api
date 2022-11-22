<?php

namespace App\Http\Controllers\Products;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoriesController extends Controller
{
    public static function delete(Request $request) {
        if (!isset($request->name))
            return "Requires a category name to delete";
        
        $category = Category::firstWhere("category", $request->name);

        // Delete products from category
        $products = $category->products()->get();
        foreach($products as $product) {
            $product->delete();
        }

        $category->delete();

        return "Deleted category";
    }

    public static function new(Request $request) {
        $request->validate([
            "category" => "required|unique:categories",
            "description" => "required|max:255",
            "image" => "required|image|mimes:png,jpg,jpeg",
        ]);

        $imageName = Str::random();
        $request->image->move("category_images", $imageName);

        $category = new Category([
            "category" => $request->category,
            "description" => $request->description,
            "image" => $imageName,
        ]);

        $category->save();

        return "Created new category";
    }

    public static function update(Request $request) {
        $request->validate([
            "category" => "required|unique:categories",
            "description" => "required|max:255",
        ]);

        $category = new Category([
            "category" => $request->category,
            "description" => $request->description,
        ]);

        if (isset($request->image)) {
            $imageName = Str::random();
            $request->image->move("category_images", $imageName);
            $category->image = $imageName;
        }
        
        $category->update();

        return "Updated new category";
    }




    public static function productsFromCategory(Request $request) {
        if (!isset($request->category))
            return "No category set";

        $category = Category::getByName($request->category);
        $category->products = $category->products()->get();
        Product::setImages($category->products);
        return $category;
    }

    public static function allCategoriesWithProducts() {
        return Category::allWithProducts();
    }

    public static function info(Request $request) {
        if (!isset($request->category))
            return "No category set";

        $category = Category::getByName($request->category);

        return $category->info();
    }

    public static function infoAll() {
        // Returns info about the categories like name and product count
        return Category::infoAll();
    }
}
