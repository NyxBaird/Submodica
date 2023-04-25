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
        Schema::table('mods', function (Blueprint $table) {
            $table->string('repo_url', 1000)->nullable()->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('donation_link', 1000)->nullable()->after('email');
        });

        Schema::table('mods', function (Blueprint $table) {
            $table->boolean('show_donation_link')->default(false)->after('subnautica_compatibility');
            $table->string('donation_link', 1000)->nullable()->after('subnautica_compatibility');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('donation_link');
        });

        Schema::table('mods', function (Blueprint $table) {
            $table->dropColumn('show_donation_link');
            $table->dropColumn('donation_link');
        });
    }
};
