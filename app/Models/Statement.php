<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Statement extends Model
{
    use HasFactory;
    protected $fillable = ['date','ref_id','type','description','team','amount','billing_date','account_name','withdrawal_date','withdrawal_rate','currency','CGST','SGST','IGST','hsn'];

    public static function saveStatement($row, $typeId, $clientId, $accountId, $billingDate)
    {
        $row['amount'] = $row['amount $'];
        unset($row['amount $']);
        $date = date_format(date_create($row['date']),'Y-m-d');
        return self::updateOrCreate(
            ['ref_id' => $row['ref id']],
            [
                'date' => $date,
                'type' => $typeId,
                'team' => $clientId,
                'account_name' => $accountId,
                'amount' => $row['amount'],
                'description' => $row['description'],
                'billing_date' => $billingDate,
                'currency' => $row['currency'] ?? null,
            ]
        );
    }

    public function getShowCheckboxAttribute()
    {
        $allowedTypes = ['Hourly', 'Bonus', 'Milestone','Fixed Price', 'Fixed-price','Reimbursement'];
        return in_array($this->type, $allowedTypes);
    }
    public function getCurrencyColumnAttribute()
    {
        if (empty($this->currency) || strtoupper($this->currency) == 'INR') {
            return 'price';
        }

        return strtolower($this->currency);
    }
}
