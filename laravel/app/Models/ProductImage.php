<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Http\Controllers\FilesController;

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

    public function path() {
        return "/product_images/" . $this->image_name;
    }

    public function delete() {
        FilesController::deletePublic($this->path());
        return parent::delete();
    }
}
