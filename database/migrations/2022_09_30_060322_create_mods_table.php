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
        Schema::create('mods', function (Blueprint $table) {
            $table->id();
            $table->integer("user_id")->index();
            $table->enum("game", ["sn1", "sbz"]);
            $table->string("creator");
            $table->string("title");
            $table->text("description");
            $table->text("misc_attributions")->nullable();
            $table->bigInteger("repo_id");
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
        Schema::dropIfExists('mods');
    }
};
