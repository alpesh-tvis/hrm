<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    use HasFactory;
    protected $table = 'leaves'; // Specify the table name if it's different from the model name.

    protected $fillable = [
        'user_id',
        'leave_date',
        'leave_status',
        'leave_reason',
        'leave_type',
        'status'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'user_id', 'id');
    }
}
