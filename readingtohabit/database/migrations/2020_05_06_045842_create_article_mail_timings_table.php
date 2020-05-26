<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticleMailTimingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_mail_timings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('article_id')->length(20)->unique();
            $table->date('last_send_date')->nullable();
            $table->date('next_send_date')->nullable();
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
        Schema::dropIfExists('article_mail_timings');
    }
}
