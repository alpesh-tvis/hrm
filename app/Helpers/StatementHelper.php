<?php
namespace App\Helpers;

use Carbon\Carbon;

class StatementHelper
{
    public static function normalize($row)
    {
        $row = array_change_key_case($row, CASE_LOWER);

        if (isset($row['amount'])) {
            $row['amount'] = $row['amount'];
        }

        $fields = [
            'transaction summary',
            'transaction summary details',
            'description 1',
            'description 2',
            'description 3'
        ];

        $desc = [];

        foreach ($fields as $field) {
            if (!empty($row[$field] ?? null)) {
                $desc[] = trim($row[$field]);
            }
        }

        $row['description'] = implode(' ', $desc);

        return $row;
    }

    public static function calculateBillingDate($date, $typeId)
    {
        $special_ids = ['1', '2', '3', '6', '8', '9', '12', '14','17'];
        $direct_parse_ids = ['1', '2', '8'];
        $wht_service_fee = ['1', '2'];
        $b_date = Carbon::parse($date);
        $day = $b_date->format('D');
        $fy_lastdate =  Carbon::createFromDate(null, 3, 31);
        if (in_array($typeId, $special_ids)) {

            if (in_array($typeId, $wht_service_fee)) {
                $b_date = ($day == 'Wed')
                        ? $b_date->copy()->subDays(10)
                        : $b_date->copy()->subDays(5);
            }    
            if (!in_array($typeId, $direct_parse_ids)) {

                if ($typeId == '3') {
                    $b_date = ($day == 'Wed')
                        ? $b_date->copy()->subDays(10)
                        : $b_date->copy()->subDays(5);
                } else {
                    $b_date = $b_date->copy()->subDays(5);
                }
            }

            // FY last date check
            if ($fy_lastdate && $b_date->equalTo($fy_lastdate)) {
                $b_date = $fy_lastdate;
            }
        }

        return $b_date;
    }

    // public static function calculateBillingDate($date, $typeId)
    // {
    //     $b_date = Carbon::parse($date);
    //     $day = $b_date->format('D');

    //     $fy_lastdate = Carbon::createFromDate(null, 3, 31);

    //     // TYPE 3 SPECIAL CASE
    //     if ($typeId == '3') {

    //         $b_date = ($day == 'Wed')
    //             ? $b_date->subDays(10)
    //             : $b_date->subDays(5);

    //     }
    //     // TYPES 1 & 2
    //     elseif (in_array($typeId, ['1','2'])) {

    //         $b_date = ($day == 'Wed')
    //             ? $b_date->subDays(10)
    //             : $b_date->subDays(5);

    //     }
    //     // OTHER SPECIAL TYPES
    //     elseif (in_array($typeId, ['6','8','9','12','17'])) {

    //         $b_date = $b_date->subDays(5);
    //     }

    //     if ($b_date->equalTo($fy_lastdate)) {
    //         return $fy_lastdate;
    //     }

    //     logger()->info('BillingDate', [
    //         'date' => $date,
    //         'typeId' => $typeId,
    //         'result' => $b_date->toDateString()
    //     ]);
        
    //     return $b_date;
    // }
}