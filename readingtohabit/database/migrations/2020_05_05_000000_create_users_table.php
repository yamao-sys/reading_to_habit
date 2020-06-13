<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('profile_img', '255')->default(\ImgPathConst::NOIMG_PATH);
            $table->rememberToken();
            $table->timestamps();
            $table->unsignedTinyInteger('deleted')->length(1)->default(0);
            $table->timestamp('deleted_at')->nullable();
        });
        DB::statement('alter table users modify email varchar(256) binary');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
