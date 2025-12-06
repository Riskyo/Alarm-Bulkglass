<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alarm extends Model
{
    protected $fillable = [
        'machine_type_id',
        'code_alarm',
        'description',
    ];

    public function actions()
    {
        return $this->hasMany(Action::class);
    }

    public function machineType()
    {
        return $this->belongsTo(MachineType::class, 'machine_type_id');
    }

    public function getStepAttribute()
    {
        return $this->actions->count();
    }
}
