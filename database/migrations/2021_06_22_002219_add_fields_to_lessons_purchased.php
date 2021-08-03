<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToLessonsPurchased extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lessons_purchased', function (Blueprint $table) {
            $table->text('spot_code')->after('lesson_id');
            $table->text('price')->after('spot_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lessons_purchased', function (Blueprint $table) {
            //
        });
    }
}
