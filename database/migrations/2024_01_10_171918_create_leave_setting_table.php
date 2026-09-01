<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaveSettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leave_setting', function (Blueprint $table) {
            $table->id();
            $table->integer('employee_id');
            $table->text('sick_leave')->nullable();
            $table->text('paid_leave')->nullable();
            $table->text('casual_leave')->nullable();
            $table->text('previous_year_leave')->nullable();
            $table->date('financial_year')->nullable();
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
        Schema::dropIfExists('leave_setting');
    }
}
