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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('banned')->default(false)->after('remember_token')->change();
        });

        Schema::create('mod_statistics_views_unique', function (Blueprint $table) {
            $table->id();
            $table->integer('mod_id')->unsigned()->index();
            $table->integer('user_id')->unsigned()->index();
            $table->timestamps();
        });

        Schema::create('mod_statistics_downloads_unique', function (Blueprint $table) {
            $table->id();
            $table->integer('mod_id')->unsigned()->index();
            $table->integer('user_id')->unsigned()->index();
            $table->string('version');
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
        Schema::dropIfExists('mod_statistics_views_unique');
        Schema::dropIfExists('mod_statistics_downloads_unique');
    }
};
