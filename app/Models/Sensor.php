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
        'machine_type',
    ];

    public function action()
    {
        return $this->belongsTo(Action::class);
    }
}
