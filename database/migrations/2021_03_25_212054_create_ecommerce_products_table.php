<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEcommerceProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ecommerce_products', function (Blueprint $table) {
            $table->id();
            $table->string('product_name');
            $table->text('product_description');
            $table->integer('stock')->default('100')->unsigned();

            /**
             *
             * -2: HAVEN'T SUBMIT YET
             *
             * -1: REJECTED
             *
             * 0: PENDING
             *
             * 1: ACCEPTED
             *
             * 2: ACCEPTED AND HIDE
             *
             */
            $table->smallInteger('status')->default("2");
            $table->bigInteger('price_irr');
            $table->bigInteger('price_irr_after_off');
            $table->foreignId('category')->references("id")->on('categories');
            $table->string('cover_image');
            $table->foreignId('brand_id')->references("id")->on('brands');
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
        Schema::dropIfExists('ecommerce_products');
    }
}
