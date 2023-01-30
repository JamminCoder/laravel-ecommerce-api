<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AboutPage extends Model
{
    use HasFactory;
    public $table = "about_page";
    public $timestamps = false;


    public $fillable = [
        "header",
        "body"
    ];
}
