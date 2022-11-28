<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\HomepageSlide;
use App\Http\Controllers\FilesController;

class HomepageSlideImage extends Model
{
    use HasFactory;

    public $timestamps = false;

    public $fillable = [
        "image_name"
    ];

    public function slide() {
        return $this->belongsTo(HomepageSlide::class);
    }

    public function path() {
        return "/slide_images/" . $this->image_name;
    }

    public function delete() {
        FilesController::deletePublic($this->path());
        return parent::delete();
    }
}
