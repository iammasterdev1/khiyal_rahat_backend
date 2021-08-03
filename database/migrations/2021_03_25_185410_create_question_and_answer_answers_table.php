<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionAndAnswerAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_and_answer_answers', function (Blueprint $table) {
            $table->id();
            $table->text('answer');
            $table->foreignId('q_id')->references('id')->on('question_and_answer_questions');

            /**
             *
             * -1: REJECTED
             *
             * 0: PENDING
             *
             * 1: ACCEPTED
             *
             */
            $table->smallInteger('status')->default('1');
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
        Schema::dropIfExists('question_and_answer_answers');
    }
}
