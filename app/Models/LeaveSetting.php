<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveSetting extends Model
{
    protected $table = 'leave_setting'; 

    protected $fillable = [
        'employee_id ',
        'emp_name ',
        'sick_leave',
        'paid_leave',
        'casual_leave',
        'previous_year_leave',
        'financial_year'
    ];
    protected $guarded=['id'];
    
    /*public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }*/
}
