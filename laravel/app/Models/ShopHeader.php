<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopHeader extends Model
{
    use HasFactory;

    const IMAGE_DIR = "images";
    
    public $table = "shop_header";

    public $timestamps = false;

    public $fillable = [
        "header",
        "lead",
        "image_path"
    ];
}
