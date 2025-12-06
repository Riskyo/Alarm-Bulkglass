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
        $table->foreign('machine_type_id')->references('id')->on('machine_types')->onDelete('cascade');
    });

    Schema::table('actions', function (Blueprint $table) {
        $table->foreign('machine_type_id')->references('id')->on('machine_types')->onDelete('cascade');
    });

    Schema::table('sensors', function (Blueprint $table) {
        $table->foreign('machine_type_id')->references('id')->on('machine_types')->onDelete('cascade');
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
