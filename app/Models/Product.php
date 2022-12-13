<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductImage;
use App\Models\Category;
use Carbon\Carbon;

class Product extends Model
{
    use HasFactory;
    
    protected $fillable = [
        "category",
        "name",
        "description",
        "price",
        "tax_percent",
        "sku",
        "stock"
    ];

    public function ownImages() {
        return $this->hasMany(ProductImage::class);
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function deleteImages() {
        $images = $this->ownImages()->get();
        foreach ($images as $img) $img->delete();
    }

    public function delete() {
        $this->deleteImages();
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

    public static function setImages(&$product) {
        if (is_iterable($product)) {
            foreach ($product as $prod) {
                $prod->images = $prod->imageNames();
            }
        } else {
            $product->images = $product->imageNames();
        }

        return $product;
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
        $product = Product::where("sku", $sku)->first();
        if ($product) return Product::setImages($product);
        return null;
    }

    public function save(array $options = []) {
        unset($this->images);
        parent::save($options);
    }

    public function getTax() {
        return  $this->price * ($this->tax_percent / 100);
    }

    public function totalPriceCents() {
        $total = $this->price + $this->getTax();
        error_log($this->getTax());
        error_log($total);
        return $total * 100;
    }
}
