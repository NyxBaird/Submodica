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
        Schema::create('mod_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('mod_id')->index();
            $table->unsignedInteger('user_id')->index();
            $table->unsignedInteger('parent_id')->nullable()->default(null);
            $table->text('comment');
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
        Schema::dropIfExists('mod_comments');
    }
};
