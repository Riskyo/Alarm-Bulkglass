<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('alarms', function (Blueprint $table) {
            if (Schema::hasColumn('alarms', 'step')) {
                $table->renameColumn('step', 'code_alarm');
            }

            if (Schema::hasColumn('alarms', 'description_alarm')) {
                $table->renameColumn('description_alarm', 'description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('alarms', function (Blueprint $table) {
            if (Schema::hasColumn('alarms', 'code_alarm')) {
                $table->renameColumn('code_alarm', 'step');
            }

            if (Schema::hasColumn('alarms', 'description')) {
                $table->renameColumn('description', 'description_alarm');
            }
        });
    }
};

