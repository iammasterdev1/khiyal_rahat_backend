<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursedTitlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coursed_titles', function (Blueprint $table) {
            $table->id();
            $table->foreignId("course_id")->references("id")->on("school_courses");
            $table->string("title");


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
            $table->smallInteger("status")->default(1);


            $table->softDeletes();
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
        Schema::dropIfExists('coursed_titles');
    }
}
