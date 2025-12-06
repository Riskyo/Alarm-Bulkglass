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
    Schema::table('pdfs', function (Blueprint $table) {
        $table->unsignedBigInteger('machine_type_id')->nullable()->after('id');
        $table->dropColumn('machine_type');
    });
}

public function down()
{
    Schema::table('pdfs', function (Blueprint $table) {
        $table->string('machine_type')->nullable();
        $table->dropColumn('machine_type_id');
    });
}

};
