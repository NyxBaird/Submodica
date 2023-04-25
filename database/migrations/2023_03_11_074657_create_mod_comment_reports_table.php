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
        Schema::create('mod_comment_reports', function (Blueprint $table) {
            $table->id();
            $table->integer('reporter_id')->index();
            $table->integer('comment_id')->index();
            $table->string('reason', 20);
            $table->string('details', 510);
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
        Schema::dropIfExists('mod_comment_reports');
    }
};
