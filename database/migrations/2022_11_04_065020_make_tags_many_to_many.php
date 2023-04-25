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
        Schema::dropIfExists('mod_tags');

        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string("tag", 50)->index();
            $table->timestamps();
        });

        Schema::create('mod_tags', function (Blueprint $table) {
            $table->integer("mod_id")->index();
            $table->integer("tag_id")->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tags');
        Schema::dropIfExists('mod_tags');
    }
};
