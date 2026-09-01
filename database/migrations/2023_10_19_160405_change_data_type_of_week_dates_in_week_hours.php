<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeDataTypeOfWeekDatesInWeekHours extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('week_hours', function (Blueprint $table) {
            $table->date('week_start_date')->nullable()->change();
            $table->date('week_end_date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('week_hours', function (Blueprint $table) {
            $table->timestamp('week_start_date')->nullable()->change();
            $table->timestamp('week_end_date')->nullable()->change();
        });
    }
}
