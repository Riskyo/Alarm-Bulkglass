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
    Schema::table('alarms', function (Blueprint $table) {
        $table->dropColumn('machine_type');
    });

    Schema::table('actions', function (Blueprint $table) {
        $table->dropColumn('machine_type');
    });

    Schema::table('sensors', function (Blueprint $table) {
        $table->dropColumn('machine_type');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
