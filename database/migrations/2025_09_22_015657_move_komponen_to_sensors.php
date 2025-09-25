<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sensors', function (Blueprint $table) {
            $table->string('komponen')->nullable(); // tambahkan ke sensors
        });

        Schema::table('actions', function (Blueprint $table) {
            $table->dropColumn('komponen'); // hapus dari actions kalau ada
        });
    }

    public function down(): void
    {
        Schema::table('actions', function (Blueprint $table) {
            $table->string('komponen')->nullable();
        });

        Schema::table('sensors', function (Blueprint $table) {
            $table->dropColumn('komponen');
        });
    }
};

