<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BidRelation extends Model
{
    use HasFactory;
    protected $table = 'bid_relation';
    protected $fillable = [
        'bid_id',
        'client_id',
        'project_id'
    ];
}
