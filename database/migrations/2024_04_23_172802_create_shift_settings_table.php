<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShiftSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shift_settings', function (Blueprint $table) {
            $table->id();
            $table->string('employee_name')->nullable();
            $table->time('mon')->nullable();
            $table->time('tue')->nullable();
            $table->time('wed')->nullable();
            $table->time('thu')->nullable();
            $table->time('fri')->nullable();
            $table->time('sat')->nullable();
            $table->time('sun')->nullable();
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
        Schema::dropIfExists('shift_settings');
    }
}
