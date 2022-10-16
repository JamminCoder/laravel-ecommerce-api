<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class ProductImage extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "image_name",
    ];

    public function product() {
        return $this->belongsTo(Product::class);
    }

    private function url() {
        return "/product_images/" . $this->name;
    }
}
