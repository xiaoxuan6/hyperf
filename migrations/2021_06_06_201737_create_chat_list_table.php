<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateChatListTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chat_lists', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger("oauth_id")->index();
            $table->string("content");
            $table->unsignedInteger("receive_id");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_lists');
    }
}
