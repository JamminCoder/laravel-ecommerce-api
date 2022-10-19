<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductImage;
use Carbon\Carbon;
use App\Http\Controllers\FilesController;

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

    public function ownImages() {
        return $this->hasMany(ProductImage::class);
    }

    public function delete() {
        // Delete the images from the file system
        $images = $this->ownImages()->get();
        foreach ($images as $img) {
            FilesController::delete($img->url());
        }
        
        ProductImage::where("product_id", $this->id)->delete();

        return parent::delete();
    }

    public function imageNames() {
        $images = $this->ownImages()->get();
        $image_names = array();
        foreach ($images as $img) {
            array_push($image_names, $img->image_name);
        }

        return $image_names;
    }

    public function withImages() {
        $this->images = $this->imageNames();
        return $this;
    }

    public static function allWithImageNames() {
        $products = Product::all();

        if (!count($products) >= 1) return "No products";
        
        $products_with_images = array();

        foreach ($products as $product) {
            $product["images"] = $product->imageNames();
            array_push($products_with_images, $product);
        }

        return $products_with_images;
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

    public static function getBySKU($sku) {
        $result = Product::where("sku", $sku)->get();
        if (count($result) >= 1) return $result[0];
        return null;
    }
}
