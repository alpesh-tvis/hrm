<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountName extends Model
{
    use HasFactory;
    protected $table = 'account_names';
    protected $fillable = ['accountname'];

    public static function getOrCreate($name)
    {
        return self::firstOrCreate(['accountname' => $name])->id;
    }
}
