<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\HomepageSlide;

class HomepageSlideImage extends Model
{
    use HasFactory;

    public function slide() {
        return $this->belongsTo(HomepageSlide::class);
    }
}
