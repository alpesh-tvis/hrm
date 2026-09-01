<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExtraFieldsToClientsTable extends Migration
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
            $table->string('p_company')->nullable();
            $table->string('b_name')->nullable();
            $table->text('b_address')->nullable();
            $table->string('b_vat')->nullable();
            $table->string('b_mobile')->nullable();
            $table->string('b_email')->nullable();
            $table->text('other_details')->nullable();
            
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
            $table->dropColumn('p_company');
            $table->dropColumn('b_name');
            $table->dropColumn('b_address');
            $table->dropColumn('b_vat');
            $table->dropColumn('b_mobile');
            $table->dropColumn('b_email');
            $table->dropColumn('other_details');
        });
    }
}
