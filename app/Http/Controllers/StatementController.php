<?php

namespace App\Http\Controllers;
use App\Services\StatementImportService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\TransactionType;
use Illuminate\Http\Request;
use App\Http\Requests\StatementImportRequest;
use App\Models\AccountName;
use App\Models\Statement;
use App\Models\Currency;
use App\Models\Client;
use App\Models\Rate;
use Carbon\Carbon;
use ZipArchive;
use DataTables;
use Response;
use Storage;
use Session;
use Auth;
use DB;
use Cache;
class StatementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }
    private function makeArray($collection)
    {
        return $collection->pluck('ref_id')
            ->unique()
            ->values()
            ->flip()
            ->map(fn($i) => $i + 1)
            ->toArray();
    } 
 
    public function index(Request $request)
    {
        $user = Auth::user();

            if ($user->role != '2') {
                return redirect()->route('dashboard.index');
            }

            $currentMonth = Carbon::now()->month;
            $currentYear  = $request->financial_year ?? Carbon::now()->year;

            $start_date = Carbon::create($currentYear, 4, 1)->startOfDay();
            $end_date = Carbon::create($currentYear + 1, 3, 31)->endOfDay();


            if (!$request->financial_year && $currentMonth < 4) {
                $start_date = Carbon::create($currentYear - 1, 4, 1)->startOfDay();
                $end_date = Carbon::create($currentYear, 3, 31)->endOfDay();
            }
        
            $key = 'statement_report_' .
                    $start_date->format('Ymd') . '_' .
                    $end_date->format('Ymd');
        
            $get_smallest_date = Cache::remember('statement_min_date', 3600, function () {
                return \App\Models\Statement::orderBy('date', 'asc')->first();
            });

            $get_largest_date = Cache::remember('statement_max_date', 3600, function () {
                return \App\Models\Statement::orderBy('date', 'desc')->first();
            });

            $stat = Cache::remember($key, 3600, function () use ($start_date, $end_date) {

                return Statement::join(
                            'transaction_types',
                            'statements.type',
                            '=',
                            'transaction_types.id'
                        )
                        ->leftJoin(
                            'clients',
                            'statements.team',
                            '=',
                            'clients.id'
                        )
                        ->leftJoin(
                            'account_names',
                            'statements.account_name',
                            '=',
                            'account_names.id'
                        )
                        ->whereBetween(
                            'statements.billing_date',
                            [$start_date, $end_date]
                        )
                        ->select(
                            'statements.*',
                            'transaction_types.type',
                            'clients.company',
                            'account_names.accountname'
                        )
                        ->orderBy('statements.billing_date', 'asc')
                        ->get();
            });


        //dd($stat->count(), $stat->last());
        $key = "all_statements_" . $start_date . "_" . $end_date;
        $allStatements = Cache::remember($key, 3600, function () use ($start_date, $end_date) {
            return Statement::whereBetween('billing_date', [$start_date, $end_date])->get();
        })->sortBy($request->get('sort_by', 'ref_id'), descending: $request->get('direction') === 'desc');
        
        $ptstat = $allStatements->whereIn('type', [3,6,9,12,14,17]);

        if (!$request->financial_year || $request->financial_year == 2025) {
            $ptstat = $ptstat->whereIn('account_name', [1,2,3,6]);
        }

        $stat_array = $this->makeArray($ptstat);

        $tistat = $ptstat->whereIn('account_name', [1,2]);
        $tistat_array = $this->makeArray($tistat); 

        $ptistat = $ptstat->whereIn('account_name', 6);
        $ptistat_array = $this->makeArray($ptistat);

        $wtstat = $ptstat->where('account_name', 3);
        $wtstat_array = $this->makeArray($wtstat);

        $localstat = $ptstat->whereIn('account_name', [4,5]);
        $localstat_array = $this->makeArray($localstat);

        $gst_by_ser_fee = $this->makeArray(
            $allStatements->where('type', 16)
                ->filter(fn($s) => str_contains($s->description, 'service fee'))
        );

        $gst_by_membership_fee = $this->makeArray(
            $allStatements->where('type', 16)
                ->filter(fn($s) => str_contains($s->description, 'membership'))
        );

        $stat_by_ser_fee = $this->makeArray(
            $allStatements->where('type', 2)
                ->filter(fn($s) => !str_contains($s->description, 'Service Fee return for Refund'))
        );

        $stat_by_wht = $this->makeArray(
            $allStatements->where('type', 1)
                ->filter(fn($s) => !str_contains($s->description, 'return for Refund'))
        );

        $stat_by_withdrawal = $this->makeArray($allStatements->where('type', 5));
        $stat_by_withdr_fee = $this->makeArray($allStatements->where('type', 4));
        $stat_by_payment    = $this->makeArray($allStatements->where('type', 10));

        $type_mf = $this->makeArray(
            $allStatements->whereIn('type', [7,15])
                ->filter(fn($s) => str_contains($s->description, 'Subscription Renewal Charges'))
        );

        $type_amf = $this->makeArray(
            $allStatements->whereIn('type', [7,13])
                ->filter(fn($s) => !str_contains($s->description, 'Subscription Renewal Charges'))
        );

        $adjustment_array = $this->makeArray($allStatements->where('type', 11));

        $return_refund = $allStatements
            ->where('type', 2)
            ->filter(fn($s) => str_contains($s->description, 'Service Fee return for Refund'))
            ->pluck('description', 'ref_id')
            ->toArray();

        $type_refund1 = $allStatements
            ->where('type', 8)
            ->pluck('description', 'ref_id');

        $refIds = $type_refund1->map(function ($desc) {
            return trim(explode('Ref ID', $desc)[1] ?? '');
        });

        $refundStatements = $allStatements->whereIn('ref_id', $refIds);

        $type_refund = [];

        foreach ($type_refund1 as $key => $refund) {
            $ref_id = trim(explode('Ref ID', $refund)[1] ?? '');

            if ($refundStatements->where('ref_id', $ref_id)->count()) {
                $type_refund[$key] = $refund;
            }
        }

        $cutoffDate = '2026-04-01';

        $key = "statement_min_max";

        $minMax = Cache::remember($key, 3600, function () {
            return [
                'min' => \App\Models\Statement::min('date'),
                'max' => \App\Models\Statement::max('date'),
            ];
        });

        $lowest_date  = $minMax['min'];
        $highest_date = $minMax['max'];
        
        $dates = $stat->pluck('billing_date')->unique();

        $key = "rates_by_dates_" . md5(serialize($dates));
        $rates = Cache::remember($key, 3600, function () use ($dates) {
            return \App\Models\Rate::whereIn('rate_date', $dates)->get();
        });
        
        $ratesByDate = $rates->keyBy('rate_date');

        $arr = [];


        $stat->map(function ($item) use ($rates, $cutoffDate) {

            $date = $item->billing_date;
            $operator = ($date >= $cutoffDate) ? '<=' : '>=';
            $order    = ($date >= $cutoffDate) ? 'desc' : 'asc';
            $cacheKey = 'rate_' . md5($operator . '_' . $date . '_' . $order);

            $check_exits_date1 = Cache::remember($cacheKey, 3600, function () use ($operator, $date, $order) {
                return \App\Models\Rate::whereDate('rate_date', $operator, $date)
                    ->orderBy('rate_date', $order)
                    ->first();
            });

            $item->check_exits_date1 = $check_exits_date1;
            $item->currency_rate = $check_exits_date1;

            return $item;
        });


        $ti_no_arr = $stat_array;
        $titi_no_arr = $tistat_array;
        $ptiti_no_arr = $ptistat_array;
        $wtti_no_arr = $wtstat_array;
        $localti_no_arr = $localstat_array;
        $gst_ser_fee = $gst_by_ser_fee;
        $gst_membership_fee = $gst_by_membership_fee;
        $com_no_arr = $stat_by_ser_fee;
        $wht_no_arr = $stat_by_wht;
        $withdrawal_no_arr = $stat_by_withdrawal;
        $withdrawal_fee_no_arr = $stat_by_withdr_fee;
        $payment_no_arr = $stat_by_payment;
        $type_mf_no_arr = [];
            foreach ($type_mf as $ref_id => $value) {

                $type_mf_no_arr[$ref_id] = $value;

            }
        $type_adj_no_arr = $adjustment_array;
        $type_amf_no_arr = $type_amf;

        $allowedAccounts = ['tviStech', 'Parth Goradia', 'WT','Pranthesh Patel'];
        $financialYear = request()->get('financial_year', 2025);

        //* Start - Edited by Priyal to seperate Pranthesh Account */

        $p_ti_keys = [];
        $p_com_keys = [];
        $p_wht_keys = [];
        $p_mf_keys = [];
        $p_amf_keys = [];
        $p_adj_keys = [];
        $p_mfp_keys = [];
        $p_rcpt_keys = [];
        $p_wt_keys = [];

        foreach ($stat as $item) {

            if ($item->accountname == 'Pranthesh Patel') {

                if (isset($ti_no_arr[$item->ref_id])) {
                    $p_ti_keys[] = $item->ref_id;
                }

                if (isset($com_no_arr[$item->ref_id])) {
                    $p_com_keys[] = $item->ref_id;
                }

                if (isset($wht_no_arr[$item->ref_id])) {
                    $p_wht_keys[] = $item->ref_id;
                }

                if (isset($type_mf_no_arr[$item->ref_id])) {
                    $p_mf_keys[] = $item->ref_id;
                }

                if (isset($type_amf_no_arr[$item->ref_id])) {
                    $p_amf_keys[] = $item->ref_id;
                }

                if (isset($type_adj_no_arr[$item->ref_id])) {
                    $p_adj_keys[] = $item->ref_id;
                }

                if (isset($payment_no_arr[$item->ref_id])) {
                    $p_mfp_keys[] = $item->ref_id;
                }

                if (isset($withdrawal_no_arr[$item->ref_id])) {
                    $p_rcpt_keys[] = $item->ref_id;
                }

                if (isset($withdrawal_fee_no_arr[$item->ref_id])) {
                    $p_wt_keys[] = $item->ref_id;
                }
            }
        }

        $p_com_keys = array_values(array_unique(array_filter($p_com_keys)));
        $p_ti_keys  = array_values(array_unique(array_filter($p_ti_keys)));
        $p_wht_keys = array_values(array_unique(array_filter($p_wht_keys)));
        $p_mf_keys  = array_values(array_unique(array_filter($p_mf_keys)));
        $p_amf_keys = array_values(array_unique(array_filter($p_amf_keys)));
        $p_adj_keys = array_values(array_unique(array_filter($p_adj_keys)));
        $p_mfp_keys = array_values(array_unique(array_filter($p_mfp_keys)));
        $p_rcpt_keys = array_values(array_unique(array_filter($p_rcpt_keys)));
        $p_wt_keys = array_values(array_unique(array_filter($p_wt_keys)));

        $p_com_no_arr = !empty($p_com_keys) ? array_combine($p_com_keys, range(1, count($p_com_keys))) : [];
        $p_ti_no_arr  = !empty($p_ti_keys)  ? array_combine($p_ti_keys, range(1, count($p_ti_keys))) : [];
        $p_wht_no_arr = !empty($p_wht_keys) ? array_combine($p_wht_keys, range(1, count($p_wht_keys))) : [];
        $p_mf_no_arr  = !empty($p_mf_keys)  ? array_combine($p_mf_keys, range(1, count($p_mf_keys))) : [];
        $p_amf_no_arr = !empty($p_amf_keys) ? array_combine($p_amf_keys, range(1, count($p_amf_keys))) : [];
        $p_adj_no_arr = !empty($p_adj_keys) ? array_combine($p_adj_keys, range(1, count($p_adj_keys))) : [];
        $p_mfp_no_arr = !empty($p_mfp_keys) ? array_combine($p_mfp_keys, range(1, count($p_mfp_keys))) : [];
        $p_rcpt_no_arr = !empty($p_rcpt_keys) ? array_combine($p_rcpt_keys, range(1, count($p_rcpt_keys))) : [];
        $p_wt_no_arr = !empty($p_wt_keys) ? array_combine($p_wt_keys, range(1, count($p_wt_keys))) : [];
        

        $com_no_arr = array_diff_key($com_no_arr, array_flip($p_com_keys));
        if (!empty($com_no_arr)) {
            $com_no_arr = array_combine(array_keys($com_no_arr),range(1, count($com_no_arr)));
        } else {
            $com_no_arr = [];
        }

        $wht_no_arr = array_diff_key($wht_no_arr, array_flip($p_wht_keys));
        if (!empty($wht_no_arr)) {
            $wht_no_arr = array_combine(array_keys($wht_no_arr),range(1, count($wht_no_arr)));
        } else {
            $wht_no_arr = [];
        }

        $payment_no_arr = array_diff_key($payment_no_arr, array_flip($p_mfp_keys));
        if (!empty($payment_no_arr)) {
            $payment_no_arr = array_combine(array_keys($payment_no_arr),range(1, count($payment_no_arr)));
        } else {
            $payment_no_arr = [];
        }

        $type_mf_no_arr = array_diff_key($type_mf_no_arr, array_flip($p_mf_keys));
        if (!empty($type_mf_no_arr)) {
            $type_mf_no_arr = array_combine(array_keys($type_mf_no_arr), range(1, count($type_mf_no_arr)));
        } else {
            $type_mf_no_arr = [];
        }

        $type_adj_no_arr = array_diff_key($type_adj_no_arr, array_flip($p_adj_keys));
        if (!empty($type_adj_no_arr)) {
            $type_adj_no_arr = array_combine(array_keys($type_adj_no_arr),range(1, count($type_adj_no_arr)));
        } else {
            $type_adj_no_arr = [];
        }

        $type_amf_no_arr = array_diff_key($type_amf_no_arr, array_flip($p_amf_keys));
        if (!empty($type_amf_no_arr)) {
            $type_amf_no_arr = array_combine(array_keys($type_amf_no_arr), range(1, count($type_amf_no_arr)));
        } else {
            $type_amf_no_arr = [];
        }

        $withdrawal_no_arr = array_diff_key($withdrawal_no_arr, array_flip($p_rcpt_keys));
        if (!empty($withdrawal_no_arr)) {
            $withdrawal_no_arr = array_combine(array_keys($withdrawal_no_arr),range(1, count($withdrawal_no_arr)));
        } else {
            $withdrawal_no_arr = [];
        }

        $withdrawal_fee_no_arr = array_diff_key($withdrawal_fee_no_arr, array_flip($p_wt_keys));
        if (!empty($withdrawal_fee_no_arr)) {
            $withdrawal_fee_no_arr = array_combine(array_keys($withdrawal_fee_no_arr),range(1, count($withdrawal_fee_no_arr)));
        } else {
            $withdrawal_fee_no_arr = [];
        }
        /* End - Edited by Priyal to seperate Pranthesh Account */

        $type_refund = $type_refund ?? [];

        $type_refund_keys = array_keys($type_refund);
        $type_refund_no_generate = $this->generate_bill_no(1, count($type_refund_keys), 1);

        $type_refund_no_arr = (!empty($type_refund_keys) && !empty($type_refund_no_generate))
            ? array_combine($type_refund_keys, $type_refund_no_generate)
            : [];

        $type_refund_no_arr1 = [];

        //* Start - Edited by Priyal to seperate Pranthesh Account */

        foreach ($stat as $item) {

            $isP = $item->accountname == 'Pranthesh Patel';
            $pre = $isP ? 'P' : '';

            $item->ti_label = null;
            $item->wt_ti_label = null;
            $item->local_ti_label = null;
            $item->gst_label = null;
            $item->membership_gst_label = null;
            $item->com_label = null;
            $item->wht_label = null;
            $item->withdrawal_label = null;
            $item->withdrawal_fee_label = null;
            $item->payment_label = null;
            $item->mf_label = null;
            $item->adj_label = null;
            $item->amf_label = null;
            $item->refund_label = null;
            $item->refund_label_cn = null;

            // TI //
            /*if (isset($ti_no_arr[$item->ref_id])) {
                if ($isP) {
                    $item->ti_label = ($financialYear == 2025)
                        ? 'PUP/TI' . ($p_ti_no_arr[$item->ref_id] ?? '')
                        : 'PTI' . ($p_ti_no_arr[$item->ref_id] ?? '');
                } elseif (in_array($item->accountname, $allowedAccounts)) {
                    $prefix = ($financialYear == 2025) ? $pre . 'UP/TI' : 'TI';
                    $item->ti_label = $prefix . $ti_no_arr[$item->ref_id];
                }
            }
            // TI //
            /*if (in_array($item->accountname, $allowedAccounts)){
                if (($item->accountname == 'tviStech' || $item->accountname == 'Parth Goradia') && isset($ti_no_arr[$item->ref_id])) {
                    //dd($item->accountname, $ti_no_arr[$item->ref_id]);
                    $item->ti_label = (!$financialYear || $financialYear == 2025)
                        ? $pre . 'UP/TI' . $ti_no_arr[$item->ref_id]
                        : $pre . 'TI' . $ti_no_arr[$item->ref_id];
                } elseif ($item->accountname == 'Pranthesh Patel' && isset($p_ti_no_arr[$item->ref_id])) {
                    $item->ti_label = (!$financialYear || $financialYear == 2025)
                        ? $pre . 'PUP/TI' . $p_ti_no_arr[$item->ref_id]
                        : $pre . 'PTI' . $p_ti_no_arr[$item->ref_id];
                }
            }*/

            if (($item->accountname == 'tviStech' || $item->accountname == 'Parth Goradia') && isset($titi_no_arr[$item->ref_id])) {
                $item->ti_label = (!$financialYear || $financialYear == 2025)
                    ? $pre . 'UP/TI' . $titi_no_arr[$item->ref_id]
                    : $pre . 'TI' . $titi_no_arr[$item->ref_id];
            }

            if ($item->accountname == 'Pranthesh Patel' && isset($ptiti_no_arr[$item->ref_id])) {
                $item->ti_label = (!$financialYear || $financialYear == 2025)
                    ? $pre . 'OT/TI' . $ptiti_no_arr[$item->ref_id]
                    : $pre . 'TI' . $ptiti_no_arr[$item->ref_id];
            }

            // WT //
            if ($item->accountname == 'WT' && isset($wtti_no_arr[$item->ref_id])) {
                $item->wt_ti_label = (!$financialYear || $financialYear == 2025)
                    ? $pre . 'OT/TI' . $wtti_no_arr[$item->ref_id]
                    : $pre . 'TI' . $wtti_no_arr[$item->ref_id];
            }

            // LOCAL //
            if (($item->accountname == 'Local' || $item->accountname == 'FL') && isset($localti_no_arr[$item->ref_id])) {
                $item->local_ti_label = (!$financialYear || $financialYear == 2025)
                    ? $pre . 'LC/TI' . $localti_no_arr[$item->ref_id]
                    : $pre . 'TI' . $localti_no_arr[$item->ref_id];
            }

            // GST //
            if (isset($gst_ser_fee[$item->ref_id])) {
                $item->gst_label = $pre . 'SGST' . $gst_ser_fee[$item->ref_id];
            }

            if (isset($gst_membership_fee[$item->ref_id])) {
                $item->membership_gst_label = $pre . 'MGST' . $gst_membership_fee[$item->ref_id];
            }

            // COM //
            if (($item->accountname == 'tviStech' || $item->accountname == 'Parth Goradia') && isset($com_no_arr[$item->ref_id])) {
                $item->com_label = $pre . 'COM' . $com_no_arr[$item->ref_id];
            }

            if ($item->accountname == 'Pranthesh Patel' && isset($p_com_no_arr[$item->ref_id])) {
                $item->com_label = $pre . 'COM' . $p_com_no_arr[$item->ref_id];
            }

            // TDS //
            if(($item->accountname == 'tviStech' || $item->accountname == 'Parth Goradia') && isset($wht_no_arr[$item->ref_id])) {
                $item->wht_label = $pre . 'TDS' . $wht_no_arr[$item->ref_id];
            }

            if ($item->accountname == 'Pranthesh Patel' && isset($p_wht_no_arr[$item->ref_id])) {
                $item->wht_label = $pre . 'TDS' . $p_wht_no_arr[$item->ref_id];
            }

            // MFP //
            if(($item->accountname == 'tviStech' || $item->accountname == 'Parth Goradia') && isset($payment_no_arr[$item->ref_id])){
                $item->payment_label = $pre . 'MFP' . $payment_no_arr[$item->ref_id];
            }

            if ($item->accountname == 'Pranthesh Patel' && isset($p_mfp_no_arr[$item->ref_id])) {
                $item->payment_label = $pre . 'MFP' . $p_mfp_no_arr[$item->ref_id];
            }

            // MF //
            if(($item->accountname == 'tviStech' || $item->accountname == 'Parth Goradia') && isset($type_mf_no_arr[$item->ref_id])){
                $item->mf_label = $pre . 'MF' . $type_mf_no_arr[$item->ref_id];
            }

            if ($item->accountname == 'Pranthesh Patel' && isset($p_mf_no_arr[$item->ref_id])) {
                $item->mf_label = $pre . 'MF' . $p_mf_no_arr[$item->ref_id];
            }

            // ADJ //
            if (($item->accountname == 'tviStech' || $item->accountname == 'Parth Goradia') && isset($type_adj_no_arr[$item->ref_id])) {
                $item->adj_label = $pre . 'ADJ' . $type_adj_no_arr[$item->ref_id];
            }

            if ($item->accountname == 'Pranthesh Patel' && isset($p_adj_no_arr[$item->ref_id])) {
                $item->adj_label = $pre . 'ADJ' . $p_adj_no_arr[$item->ref_id];
            }

            //AMF//
            if (($item->accountname == 'tviStech' || $item->accountname == 'Parth Goradia') && isset($type_amf_no_arr[$item->ref_id])) {
                $item->amf_label = $pre . 'AMF' . $type_amf_no_arr[$item->ref_id];
            }

            if ($item->accountname == 'Pranthesh Patel' && isset($p_amf_no_arr[$item->ref_id])) {
                $item->amf_label = $pre . 'AMF' . $p_amf_no_arr[$item->ref_id];
            }


            // RCPT //
            if (($item->accountname == 'tviStech' || $item->accountname == 'Parth Goradia') && isset($withdrawal_no_arr[$item->ref_id])) {
                $item->withdrawal_label = $pre . 'RCPT' . $withdrawal_no_arr[$item->ref_id];
            }

            if ($item->accountname == 'Pranthesh Patel' && isset($p_rcpt_no_arr[$item->ref_id])) {
                $item->withdrawal_label = $pre . 'RCPT' . $p_rcpt_no_arr[$item->ref_id];
            }


            // WF //
            if (($item->accountname == 'tviStech' || $item->accountname == 'Parth Goradia') && isset($withdrawal_fee_no_arr[$item->ref_id])) {
                $item->withdrawal_fee_label = $pre . 'WF' . $withdrawal_fee_no_arr[$item->ref_id];
            }
            if ($item->accountname == 'Pranthesh Patel' && isset($p_wt_no_arr[$item->ref_id])) {
                $item->withdrawal_fee_label = $pre . 'WF' . $p_wt_no_arr[$item->ref_id];
            }

            if (array_key_exists($item->ref_id, $type_refund_no_arr)) {
                $item->refund_label_cn =
                    'CN' . $type_refund_no_arr[$item->ref_id] .
                    '-TI' . ($type_refund_no_arr1[$item->ref_id] ?? '');
            }

            if (isset($return_refund[$item->ref_id])) {
                $item->refund_label = str_replace(
                    'Service Fee return for Refund - Ref ID',
                    '',
                    $return_refund[$item->ref_id]
                );
            }

            $item->formatted_amount = number_format((float)$item->amount, 2, '.', '');

            $item->display_name = ($item->accountname == 'Parth Goradia') ? 'PG': (($item->accountname == 'Pranthesh Patel') ? 'PP': $item->accountname);

            if (str_contains($item->description, "Service Fee return for Refund")) {
                $item->display_type = 'Refund SF';
            } elseif (
                str_contains($item->description, "return for Refund") &&
                $item->type == "WHT"
            ) {
                $item->display_type = 'Refund WHT';
            } else {
                $item->display_type = $item->type;
            }
        }

        /* End - Edited by Priyal to seperate Pranthesh Account */

        return view('statement.statement_list')->with(compact(
            'stat',
            'stat_array',
            'stat_by_ser_fee',
            'stat_by_wht',
            'stat_by_withdrawal',
            'stat_by_withdr_fee',
            'stat_by_payment',
            'type_mf',
            'type_amf',
            'return_refund',
            'type_refund',
            'adjustment_array',
            'wtstat_array',
            'localstat_array',
            // 'rates',
            'cutoffDate',
            'lowest_date',
            'highest_date',
            'ratesByDate',
            'gst_by_ser_fee',
            'gst_by_membership_fee',
            'get_smallest_date',
            'get_largest_date' ,'type_refund_no_arr','type_refund_no_arr1'
        ));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('statement.statement_form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Cache::flush();
        return $this->store_new_csv_data($request);

        $validated = $request->validate([
            'statement_import' => 'required'
        ]);
        $file = $request->file('statement_import');

        $filename = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $tempPath = $file->getRealPath();
        $fileSize = $file->getSize();
        $mimeType = $file->getMimeType();
        $valid_extension = array("csv");
        $maxFileSize = 2097152; 

        if(in_array(strtolower($extension),$valid_extension)){
            if($fileSize <= $maxFileSize){
                
                $location = 'uploads';
                $file->move(public_path()."/uploads/", $file->getClientOriginalName());
                $filepath = public_path($location."/".$filename);
                $file = fopen($filepath,"r");
                $head = fgetcsv($file, 1000, ',', '"');
                $importData_arr = array();  
                $i = 0;
                while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                    $num = count($filedata );
                    $importData_arr[] = array_combine($head, $filedata);
                }
                fclose($file);

                $importData_new = array_reverse($importData_arr);
                $currentYear = date('Y');
                foreach($importData_new as  $importData){
                    
                    $all_data = array_change_key_case($importData,CASE_LOWER);

                    $validator = Validator::make($all_data, [
                        'amount' => 'required|numeric'
                    ]);

                    if ($validator->fails()) {
                        return redirect()->route('importStatement.create')
                            ->withErrors($validator)
                            ->withInput()
                            ->with('error', 'Amount field is invalid at one or more rows.');
                    }

                    
                    $date = date_format(date_create($all_data['date']),"Y-m-d");
                    $fy_lastdate = Carbon::parse($currentYear.'-03-31');
                    $company_name = utf8_encode($all_data['team']);
                    $check_type_exits =  TransactionType::where('type', $all_data['type'] )->first();
                    $check_company_exits =  Client::where('company', $company_name)->first();
                    $check_statements_exits =  Statement::where('ref_id', $all_data['ref id'] )->first();
                    $check_accountname_exits =  AccountName::where('accountname', $all_data['account name'] )->first();
                    $TransactionType_data = TransactionType::where('type', $all_data['type'] )->first();
                    $account_name =  AccountName::where('accountname', $all_data['account name'] )->first();
                    $day = date('D', strtotime($date));
                    $transection_date = Carbon::parse($date);
                    
                    if($check_type_exits->id == '1' || $check_type_exits->id == '2' || $check_type_exits->id == '3' || $check_type_exits->id == '6' || $check_type_exits->id == '9' || $check_type_exits->id == '12' || $check_type_exits->id == '8'|| $check_type_exits->id == '14')
                    {

                        if($check_type_exits->id == '8' || $check_type_exits->id == '1' || $check_type_exits->id == '2'){
                            $ex_val = explode('Ref ID',$all_data['description']);
                            $ref_id = trim($ex_val[1]);

                            $stat1 = Statement::where('ref_id', $ref_id)->first();
                            $b_date = Carbon::parse($stat1->billing_date);
                        } else if($check_type_exits->id == '3'){
                            if(str_contains($all_data['description'], "return for Refund")){
                                $explode = explode('Ref ID', $all_data['description']);
                                $statement = Statement::where('ref_id', trim($explode[1]))->first();
                                $b_date = Carbon::parse($statement->billing_date);
                            }else{
                                if($day == 'Wed') {
                                    $b_date = Carbon::parse($date)->subDays(10);   
                                }else{
                                    $b_date = Carbon::parse($date)->subDays(5);
                                }
                            }
                        } else {
                            $b_date = Carbon::parse($date)->subDays(5);
                        }    
                        
                        if($b_date == $fy_lastdate){
                            $b_date = $fy_lastdate;
                        }
                    
                        
                        if($b_date == $fy_lastdate){
                            $get_rate_b_date = Rate::where('rate_date','<=',$b_date)->orderBy('rate_date','desc')->first();
                            $check_rate_exits = Rate::whereDate('rate_date', '<=', $b_date)->exists();
                        }
                        else
                        {
                            $get_rate_b_date = Rate::where('rate_date','>=',$b_date)->orderBy('rate_date','asc')->first();
                            $check_rate_exits = Rate::whereDate('rate_date', '>=', $b_date)->exists();
                        }    

                        
                        if($b_date == $fy_lastdate){
                                $billing_date = $fy_lastdate;
                        }else{
                            $billing_date = $get_rate_b_date->rate_date;
                        }
                    }
                    else
                    {

                        $get_rate_date = Rate::where('rate_date','>=',$date)->orderBy('rate_date','asc')->first();
                        $check_rate_exits = Rate::whereDate('rate_date', '>=', $transection_date)->exists();
                        if(empty($get_rate_date->rate_date)){
                            return redirect()->route('importStatement.create')->with('error','Current date rate not found. please upload rates');
                            exit(); 
                        }
                        $billing_date = $get_rate_date->rate_date;

                    }
                    
                    if(!$check_type_exits){
                        
                        TransactionType::create(['type' => $all_data['type']]);
                    }
                    if(!$check_company_exits){
                        $check_company_exits = Client::create(['company' => $company_name]);
                    }
                    if(!$check_accountname_exits){
                        AccountName::create(['accountname' => $all_data['account name']]);
                    }
                    
                    if(!$check_statements_exits){
                        
                        if($check_rate_exits == true){
                            $currency = $all_data['currency'] ?? null;
                            Statement::create([
                                'date' => $date, 
                                'ref_id' => $all_data['ref id'],
                                'type' => $TransactionType_data->id,
                                'description' => $all_data['description'],
                                'team' => $check_company_exits->id,
                                'billing_date'=> $billing_date, 
                                'amount' => $all_data['amount'],
                                'account_name' => $account_name->id,
                                'currency' => $currency
                            ]);
                        }
                        else{
                            return redirect()->route('importStatement.create')->with('error','Current date rate not found. please upload rates');
                            exit(); 
                        }
                    }
                    else{
                        $currency = $all_data['currency'] ?? null;
                        Statement::where('ref_id',$all_data['ref id'])->update([
                            'date' => $date,
                            'ref_id' => $all_data['ref id'],
                            'type' => $TransactionType_data->id,
                            'description' => $all_data['description'],
                            'team' => $check_company_exits->id,
                            'billing_date'=> $billing_date, 
                            'amount' => $all_data['amount'],
                            'account_name' => $account_name->id,
                            'currency' => $currency
                            ]); 
                    }    

                }
                $path = public_path("uploads/".$filename);
                unlink($path);    

                return redirect()->route('importStatement.create')->with('success','Import Successfully.');
                
            }else{
                return redirect()->route('importStatement.create')->with('error','Please upload file less than 2MB'); 
            }

        }
        else {
            return redirect()->route('importStatement.create')->with('error','Invalid file type. Please upload csv file'); 
        }    
    }    

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Statement  $statement
     * @return \Illuminate\Http\Response
     */
    public function show(Statement $statement)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Statement  $statement
     * @return \Illuminate\Http\Response
     */
    public function edit(Statement $statement)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Statement  $statement
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Statement $statement)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Statement  $statement
     * @return \Illuminate\Http\Response
     */
    public function destroy(Statement $statement)
    {
        //
    }
    public function download(){
        $file_path = public_path('uploads/statements_sample.csv');
        return response()->download( $file_path);
    }
    public function statement_sample(){

        $file_path = public_path('uploads/sample_file/statements_sample.csv');
        return response()->download( $file_path);
        
    }
    
    public function multi_select(Request $request)
    {
        $multi_id = $request->multi_id;

        // Check if IDs exist and are array
        if (empty($multi_id) || !is_array($multi_id)) {

            return redirect()
                ->route('importStatement.index')
                ->with('error', 'Select at least one bill date box.');
        }

        // Validate required fields
        if (empty($request->withdrawal_date) || empty($request->withdrawal_rate)) {

            return redirect()
                ->route('importStatement.index')
                ->with('error', 'Withdrawal Date and Rate are required.');
        }

        // Update all selected records
        Statement::whereIn('ref_id', $multi_id)->update([
            'withdrawal_date' => $request->withdrawal_date,
            'withdrawal_rate' => $request->withdrawal_rate
        ]);

        return redirect()
            ->route('importStatement.index')
            ->with('success', 'Withdrawal details added successfully.');
    }

    public function generate_pdf(Request $request)
    {
        $current_year = $request->fin_year;
        $cheked_header = $request->print_header;
        $ref_id = $request->ref_id ?? [];

        $start_date = $current_year . "-04-01";
        $end_date = date('Y-m-d', strtotime($start_date . ' + 365 days'));

        $baseQuery = Statement::whereIn('type', ['3','6','9','12','14','17'])
            ->whereBetween('statements.billing_date', [$start_date, $end_date]);

        $invoiceData = (clone $baseQuery)
            ->whereIn('ref_id', $ref_id)
            ->join('clients', 'statements.team', '=', 'clients.id')
            ->orderBy('ref_id', 'asc')
            ->get();

        $ti_keys = (clone $baseQuery)->whereIn('account_name', [1,2])
            ->groupBy('ref_id')->pluck('ref_id')->toArray();

        $wt_ti_keys = (clone $baseQuery)->where('account_name', 3)
            ->groupBy('ref_id')->pluck('ref_id')->toArray();

        $local_ti_keys = (clone $baseQuery)->whereIn('account_name', [4,5])
            ->groupBy('ref_id')->pluck('ref_id')->toArray();

        $p_ti_keys = (clone $baseQuery)->where('account_name', 6)
            ->groupBy('ref_id')->pluck('ref_id')->toArray();


        $ti_no_arr = [];
        foreach (array_values(array_unique($ti_keys ?? [])) as $index => $refId) {
            $ti_no_arr[$refId] = $index + 1;
        }

        $wt_no_arr = [];
        foreach (array_values(array_unique($wt_ti_keys ?? [])) as $index => $refId) {
            $wt_no_arr[$refId] = $index + 1;
        }

        $local_no_arr = [];
        foreach (array_values(array_unique($local_ti_keys ?? [])) as $index => $refId) {
            $local_no_arr[$refId] = $index + 1;
        }

        $p_no_arr = [];
        foreach (array_values(array_unique($p_ti_keys ?? [])) as $index => $refId) {
            $p_no_arr[$refId] = $index + 1;
        }

        $stat_array = $ti_keys ?? [];
        $wtstat_array = $wt_no_arr ?? [];
        $localstat_array = $local_no_arr ?? [];

    
        if ($request->pdf_id == 'generate_pdf') {

            $pdf = PDF::loadView('invoice', compact(
                'localstat_array',
                'invoiceData',
                'stat_array',
                'cheked_header',
                'wtstat_array',
                'p_no_arr',
                'current_year'
            ))->save(Storage::path('invoice.pdf'));

            if ($pdf) {

                $multi_file_array = [];

                foreach ($invoiceData as $invoice) {

                    $invoice_no = '';

                    if (isset($ti_no_arr[$invoice->ref_id])) {
                        $invoice_no = 'UP/TI'.$ti_no_arr[$invoice->ref_id];
                    }

                    if (isset($local_no_arr[$invoice->ref_id])) {
                        $invoice_no = 'LC/TI'.$local_no_arr[$invoice->ref_id];
                    }

                    if (isset($wt_no_arr[$invoice->ref_id])) {
                        $invoice_no = 'OT/TI'.$wt_no_arr[$invoice->ref_id];
                    }

                    if (isset($p_no_arr[$invoice->ref_id])) {
                        $invoice_no = 'POT/TI'.$p_no_arr[$invoice->ref_id];
                    }

                    $multi_file_array[] = $invoice_no;
                }

                sort($multi_file_array);

                $first = reset($multi_file_array);
                $last = end($multi_file_array);

                // $pdf_val = (count($multi_file_array) > 1)
                //     ? 'TI'.$first.'-TI'.$last.'.pdf'
                //     : 'TI'.$first.'.pdf';

                $pdf_val = (count($multi_file_array) > 1)? $first.'-'.$last.'.pdf': $first.'.pdf';

                return Response::json([
                    'success' => true,
                    'pdf' => route('print'),
                    'stat_ids' => $pdf_val
                ]);
            }
        }

        if ($request->pdf_id == 'generate_zip') {

        // Purane PDFs remove karo
        Storage::disk('public')->deleteDirectory('pdf');
        Storage::disk('public')->makeDirectory('pdf');

        $multi_file_array = [];
        $multi_file_array1 = [];

        // Duplicate ref_id remove
        $uniqueInvoices = $invoiceData->unique('ref_id');

        foreach ($uniqueInvoices as $invoice) {

            $user_details = Statement::where('ref_id', $invoice->ref_id)
                ->join('clients', 'statements.team', '=', 'clients.id')
                ->whereBetween('statements.billing_date', [$start_date, $end_date])
                ->get();

            $invoice_no = '';

                if (isset($ti_no_arr[$invoice->ref_id])) {
                    $invoice_no = 'UP-TI'.$ti_no_arr[$invoice->ref_id];
                }

                if (isset($local_no_arr[$invoice->ref_id])) {
                    $invoice_no = 'LC-TI'.$local_no_arr[$invoice->ref_id];
                }

                if (isset($wt_no_arr[$invoice->ref_id])) {
                    $invoice_no = 'OT-TI'.$wt_no_arr[$invoice->ref_id];
                }

                if (isset($p_no_arr[$invoice->ref_id])) {
                    $invoice_no = 'POT-TI'.$p_no_arr[$invoice->ref_id];
                }


            $html = view('multipdf', compact(
                'user_details',
                'stat_array',
                'ti_no_arr',
                'cheked_header',
                'localstat_array',
                'wtstat_array',
                'p_no_arr'
            ))->render();

           

            PDF::loadHTML($html)->save(Storage::disk('public')->path('pdf/'.$invoice_no.'.pdf'));

            $multi_file_array[] = 'pdf/'.$invoice_no.'.pdf';
            $multi_file_array1[] = $invoice_no;
        }

        sort($multi_file_array1);

        $first = reset($multi_file_array1);
        $last = end($multi_file_array1);

        $pdf_val = (count($multi_file_array1) > 1)
            ? 'TI'.$first.'-TI'.$last.'.zip'
            : 'TI'.$first.'.zip';

        Session::put('ref_ids', $multi_file_array);

        return Response::json([
            'success' => true,
            'pdf' => route('zip_download'),
            'stat_ids' => $pdf_val
        ]);
    }}

    public function preview_pdf()
    {
        $zip = new ZipArchive();

        $fileName = 'invoices.zip';

        $zipPath = public_path($fileName);

        if ($zip->open($zipPath, ZipArchive::OVERWRITE | ZipArchive::CREATE) === TRUE) {

            $files = Storage::disk('public')->allFiles('pdf');

            foreach ($files as $value) {

                $fullPath = storage_path('app/public/' . $value);

                if (file_exists($fullPath)) {

                    $relativeName = basename($value);

                    $zip->addFile($fullPath, $relativeName);
                }
            }

            $zip->close();

            return response()->download($zipPath)->deleteFileAfterSend(true);
        }

        return redirect()
            ->route('importStatement.index')
            ->with('error', 'Unable to generate ZIP file.');
    }

    public function print(Request $request)
    {
        $filename = $request->pdf;

        $path =  storage_path('/app/public/invoice.pdf');
        
        return Response::make(file_get_contents($path), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$filename.'"'
        ]);

        return redirect()->route('importStatement.index')->with('success','PDF Download Successfully');
    }

    public function zip_download(Request $request)
    {
        $session_value = Session::get('ref_ids', []);

        if (empty($session_value) || !is_array($session_value)) {
            return redirect()
                ->route('importStatement.index')
                ->with('error', 'No PDF files found for download.');
        }

        $zip = new ZipArchive();
        $fileName = $request->pdf ?? 'invoices.zip';
        $zipPath = public_path($fileName);

        if ($zip->open($zipPath, ZipArchive::OVERWRITE | ZipArchive::CREATE) === TRUE) {
            
            foreach ($session_value as $value) {

                $fullPath = storage_path('app/public/' . $value);

                if (file_exists($fullPath)) {
                    $zip->addFile($fullPath, basename($value));
                }
            }

            $zip->close();

            session()->forget('ref_ids');

            return response()->download($zipPath)->deleteFileAfterSend(true);
        }

        return redirect()
            ->route('importStatement.index')
            ->with('error', 'Unable to create ZIP file.');
    }

    
    
    function generate_bill_no($start, $count, $digits) {
        $result = array();

        for ($n = $start; $n < $start + $count; $n++) {
            $result[] = str_pad($n, $digits, "0", STR_PAD_LEFT);
        }
        return $result;
    }

    function delete_w_rate($id){

        Statement::where('id',$id)->update(['withdrawal_date'=>null, 'withdrawal_rate'=>null]);
        return redirect()->route('importStatement.index')->with('success','W Rate Deleted Successfully');
    }

    public function ajax_statement(Request $request)
    {
        if ( $request->ajax() ) {
            if($request->financial_year)
            {
                $current_year = $request->financial_year;
            }
            else
            {
                $current_year = date('Y');
            }

            $start_date = $current_year."-04-01";
            $end_date = date('Y-m-d', strtotime($start_date. ' + 365 days'));
            $user = Auth::user();
            
            $stat = Statement::Join('transaction_types','statements.type','=','transaction_types.id')->leftJoin('clients','statements.team','=','clients.id')->leftJoin('account_names','statements.account_name','=','account_names.id')->select('statements.id as id','statements.date','statements.ref_id','transaction_types.type','statements.amount','statements.description','statements.billing_date','statements.withdrawal_date','statements.withdrawal_rate','clients.company','account_names.accountname')->whereBetween('statements.billing_date',[$start_date, $end_date])->get();

            $datatable = Datatables::of($stat)->addIndexColumn()->make(true);
            return $datatable;
        }
        return view('statement.statement_ajax');

        if($request->financial_year)
        {
            $current_year = $request->financial_year;
        }
        else
        {
            $current_year = date('Y');
        }
        
        $start_date = $current_year."-04-01";
        $end_date = date('Y-m-d', strtotime($start_date. ' + 365 days'));
        $user = Auth::user();

        $stat = Statement::Join('transaction_types','statements.type','=','transaction_types.id')->leftJoin('clients','statements.team','=','clients.id')->leftJoin('account_names','statements.account_name','=','account_names.id')->select('statements.id','statements.date','statements.ref_id','transaction_types.type','statements.amount','statements.description','statements.billing_date','statements.withdrawal_date','statements.withdrawal_rate','clients.company','account_names.accountname')->whereBetween('statements.billing_date',[$start_date, $end_date])->get();


        
        if($user->role == '2'){
            return view('statement.statement_ajax')->with(compact('stat'));
        }
        else
        {
            return view('admin.admin_main');
        }
    }    

    function add_invoice()
    {
        $ids = [3, 6, 9];
        // $TransactionType = TransactionType::orderBy('type', 'asc')->pluck('type', 'id');
        $TransactionType = TransactionType::whereIn('id', $ids)->pluck('type', 'id');
        $clients = Client::get()->sortBy('fullname')->pluck('fullname','id');
        $AccountNames = AccountName::orderBy('accountname')->pluck('accountname','id');
        $currency = Currency::orderBy('name')->get(['name', 'code', 'html_symbol']);

        return view('statement.add_invoice',[
            'TransactionType' => $TransactionType,
            'clients' => $clients,
            'AccountNames' => $AccountNames,
            'Currency' => $currency
        ]);
    }

    public function storeInvoice(Request $request)
    {
        $messages = [
            'billing_date.exists'            => 'No rate found for the selected billing date',
            'date.required'                  => 'The Invoice Date is required',
            'billing_date.required'          => 'The Billing Date is required',
            'ref_id.required'                => 'The Reference ID is required',
            'ref_id.unique'                  => 'This Reference ID already exists',
            'type.required'                  => 'The Transaction Types is required',
            'team.required'                  => 'Please select Client',
            'account_name.required'          => 'Please select Account Name',
            'amount.required'                => 'The Amount is required',
            'business_location.required_if'  => 'Please select Business Location.',
            'CGST.required_if'               => 'The CGST is required',
            'SGST.required_if'               => 'The SGST is required',
            'IGST.required_if'               => 'The IGST is required',

        ];

        $validator = Validator::make($request->all(), [
            'date'              => 'required|date',
            'billing_date'      => 'required|date|exists:rates,rate_date',
            'ref_id'            => 'required|max:255|unique:statements,ref_id',
            'amount'            => 'required|min:0',
            'type'              => 'required',
            'team'              => 'required',
            'account_name'      => 'required',
            'currency'          => 'required',
            'business_location' => 'required_if:currency,INR',
            'CGST'              => 'required_if:business_location,in_g',
            'SGST'              => 'required_if:business_location,in_g',
            'IGST'              => 'required_if:business_location,out_g',
            'description'       => 'nullable|string',
            'hsn'               => 'nullable|string',
        ], $messages);


        $validator->after(function ($validator) use ($request) {

            $currency = $request->currency;
            $date     = $request->billing_date;

            $rate = Rate::where('rate_date', $date)->first();

            $currencyColumn = strtolower($currency);
            if (!$rate || empty($rate->$currencyColumn)) {
                $validator->errors()->add(
                    'currency',
                    'Selected currency rate not available for selected billing date'
                );
            }
        });

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        $validatedData = $validator->validated();

        $business_location = null;
        $cgst = null;
        $sgst = null;
        $igst = null;

        if($validatedData['currency'] == 'INR'){
            if($validatedData['business_location'] == 'in_g'){
                $business_location = $validatedData['business_location'];
                $cgst = $validatedData['CGST'];
                $sgst = $validatedData['SGST'];
            }
            if($validatedData['business_location'] == 'out_g'){
                $business_location = $validatedData['business_location'];
                $igst = $validatedData['IGST'];
            }
        }

        // dd($sgst);
        Statement::create([
            'date'    => $validatedData['date'],
            'billing_date'    => $validatedData['billing_date'],
            'ref_id'          => $validatedData['ref_id'],
            'amount'          => $validatedData['amount'],
            'type' => $validatedData['type'],
            'team'       => $validatedData['team'],
            'account_name' => $validatedData['account_name'],
            'description'     => $validatedData['description'],
            'currency' => $validatedData['currency'],
            'business_location' => $business_location,
            'CGST' => $cgst,
            'SGST' => $sgst,
            'IGST' => $igst,
            'hsn' => $validatedData['hsn']
        ]);

        return redirect()->route('importStatement.index')->with('success','Invoice added successfully!');
    }

    public function store_new_csv_data(Request $request){

        /*New code*/
        $request->validate([
            'statement_import' => 'required|file|mimes:csv|max:2048',
        ]);
        StatementImportService::import($request->file('statement_import'));

        return redirect()
            ->route('importStatement.create')
            ->with('success', 'Import Successfully.');

        /*End New code*/
        $request->validate([
            'statement_import' => 'required|file|mimes:csv|max:2048',
        ]);

        $file = $request->file('statement_import');
        
        $transactionTypes = TransactionType::pluck('id','type')->toArray();
        $clients = Client::pluck('id','company')->toArray();
        $accounts = AccountName::pluck('id','accountname')->toArray();
        $rates = Rate::orderBy('rate_date','asc')->get();
        
        $importData = [];
        
        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            $headers = fgetcsv($handle, 1000, ',', '"');

            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                if (count($headers) == count($row)) {
                    $importData[] = array_combine($headers, $row);
                }
            }
            fclose($handle);
        }
        
        $importData_new = array_reverse($importData);
        
        foreach($importData_new as  $importData){
            $all_data = array_change_key_case($importData,CASE_LOWER);
        
            
            // dd($all_data);
            if(isset($all_data['amount'])){
                $all_data['amount $'] = $all_data['amount'];
            }

            if(isset($all_data['current balance'])){
                $all_data['current balance $'] = $all_data['current balance'];
            }

            if (!isset($all_data['amount $']) ) {
                return redirect()->route('importStatement.create')
                    ->with('error', 'Invalid amount at one or more rows.');
            }

            $all_data['amount'] = $all_data['amount $'];
            
            if (isset($all_data['client team'])) {
                $company_name = utf8_encode($all_data['client team']);
            }

            if (isset($all_data['transaction type'])) {
                $all_data['type'] = $all_data['transaction type'];
            }

            $date = date_format(date_create($all_data['date']),'Y-m-d');
            $fy_lastdate =  Carbon::createFromDate(null, 3, 31);

            $typeName = $all_data['transaction type'];

            if(!isset($transactionTypes[$typeName])){
                $type = TransactionType::create(['type'=>$typeName]);
                $transactionTypes[$typeName] = $type->id;
            }

            $check_type_exists = (object)['id'=>$transactionTypes[$typeName]];
            
            if(!isset($clients[$company_name])){
                $client = Client::create(['company'=>$company_name]);
                $clients[$company_name] = $client->id;
            }

            $check_company_exits = (object)['id'=>$clients[$company_name]];

            $accountName = $all_data['account name'];

            if(!isset($accounts[$accountName])){
                $account = AccountName::create(['accountname'=>$accountName]);
                $accounts[$accountName] = $account->id;
            }

            $account_name = (object)['id'=>$accounts[$accountName]];

            $day = date('D', strtotime($date));
            
            $typeName = $all_data['transaction type'];

            $descriptionFields = [
                'transaction summary',
                'transaction summary details',
                'description 1',
                'description 2',
                'description 3'
            ];

            $descriptions = [];
            foreach ($descriptionFields as $field) {
                if (!empty($all_data[$field] ?? null)) {
                    $descriptions[] = trim($all_data[$field]);
                }
            }

            $all_data['description'] = implode(' ', $descriptions);

            $special_ids = ['1','2','3','6','8','9','12','14','17'];
            $direct_parse_ids = ['1','2','8'];
            $b_date = Carbon::parse($date);
            if (in_array($check_type_exists->id, $special_ids)) {
                
                $b_date = Carbon::parse($date);
                if (!in_array($check_type_exists->id, $direct_parse_ids)) {
                    if ($check_type_exists->id == '3') {
                        $b_date = ($day == 'Wed') ? $b_date->copy()->subDays(10) : $b_date->copy()->subDays(5);
                    } else {
                        $b_date = $b_date->subDays(5);
                    }
                }
                
                if($b_date == $fy_lastdate){
                    $b_date = $fy_lastdate;
                }

                $billing_date = $b_date;
                
            }
            else
            {
                $billing_date = $b_date;
            }

            $data = [
                'date' => $date,
                'ref_id' => $all_data['ref id'],
                'type' => $check_type_exists->id,
                'description' => $all_data['description'],
                'team' => $check_company_exits->id,
                'billing_date'=> $billing_date,
                'amount' => $all_data['amount'],
                'account_name' => $account_name->id,
                'currency' => $all_data['currency'] ?? null
            ];
            

                Statement::updateOrCreate(
                    ['ref_id' => $all_data['ref id']],
                    $data
                );    
            
        }
        
        return redirect()->route('importStatement.create')->with('success','Import Successfully.');
    }
}