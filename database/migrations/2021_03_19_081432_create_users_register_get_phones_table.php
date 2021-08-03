<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersRegisterGetPhonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_register_get_phones', function (Blueprint $table) {
            $table->id();
            $table->string("phone_number");
            $table->integer("verification_code");
            $table->string('token' , 100);
            $table->ipAddress("send_request_ip");
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
        Schema::dropIfExists('users_register_get_phones');
    }
}
