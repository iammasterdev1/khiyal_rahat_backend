<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchoolCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school_courses', function (Blueprint $table) {
            $table->id();
            $table->string("course_name");
            $table->text("course_description");
            $table->bigInteger("price_irr")->unsigned();
            $table->bigInteger('irr_price_after_off')->unsigned();
            $table->foreignId("owner")->references("id")->on("users");

            /**
             *
             * STATUS
             *
             * -2: WAITING TO SUBMIT FOR PENDING
             *
             * -1: ADMIN REJECTED THIS COURSE
             *
             * 0: WAITING FOR ADMIN ACCEPT
             *
             * 1: ACCEPTED BY ADMIN
             *
             */
            $table->smallInteger("status")->default("-2");
            $table->foreignId('cat_id')->references('id')->on('categories');
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
        Schema::dropIfExists('school_courses');
    }
}
