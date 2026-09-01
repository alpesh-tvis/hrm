<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftSettings extends Model
{
    use HasFactory;
    protected $table = 'shift_settings';
    protected $fillable = [
        'employee_name',
        'mon',
        'tue',
        'wed',
        'thu',
        'fri',
        'sat',
        'sun',
        'shift',
        'created_at'
    ];
}
