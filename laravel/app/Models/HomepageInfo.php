<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomepageInfo extends Model
{
    use HasFactory;

    public $timestamps = false;
    public $table = "homepage_info";

    public $fillable = [
        "header",
        "lead"
    ];
}
