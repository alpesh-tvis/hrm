<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    
    protected $table = 'clients';
    protected $fillable  = ['first_name', 'last_name', 'full_name', 'mobile', 'email', 'billing_address','linkdin', 'company', 'source_portal','p_company','b_name','b_address','b_vat','b_mobile','b_email','other_details','address_line1','address_line2','p_city','p_state','p_postalcode','p_country'];

    protected $hidden = [
        'remember_token',
    ];

    public function getFullnameAttribute()
    {
        
        if (!empty($this->first_name) && !empty($this->last_name)) {
            $name = $this->first_name . ' ' . $this->last_name;

            if (!empty($this->company)) {
                $name .= ' (' . $this->company . ')';
            }

            return $name;
        }

        return !empty($this->company) ? $this->company : 'Unknown';
    }
    public static function getOrCreate($company)
    {
        return self::firstOrCreate(['company' => $company])->id;
    }
    
}
