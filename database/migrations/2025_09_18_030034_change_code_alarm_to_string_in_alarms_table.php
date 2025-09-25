<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('alarms', function (Blueprint $table) {
            $table->string('code_alarm', 50)->change();
        });
    }

    public function down(): void
    {
        Schema::table('alarms', function (Blueprint $table) {
            $table->integer('code_alarm')->change();
        });
    }
};
