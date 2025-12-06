<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MachineType extends Model
{
    protected $fillable = ['name'];

    public function alarms()
    {
        return $this->hasMany(Alarm::class);
    }

    public function actions()
    {
        return $this->hasMany(Action::class);
    }

    public function sensors()
    {
        return $this->hasMany(Sensor::class);
    }

    public function pdfs()
    {
        return $this->hasMany(Pdf::class);
    }
}
