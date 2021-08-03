<?php

use App\Models\tmp_codes;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPresenterToTmpCodes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tmp_codes', function (Blueprint $table) {
            $table->tinyInteger('presenter')->default(tmp_codes::NO_PRESENTER)->after('expire');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tmp_codes', function (Blueprint $table) {
            $table->dropColumn('presenter');
        });
    }
}
