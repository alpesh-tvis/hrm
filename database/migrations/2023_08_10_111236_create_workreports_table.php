<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkreportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workreports', function (Blueprint $table) {
            $table->id();
            $table->string('user_id')->nullable();
            $table->string('work_type')->nullable();
            $table->string('activity_type')->nullable();
            $table->string('project_id')->nullable();
            $table->text('description')->nullable();
            $table->string('emp_ids')->nullable();
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
        Schema::dropIfExists('workreports');
    }
}
