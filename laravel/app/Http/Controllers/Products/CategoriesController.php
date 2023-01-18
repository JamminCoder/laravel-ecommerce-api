<?php

namespace App\Http\Controllers\Products;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryImage;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoriesController extends Controller
{
    /**
     * Delete category by name.  
     * @param Request $request
     * Request parameters {  
     *      category: string  
     * }
     */
    public static function delete(Request $request) {
        if (!isset($request->category))
            return "Requires a category name to delete";
        
        $category = Category::firstWhere("category", $request->category);

        // Delete products from category
        $products = $category->products()->get();
        foreach($products as $product) {
            $product->delete();
        }

        $category->delete();

        return "Deleted category";
    }


    /**
     * Creates a new category.  
     * @param Request $request  
     * Request parameters {  
     *      category: string,  
     *      description: string,  
     *      image: image file  
     * }
     */
    public static function new(Request $request) {
        $request->validate([
            "category" => "required|unique:categories",
            "description" => "required|max:255",
            "image" => "required|image|mimes:png,jpg,jpeg",
        ]);


        $category = new Category([
            "category" => $request->category,
            "description" => $request->description,
        ]);

        $category->save();

        $image_name = Str::random();
        $image = new CategoryImage(["image_name" => $image_name]);
        $category->image()->save($image);
        $request->image->move("category_images", $image_name);

        return "Created new category";
    }

    /**
     * Updates a category.  
     * @param Request $request  
     * Request parameters {  
     *      target_category: string,  
     *      category: string,  
     *      description: string,  
     * }
     */
    public static function update(Request $request) {
        $request->validate([
            "target_category" => "required",
            "category" => "required",
            "description" => "required|max:255",
        ]);

        // Protect this category being renamed to existing category
        $category_exists = Category::where("category", $request->category)->get()->count();
        if ($request->target_category !== $request->category && $category_exists) 
            return "Cannot use that name";

        
        $category = Category::firstWhere("category", $request->target_category);

        $category->category = $request->category;
        $category->description = $request->description;

        if (isset($request->image)) {
            $image_name = Str::random();
            $image = new CategoryImage(["image_name" => $image_name]);
            
            $old_image = $category->image()->get()->first();
            $old_image->delete();
            
            $category->image()->save($image);
            $request->image->move("category_images", $image_name);
        }
        
        $category->update();

        return "Updated new category";
    }

    /**
     * Gets category data by name.
     * @param Request $request  
     * Request object {  
     *      category: string,  
     * }
     * 
     * @return Category  Category with image and products
     */
    public static function productsFromCategory(Request $request) {
        if (!isset($request->category))
            return "No category set";

        $category = Category::getByName($request->category);
        $category->products = $category->products()->get();
        Product::setImages($category->products);

        $category->image = "category_images/" . $category->imageName();
        return $category;
    }

    /**
     * Gets all categories with product data
     * @param Request $request
     * Request object {  
     *      limit: number || null  
     * }
     * 
     * @return array<Category>
     */
    public static function allCategoriesWithProducts(Request $request) {
        return Category::allWithProducts(limit: isset($request->limit) ? $request->limit: null);
    }

    /**
     * @param Request $request
     * Request object {  
     *      category: string  
     * }
     * 
     * @return array[[  
     *      "name": string,  
     *      "description": string,  
     *      "product_count": string,  
     *      "image" => string,  
     *    ]]
     */
    public static function info(Request $request) {
        if (!isset($request->category))
            return "No category set";

        $category = Category::getByName($request->category);

        return $category->info();
    }

    /**
     * Gets all of the categories' info.
     * @return array[  
     *      "name": string,  
     *      "description": string,  
     *      "product_count": string,  
     *      "image" => string,  
     *    ]
     */
    public static function infoAll() {
        // Returns info about the categories like name and product count
        return Category::infoAll();
    }
}
