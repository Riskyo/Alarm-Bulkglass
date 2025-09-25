<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('alarms', function (Blueprint $table) {
            // ubah nama kolom
            $table->renameColumn('description_alarm', 'description');
            $table->renameColumn('step', 'code_alarm');
        });
    }

    public function down(): void
    {
        Schema::table('alarms', function (Blueprint $table) {
            // kembalikan lagi kalau rollback
            $table->renameColumn('description', 'description_alarm');
            $table->renameColumn('code_alarm', 'step');
        });
    }
};
