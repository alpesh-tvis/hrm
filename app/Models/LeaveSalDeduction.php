<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveSalDeduction extends Model
{
    use HasFactory;
    protected $table = 'leave_sal_deduction';
    protected $fillable = [
        'employee_id ',
        'employee_name',
        'leave_type',
        'salary_deduction',
        'reason',
        'date',
        'month',
        'financial_year',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
