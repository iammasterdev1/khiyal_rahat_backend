<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionAndAnswerQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_and_answer_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users');
            $table->text('question');

            /**
             *
             * -1: REJECTED
             *
             * 0: PENDING
             *
             * 1: ACCEPTED
             *
             */
            $table->smallInteger('status')->default("1");
            $table->string('product_id');
            $table->string('product_type');
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
        Schema::dropIfExists('question_and_answer_questions');
    }
}
