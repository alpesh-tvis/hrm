<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bid_status extends Model
{
    use HasFactory;
    protected $table = 'bid_status';
    protected $fillable = ['bid_id','user_id','bid_status','bid_date'];
}
