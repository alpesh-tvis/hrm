<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','project_name','project_description','staging_url','live_url','timer_id','employee_id','cred_type','start_date','end_date','service_status'];

    public function setEmployee_idAttribute($value)
    {
        // dd($value);
        // $this->attributes['employee_id'] = $value;
    }    
}
