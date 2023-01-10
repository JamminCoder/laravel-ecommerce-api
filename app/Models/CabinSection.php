<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CabinSection extends Model
{
    use HasFactory;

    protected $fillable = [
        "header",
        "lead",
        "image_path",
        "link_text",
        "href",
    ];
}
