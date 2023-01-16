<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Http\Controllers\FilesController;

class CategoryImage extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "image_name",
    ];

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function path() {
        return "/category_images/" . $this->image_name;
    }

    public function delete() {
        FilesController::deletePublic($this->path());
        return parent::delete();
    }
}
