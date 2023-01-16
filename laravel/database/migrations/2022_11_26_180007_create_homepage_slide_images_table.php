<?php

use App\Models\HomepageSlide;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('homepage_slide_images', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(HomepageSlide::class);
            $table->string("image_name");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('homepage_slide_images');
    }
};
