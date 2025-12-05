<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pdf extends Model
{
    protected $fillable = [
        'machine_type',
        'title',
        'filename',
    ];
}
