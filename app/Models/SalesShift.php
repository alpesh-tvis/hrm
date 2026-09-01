<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesShift extends Model
{
    use HasFactory;
    protected $table = 'sales_shift';
    protected $primaryKey = 'id';
    protected $fillable = ['user_id', 'sun', 'mon', 'tue', 'wed', 'thur', 'fri', 'sat', 'shift', 'shift_time'];
}
