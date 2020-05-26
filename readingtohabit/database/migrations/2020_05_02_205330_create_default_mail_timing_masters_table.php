<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDefaultMailTimingMastersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('default_mail_timing_masters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('default_mail_timing_id')->length(20)->unique();
            $table->unsignedInteger('by_day')->length(2)->default(3);
            $table->unsignedInteger('by_week')->length(2)->default(1);
            $table->unsignedInteger('by_month')->length(2)->default(1);
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
        Schema::dropIfExists('default_mail_timing_masters');
    }
}
