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
        Schema::create('mod_attributes', function (Blueprint $table) {
            $table->id();
            $table->integer('mod_id')->nullable()->index();
            $table->integer('local_attribution_id')->nullable()->index();
            $table->string('name')->nullable();
            $table->string('url', 2048)->nullable(); //2048 is the max length of a browser URL
            $table->timestamps();
        });

        Schema::table("mods", function (Blueprint $table) {
            $table->string('misc_attributions', 1000)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mod_attributes');

        Schema::table("mods", function (Blueprint $table) {
            $table->text("misc_attributions")->nullable();
        });
    }
};
