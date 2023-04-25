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
        Schema::table('roles', function (Blueprint $table) {
            $table->string('color', 7)->nullable()->after('level');
        });

        $roles = \App\Models\Role::all()->each(function ($role) {
            switch ($role->slug) {
                case 'nyx':
                    $role->color = '#570202';
                    break;
                case 'admin':
                    $role->color = '#ffcc00';
                    break;
                case 'mod':
                    $role->color = '#338bff';
                    break;
                default:
                    return;
            }

            $role->save();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('color');
        });
    }
};
