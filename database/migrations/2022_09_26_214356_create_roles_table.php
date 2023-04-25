<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $roles = [
            [
                'name' => 'Member',
                'slug' => 'member',
                'level' => 10
            ],
            [
                'name' => 'Staff',
                'slug' => 'mod',
                'level' => 50
            ],
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'level' => 90
            ],
            [
                'name' => 'Nyx',
                'slug' => 'nyx',
                'level' => 100
            ],
        ];
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string("name", 20);
            $table->string("slug", 20);
            $table->integer("level");
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->integer("role_id")->default(1)->after("github_id")->index();
        });

        foreach ($roles as $role)
            DB::table('roles')->insert([
                'slug' => $role['slug'],
                'name' => $role['name'],
                'level' => $role['level'],
                'created_at' => date("Y-m-d H:i:s", strtotime("now")),
                'updated_at' => date("Y-m-d H:i:s", strtotime("now")),
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn("role_id");
        });
    }
};
