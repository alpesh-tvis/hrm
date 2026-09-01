<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UpworkTimer extends Model
{
    use HasFactory;
    protected $fillable = [
        'timer_id',
        'project_id',
        'user_id',
        'timer_date',
        'timer_status',
        'total_time'
    ];
}
