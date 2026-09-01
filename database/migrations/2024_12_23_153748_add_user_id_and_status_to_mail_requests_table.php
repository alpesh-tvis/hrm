<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdAndStatusToMailRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mail_requests', function (Blueprint $table) {
            $table->enum('status', ['pending','approved', 'cancelled'])->nullable()->default(null)->after('request_date');
            $table->integer('user_id')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mail_requests', function (Blueprint $table) {
             $table->dropColumn(['status', 'user_id']);
        });
    }
}
