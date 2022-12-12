<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopHeader extends Model
{
    use HasFactory;

    const IMAGE_DIR = "shop_header_image";
    
    public $table = "shop_header";

    public $timestamps = false;

    public $fillable = [
        "header",
        "lead",
        "image_path"
    ];
}
