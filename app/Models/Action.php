<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    protected $fillable = [
        'alarm_id',
        'action_text',
        'machine_type_id',
    ];

    public function alarm()
    {
        return $this->belongsTo(Alarm::class);
    }

    public function sensors()
    {
        return $this->hasMany(Sensor::class);
    }

    public function machineType()
    {
        return $this->belongsTo(MachineType::class, 'machine_type_id');
    }
}
