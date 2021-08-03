<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string("exam_name");
            $table->foreignId("course_id")->references("id")->on("school_courses");

            /**
             *
             * -2: STILL WAITING TO SUBMIT TO REVIEW
             *
             * -1: REJECTED BY ADMIN
             *
             * 0: PENDING
             *
             * 1: ACCEPTED BY ADMIN
             *
             */
            $table->smallInteger("status")->default("-2");
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
        Schema::dropIfExists('exams');
    }
}
