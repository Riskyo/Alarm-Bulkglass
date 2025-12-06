<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sensor extends Model
{
    protected $fillable = [
        'action_id',
        'sensor_name',
        'komponen',
        'plc_io',
        'machine_type_id',
    ];

    public function action()
    {
        return $this->belongsTo(Action::class);
    }

    public function machineType()
    {
        return $this->belongsTo(MachineType::class, 'machine_type_id');
    }
}
