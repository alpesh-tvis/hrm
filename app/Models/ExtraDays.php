<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ExtraDays extends Model
{
    protected $table = 'extra_days'; 

    protected $fillable = [
        'employee_id',
        'employee_name',
        'date',
        'reason_of_work_description',
        'extra_days',
        'financial_year'
    ];
    protected $guarded=['id'];
    
}
