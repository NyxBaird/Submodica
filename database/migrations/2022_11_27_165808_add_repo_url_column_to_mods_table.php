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
        //Changing this to 1000 characters in CreateDonationUrlColumns migration
        Schema::table('mods', function (Blueprint $table) {
            $table->string('repo_url', 255)->nullable()->after('repo_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mods', function (Blueprint $table) {
            $table->dropColumn('repo_url');
        });
    }
};
