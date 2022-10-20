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

            $catagories_with_products[$catagory_name] = $products;
        }

        return $catagories_with_products;
    }
}
