<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CabinSection extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $table = "cabin_section_iframe";

    protected $fillable = [
        "iframe_url"
    ];
}
