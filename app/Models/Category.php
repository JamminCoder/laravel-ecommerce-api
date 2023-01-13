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

    public function imageName() {
        $image = $this->image()->get()->first();
        return $image->image_name;
    }

    public static function allWithProducts($limit=null) {
        $categories = Category::all();
        $categories_with_products = [];

        $i = 1;
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
                "image" => "category_images/" . $cat->imageName(),
                "products" => $products
            ]);

            if ($limit && $i >= $limit) break;
            $i++;
        }

        return $categories_with_products;
    }

    public static function getByName($name) {
        $category = Category::firstWhere("category", $name);
        
        foreach ($category->products as $pro) {
            Product::setImages($pro);
            $pro->category = $category->category;
        }

        return $category;
    }

    public function info() {
        return [
            "name" => $this->category,
            "description" => $this->description,
            "product_count" => $this->products()->count(),
            "image" => "category_images/" . $this->imageName(),
        ];
    }

    public static function infoAll() {
        $categories = self::all();
        $info = [];

        foreach ($categories as $category) {
            array_push($info, [
                "category" => $category->category,
                "description" => $category->description,
                "image" => "category_images/" . $category->imageName(),
                "product_count" => $category->products()->count(),
            ]);
        }
        
        return $info;
    }

    public function delete() {
        $image = $this->image()->get()->first();
        $image->delete();

        $products = $this->products()->get();
        foreach ($products as $product) {
            $product->delete();
        }
        
        parent::delete();
    }
}
