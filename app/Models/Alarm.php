<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alarm extends Model
{
    protected $fillable = [
        'machine_type',
        'code_alarm',
        'description',
    ];

    public function actions()
    {
        return $this->hasMany(Action::class);
    }

    // Optional: jumlah actions
    public function getStepAttribute()
    {
        return $this->actions->count();
    }
}
