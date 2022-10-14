<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductImage;

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
}
