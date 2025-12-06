<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    // 1. alarms
    Schema::table('alarms', function (Blueprint $table) {
        $table->unsignedBigInteger('machine_type_id')->nullable()->after('id');
    });

    // 2. actions
    Schema::table('actions', function (Blueprint $table) {
        $table->unsignedBigInteger('machine_type_id')->nullable()->after('alarm_id');
    });

    // 3. sensors
    Schema::table('sensors', function (Blueprint $table) {
        $table->unsignedBigInteger('machine_type_id')->nullable()->after('action_id');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alarm_related_tables', function (Blueprint $table) {
            //
        });
    }
};
