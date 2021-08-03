<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products_images', function (Blueprint $table) {
            $table->id();
            $table->string("image_path");
            $table->string("image_alt")->nullable();
            $table->string("product_type");
            $table->string("product_id");

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
        Schema::dropIfExists('products_images');
    }
}
