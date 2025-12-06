<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MachineTypeSeeder extends Seeder
{
    public function run(): void
    {
        $machines = [
            'bulkglass',
            'depalletiser',
            'robocolumn',
            'incarobot',
            'paletizer',
            'conveyor_b23',
            'conveyor_b17',
            'packer',
            'unpacker',
            'crate_magazine',
        ];

        foreach ($machines as $m) {
            DB::table('machine_types')->insert([
                'name' => $m,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}