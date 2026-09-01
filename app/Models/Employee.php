<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    protected $table = 'employees';
    protected $fillable  = ['first_name', 'last_name', 'full_name', 'mobile', 'personal_email', 'postal_address', 'service_start_date', 'department', 'position', 'reporting_person', 'linkdin', 'pancard', 'eid','company_email','bday','service_enddate','upwork_password','upwork_profile','profile_image','sift_type','upwork_username'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function leaves()
    {
        return $this->hasMany(Leave::class, 'user_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'id');
    }
    public function weekHour()
    {
        return $this->hasOne(WeekHour::class, 'user_id', 'id');
    }
}
