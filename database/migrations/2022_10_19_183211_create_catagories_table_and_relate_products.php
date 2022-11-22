<?php

use App\Models\Category;
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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string("category");
            
        });

        Schema::table("products", function (Blueprint $table) {
            $table->foreignIdFor(Category::class)->nullable();
            $table->dropColumn("category");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
        Schema::table("products", function (Blueprint $table) {
            $table->dropColumn("category_id");
            $table->string("category");
        });
    }
};
