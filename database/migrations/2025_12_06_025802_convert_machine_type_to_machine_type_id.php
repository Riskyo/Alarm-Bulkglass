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
    $types = DB::table('machine_types')->get();

    // alarms
    foreach (DB::table('alarms')->get() as $a) {
        $type = $types->firstWhere('name', $a->machine_type);
        if ($type) {
            DB::table('alarms')->where('id', $a->id)->update([
                'machine_type_id' => $type->id
            ]);
        }
    }

    // actions
    foreach (DB::table('actions')->get() as $act) {
        $type = $types->firstWhere('name', $act->machine_type);
        if ($type) {
            DB::table('actions')->where('id', $act->id)->update([
                'machine_type_id' => $type->id
            ]);
        }
    }

    // sensors
    foreach (DB::table('sensors')->get() as $s) {
        $type = $types->firstWhere('name', $s->machine_type);
        if ($type) {
            DB::table('sensors')->where('id', $s->id)->update([
                'machine_type_id' => $type->id
            ]);
        }
    }
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('machine_type_id', function (Blueprint $table) {
            //
        });
    }
};
