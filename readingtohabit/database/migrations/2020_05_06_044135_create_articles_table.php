<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->length(20);
            $table->string('bookimg', '255')->default('/img/no_image.jpg');
            $table->string('bookname', '255');
            $table->string('author', '255');
            $table->text('learning');
            $table->text('action');
            $table->unsignedTinyInteger('mail')->length(1)->default(1);
            $table->unsignedTinyInteger('favorite')->length(1)->default(0);
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
        Schema::dropIfExists('articles');
    }
}
