<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkReport extends Model
{
    use HasFactory;
    protected $table = 'workreports';
    protected $fillable = [
        'user_id',
        'work_type',
        'activity_type',
        'project_id',
        'description',
        'emp_ids',
        'work_date',
        'work_time',
        'sift',
        'timer_id',
        'created_at'
    ];

    public function timerName()
    {
        return $this->belongsTo(Employee::class, 'timer_id', 'id');
    }
    public function helpPerson()
    {
        return $this->belongsTo(Employee::class, 'emp_ids', 'id');
    }
    public function projectName()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }
}
