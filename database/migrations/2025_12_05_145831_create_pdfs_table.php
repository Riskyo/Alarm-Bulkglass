<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pdfs', function (Blueprint $table) {
            $table->id();

            // Machine Type: bulkglass / depalletiser
            $table->string('machine_type', 50);

            // Judul PDF
            $table->string('title');

            // Path file PDF yang disimpan
            $table->string('filename');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pdfs');
    }
};
