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
        Schema::create('user_github_data', function (Blueprint $table) {
            $table->id();
            $table->string("login");
            $table->integer("github_id")->index();
            $table->string("node_id");
            $table->string("avatar_url");
            $table->string("gravatar_id");
            $table->string("url");
            $table->string("html_url");
            $table->string("followers_url");
            $table->string("following_url");
            $table->string("gists_url");
            $table->string("starred_url");
            $table->string("subscriptions_url");
            $table->string("organizations_url");
            $table->string("repos_url");
            $table->string("events_url");
            $table->string("received_events_url");
            $table->string("type");
            $table->string("email")->nullable();
            $table->string("twitter_username")->nullable();
            $table->integer("public_repos");
            $table->integer("public_gists");
            $table->integer("followers");
            $table->integer("following");
            $table->timestamps();
        });

        Schema::table("users", function (Blueprint $table){
            $table->dropColumn("discord_id");
        });
        Schema::table("users", function (Blueprint $table){
            $table->bigInteger("github_id")->after("id")->nullable()->index();
            $table->bigInteger("discord_id")->after("id")->nullable()->index();
        });

        Schema::table("user_discord_data", function (Blueprint $table){
            $table->dropColumn("discord_id");
        });
        Schema::table("user_discord_data", function (Blueprint $table){
            $table->bigInteger('discord_id')->after("id")->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_github_data');

        Schema::table("users", function (Blueprint $table){
            $table->removeColumn("github_id");
        });

        Schema::table("user_discord_data", function (Blueprint $table){
            $table->string('discord_id', 50)->change();
        });
    }
};
