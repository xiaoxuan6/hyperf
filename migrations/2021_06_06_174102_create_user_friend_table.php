<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateUserFriendTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_friends', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger("oauth_id")->comment("用户1");
            $table->unsignedInteger("user_id")->comment("用户2");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_friends');
    }
}
