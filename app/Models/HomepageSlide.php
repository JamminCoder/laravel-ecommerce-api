<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\HomepageSlideImage;

class HomepageSlide extends Model
{
    use HasFactory;

    public function image() {
        return $this->hasOne(HomepageSlideImage::class);
    }
}
