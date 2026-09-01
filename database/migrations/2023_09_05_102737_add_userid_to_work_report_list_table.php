<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUseridToWorkReportListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('work_report_list', function (Blueprint $table) {
            $table->integer('user_id')->nullable();
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
        Schema::table('work_report_list', function (Blueprint $table) {
            //
            $table->dropColumn('user_id');
            $table->dropColumn('work_date');
            $table->dropColumn('work_time');
        });
    }
}
