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
            $table->string('firstName');
            $table->string('lastName');
            $table->string('email')->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string("phone_number")->unique();
            $table->timestamp('phone_number_verified_at')->nullable();
            $table->foreignId('major')->references('id')->on('majors');
            $table->foreignId("study_area")->references('id')->on('student_study_areas');
            $table->string('password');
            $table->softDeletes();

            /**
             *
             * 0: Admin
             *
             * 1: Students
             *
             * 2: Teachers
             *
             * 3: Consultants
             */
            $table->smallInteger("account_type")->unsigned();
            $table->string("token")->nullable()->unique();
            $table->ipAddress("ip_address");
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
        Schema::dropIfExists('users');
    }
}
