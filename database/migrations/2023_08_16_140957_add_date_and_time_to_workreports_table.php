<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDateAndTimeToWorkreportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('workreports', function (Blueprint $table) {
            //
            $table->date('work_date')->nullable();
            $table->time('work_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('workreports', function (Blueprint $table) {
            //
            $table->dropColumn('work_date');
            $table->dropColumn('work_time');
        });
    }
}
