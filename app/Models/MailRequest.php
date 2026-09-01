<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailRequest extends Model
{
    use HasFactory;
    protected $table = 'mail_requests';
    protected $fillable = [
        'subject',
        'reason',
        'description',
        'request_date',
        'status',
        'user_id'
    ];

    protected $attributes = [
        'status' => 'pending',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'user_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
