<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class Catagory extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "catagory",
        "image",
    ];

    public function products() {
        return $this->hasMany(Product::class);
    }

    public static function allWithProducts() {
        $catagories = Catagory::all();
        $catagories_with_products = [];

        foreach ($catagories as $cat) {
            $products = $cat->products()->get();
            $catagory_name = $cat->catagory;

            foreach ($products as $pro) {
                Product::setImages($pro);
                $pro->catagory = $catagory_name;
            }

            array_push($catagories_with_products, [
                "name" => $catagory_name,
                "products" => $products
            ]);
        }

        return $catagories_with_products;
    }

    public static function getByName($name) {
        $catagory = Catagory::firstWhere("catagory", $name);
        return $catagory;
    }

    public function info() {
        return [
            "catagory" => $this->catagory,
            "product_count" => $this->products()->count(),
            "image" => "catagory_images/$this->image",
        ];
    }

    public static function infoAll() {
        $catagories = self::all();
        $info = [];

        foreach ($catagories as $catagory) {
            array_push($info, [
                "catagory" => $catagory->catagory,
                "image" => "catagory_images/$catagory->image",
                "product_count" => $catagory->products()->count(),
            ]);
        }
        
        return $info;
    }
}
