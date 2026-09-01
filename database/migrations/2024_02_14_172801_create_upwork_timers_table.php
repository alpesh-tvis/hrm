<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUpworkTimersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('upwork_timers', function (Blueprint $table) {
            $table->id();
            $table->integer('timer_id');
            $table->integer('project_id');
            $table->integer('user_id');
            $table->date('timer_date');
            $table->text('timer_status')->nullable();
            $table->text('total_time')->nullable();
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
        Schema::dropIfExists('upwork_timers');
    }
}
