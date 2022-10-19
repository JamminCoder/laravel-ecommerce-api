<?php

use App\Models\Catagory;
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
        Schema::create('catagories', function (Blueprint $table) {
            $table->id();
            $table->string("catagory");
        });

        Schema::table("products", function (Blueprint $table) {
            $table->foreignIdFor(Catagory::class);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('catagories');
        Schema::table("products", function (Blueprint $table) {
            $table->dropColumn("catagory_id");
        });
    }
};
