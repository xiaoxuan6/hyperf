<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class AddPasswordIntoOauthTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('oauth', function (Blueprint $table) {
            $table->string("password", 32)->default("123")->comment("密码");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('oauth', function (Blueprint $table) {
            $table->dropColumn("password");
        });
    }
}
