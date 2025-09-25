<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alarm extends Model
{
    protected $fillable = [
        'code_alarm',
        'description',
    ];

    public function actions()
    {
        return $this->hasMany(Action::class);
    }
    public function getStepAttribute()
    {
        return $this->actions->count();
    }
}
