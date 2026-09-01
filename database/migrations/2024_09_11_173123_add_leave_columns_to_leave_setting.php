<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLeaveColumnsToLeaveSetting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leave_setting', function (Blueprint $table) {
            $table->text('taken_cl_leave')->nullable();
            $table->text('taken_sl_leave')->nullable();
            $table->text('taken_pl_leave')->nullable();
            $table->text('remaining_cl_leave')->nullable();
            $table->text('remaining_sl_leave')->nullable();
            $table->text('remaining_pl_leave')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leave_setting', function (Blueprint $table) {
            $table->dropColumn('taken_cl_leave');
            $table->dropColumn('taken_sl_leave');
            $table->dropColumn('taken_pl_leave');
            $table->dropColumn('remaining_cl_leave');
            $table->dropColumn('remaining_sl_leave');
            $table->dropColumn('remaining_pl_leave');
        });
    }
}
