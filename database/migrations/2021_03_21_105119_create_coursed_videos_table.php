<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursedVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coursed_videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId("title")->references("id")->on('coursed_titles');
            $table->string("video_path");

            /**
             *
             * STATUS
             *
             * -1: DECLINED BY ADMIN
             *
             * 0: PENDING
             *
             * 1: ACCEPTED
             *
             */
            $table->smallInteger("status");

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
        Schema::dropIfExists('coursed_videos');
    }
}
