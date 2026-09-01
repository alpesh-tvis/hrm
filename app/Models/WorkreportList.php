<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkreportList extends Model
{
    use HasFactory;
    protected $table = 'work_report_list';
    protected $fillable  = ['start_time', 'end_time', 'working_hours','user_id','work_date','work_time'];
}
