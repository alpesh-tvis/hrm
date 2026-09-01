<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExtraColumnsToClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            //
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->text('p_city')->nullable();
            $table->string('p_state')->nullable();
            $table->string('p_postalcode')->nullable();
            $table->string('p_country')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            //
            $table->dropColumn('address_line1');
            $table->dropColumn('address_line2');
            $table->dropColumn('p_city');
            $table->dropColumn('p_state');
            $table->dropColumn('p_postalcode');
            $table->dropColumn('p_country');
        });
    }
}
