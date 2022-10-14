<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductImage;
use Carbon\Carbon;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        "catagory",
        "name",
        "description",
        "price",
        "sku",
    ];

    public function images() {
        return $this->hasMany(ProductImage::class);
    }

    public static function generateSKU($productName) {
        // separate by spaces
        $exploded = explode(" ", $productName);
        $SKU = "";
        foreach ($exploded as $piece) {
            if ($piece === "") return;

            $SKU .= strtoupper($piece) . "-";
        }
        
        $time = Carbon::now()->toDateString();
        $SKU .= $time;

        return $SKU;
    }
}
