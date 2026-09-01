<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryDeduction extends Model
{
    use HasFactory;
    protected $table = 'salary_deduction';
    protected $fillable = [
        'employee_id ',
        'employee_name',
        'leave_type',
        'salary_deduction',
        'reason',
        'date',
        'month',
        'financial_year',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
