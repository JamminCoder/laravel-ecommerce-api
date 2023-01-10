<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CabinSection extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $table = "cabin_section";

    protected $fillable = [
        "header",
        "lead",
        "image_path",
        "link_text",
        "href",
    ];
}
