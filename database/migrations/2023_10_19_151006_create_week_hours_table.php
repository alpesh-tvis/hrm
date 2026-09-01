<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWeekHoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('week_hours', function (Blueprint $table) {
            $table->id();
            $table->string('week_start_end_date')->nullable();
            $table->string('total_hours')->nullable();
            $table->string('remaining_hours')->nullable();
            $table->string('working_hours')->nullable();
            $table->string('user_id')->nullable();
            $table->string('entry_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('week_hours');
    }
}
