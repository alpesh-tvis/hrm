<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeekHour extends Model
{
   use HasFactory;
   protected $table = 'week_hours';

   protected $fillable = [
      'total_hours',
      'remaining_hours',
      'working_hours',
      'user_id',
      'entry_type',
      'week_start_date',
      'week_end_date',
   ];

   public function employee()
   {
       return $this->belongsTo(Employee::class, 'user_id', 'id');
   }
}
