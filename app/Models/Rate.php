<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    use HasFactory;
    protected $table = 'rates';
    protected $guarded = [];

    protected $fillable = ['rate_date','price','usd','gbp','eur','yen','sgd'];
}
