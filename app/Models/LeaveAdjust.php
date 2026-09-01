<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveAdjust extends Model
{
    use HasFactory;
    protected $table = 'leave_adjust';
    protected $fillable = [
        'user_id ',
        'leave_type',
        'leave_deduct',
        'leave_adjust',
        'leave_date',
        'leave_month',
        'leave_reason',
        'financial_year',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
