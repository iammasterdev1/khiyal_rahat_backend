<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users');
            $table->bigInteger('total_price');
            $table->bigInteger('final_price');
            $table->integer('transaction_id')->unique()->nullable();
            $table->string('tracking_number')->unique()->nullable();

            /**
             *
             * 0: NOT COMPLETED
             *
             * 1: SUCCESSFUL
             *
             */
            $table->smallInteger('payment_status')->default('0');

            /**
             *
             * 0: PENDING TO ADMIN ACCEPTS
             *
             * 1: ACCEPTED AND MAKING READY
             *
             * 2: MADE READY AND SENT
             *
             */
            $table->smallInteger('status')->default('0');
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
        Schema::dropIfExists('orders');
    }
}
