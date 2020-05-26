<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAutoLoginTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auto_login_tokens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->length(20);
            $table->string('token')->unique();
            $table->timestamp('expires');
            $table->timestamps();
            $table->unsignedTinyInteger('deleted')->length(1)->default(0);
            $table->timestamp('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('auto_login_tokens');
    }
}
