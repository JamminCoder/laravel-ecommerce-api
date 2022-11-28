<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\HomepageSlideImage;

class HomepageSlide extends Model
{
    use HasFactory;

    public $timestamps = false;

    public $fillable = [
        "header",
        "lead",
        "buttons"
    ];

    public function image() {
        return $this->hasOne(HomepageSlideImage::class);
    }

    public function getImagePath() {
        $image = $this->image()->get()->first();
        return $image->path();
    }

    public function delete() {
        $image = $this->image()->get()->first();
        $image->delete();
        
        parent::delete();
    }
}
