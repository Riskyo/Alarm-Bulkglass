<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pdfs', function (Blueprint $table) {

            // Pastikan kolom ada (agar tidak error)
            if (!Schema::hasColumn('pdfs', 'machine_type_id')) {
                $table->unsignedBigInteger('machine_type_id')
                      ->nullable()
                      ->after('id');
            }

            // Tambahkan foreign key
            $table->foreign('machine_type_id')
                  ->references('id')
                  ->on('machine_types')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('pdfs', function (Blueprint $table) {
            $table->dropForeign(['machine_type_id']);
        });
    }
};
