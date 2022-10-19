<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class Catagory extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "catagory"
    ];

    public function products() {
        return $this->hasMany(Product::class);
    }
}
