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
        Schema::create('user_discord_data', function (Blueprint $table) {
            $table->id();
            $table->string('discord_id', 50);
            $table->string('email', 100);
            $table->string('username', 100);
            $table->string('avatar', 100);
            $table->integer('discriminator');
            $table->integer('public_flags');
            $table->integer('flags');
            $table->string('banner', 100)->nullable();
            $table->string('banner_color', 20)->nullable();
            $table->string('accent_color', 20)->nullable();
            $table->string('locale', 10);
            $table->boolean('verified');
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
        Schema::dropIfExists('user_discord_data');
    }
};
