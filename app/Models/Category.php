<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class Category extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "category",
        "description",
    ];

    public function products() {
        return $this->hasMany(Product::class);
    }

    public function image() {
        return $this->hasOne(CategoryImage::class);
    }

    public static function allWithProducts() {
        $categories = Category::all();
        $categories_with_products = [];

        foreach ($categories as $cat) {
            $products = $cat->products()->get();
            $category_name = $cat->category;
            $category_description = $cat->description;

            foreach ($products as $pro) {
                Product::setImages($pro);
                $pro->category = $category_name;
            }

            array_push($categories_with_products, [
                "name" => $category_name,
                "description" => $category_description,
                "products" => $products
            ]);
        }

        return $categories_with_products;
    }

    public static function getByName($name) {
        $category = Category::firstWhere("category", $name);
        return $category;
    }

    public function info() {
        return [
            "category" => $this->category,
            "product_count" => $this->products()->count(),
            "image" => "category_images/$this->image",
        ];
    }

    public static function infoAll() {
        $categories = self::all();
        $info = [];

        foreach ($categories as $category) {
            array_push($info, [
                "category" => $category->category,
                "description" => $category->description,
                "image" => "category_images/$category->image",
                "product_count" => $category->products()->count(),
            ]);
        }
        
        return $info;
    }
}
