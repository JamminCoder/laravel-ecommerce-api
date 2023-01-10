<?php

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
        Schema::create('cabin_section', function (Blueprint $table) {
            $table->id();
            $table->string("image_path");
            $table->string("header");
            $table->string("lead")->nullable();
            $table->string("link_text")->default("Rent a Cabin");
            $table->string("href");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('cabin_section');
    }
};
