<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyWeekHoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('week_hours', function (Blueprint $table) {
            // Remove the 'week_start_end_date' field
            $table->dropColumn('week_start_end_date');

            // Add 'week_start_date' and 'week_end_date' fields
            $table->date('week_start_date')->nullable();
            $table->date('week_end_date')->nullable();
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
        Schema::table('week_hours', function (Blueprint $table) {
            // Add back the 'week_start_end_date' field
            $table->timestamp('week_start_end_date')->nullable();
            
            // Remove the 'week_start_date' and 'week_end_date' fields
            $table->dropColumn('week_start_date');
            $table->dropColumn('week_end_date');
        });
    }
}
