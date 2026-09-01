<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;
    protected $table = 'leads';

    protected $fillable = ['bid_name','bid_url','bid_status','bid_source','user_id','bid_reason','client_id','bid_date'];

    protected $casts = [
        'created_at' => 'datetime:d-m-Y H:i:s',
    ];
}
