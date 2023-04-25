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
        Schema::create('mod_images', function (Blueprint $table) {
            $table->id();
            $table->integer('mod_id')->index();
            $table->string('source');
            $table->tinyInteger('adult')->default(0);
            $table->tinyInteger('medical')->default(0);
            $table->tinyInteger('spoof')->default(0);
            $table->tinyInteger('violence')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mod_images');
    }
};
