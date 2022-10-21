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
        "catagory"
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
                $pro->images = $pro->imageNames();
                $pro->catagory = $catagory_name;
            }

            array_push($catagories_with_products, [
                "name" => $catagory_name,
                "products" => $products
            ]);
        }

        return $catagories_with_products;
    }

    public static function info() {
        $catagories = self::all();
        $info = [];

        foreach ($catagories as $catagory) {
            array_push($info, [
                "catagory" => $catagory->catagory,
                "product_count" => $catagory->products()->count(),
            ]);
        }
        
        return $info;
    }
}
