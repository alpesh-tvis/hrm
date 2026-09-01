<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Invoice</title>
    </head>
    <body>
    
    @foreach($user_details as $inv_data)
         @php
            $check_exits_date = \App\Models\Rate::whereDate('rate_date', '=',$inv_data->billing_date)->first();
            $check_exits_date1 = \App\Models\Rate::whereDate('rate_date', '>',$inv_data->billing_date)->orderBy('rate_date', 'asc')->first();

            $dateString = $inv_data->billing_date;
            $date1 = \Carbon\Carbon::createFromFormat('Y-m-d', $dateString);
        @endphp

        @if($inv_data)
            @php
                $get_currency = strtolower($inv_data->currency);
            @endphp
        @endif
        
        @if ($date1->format('m-d') === '03-31')
            @php
                $check_exits_date1 = \App\Models\Rate::whereDate('rate_date', '<',$inv_data->billing_date)->orderBy('rate_date', 'desc')->first();
            @endphp    
        @else
            @php
                $check_exits_date1 = \App\Models\Rate::whereDate('rate_date', '>',$inv_data->billing_date)->orderBy('rate_date', 'asc')->first();
            @endphp    
        @endif
        
        @if($check_exits_date)
            @php
                $date = $check_exits_date->rate_date;
            @endphp
        @else
            @php
                $date = $dateString;
            @endphp
        @endif

        @if($cheked_header == 'cheked_headers')
        <table class="header" style="width: 545pt;padding: 20px 0;border-bottom: 1px solid #000; margin:0px auto 30px">
            <tr>
                <td style="width:85%">
                    <h1 style="color:black;text-transform: normal;font-size: 21px;">tviStech</h1>
                    <div style="color:#000;font-weight: bold;font-size: 12px;padding-bottom: 5px;">Web Design & Development Agency</div>
                    <br/>
                    <p><b>Office :</b> 704, Indraprasth Business House, Nr. Vijay Cross Road, Bh. Rasranjan, Navrangpura, Ahmedabad - 380009, Gujarat, India.</p>
                    <p><b>Website :</b> tvistech.com &nbsp;&nbsp;<b>Email:</b> info@tvistech.com &nbsp;&nbsp;<b>Mobile:</b> +919429636656</p>
                    <p><b>GST No. :</b> 24AQYPG3098E1ZI</p>
                </td>
                <td style="width:15%;">
                    <img src="{{asset('public/img/pdf-logo.png')}}" width="100">
                </td>
            </tr>
        </table>
        @endif  
        @if($cheked_header == 'cheked_headers')    
        <table style="width: 545pt; margin:0px auto 30px">
        @endif     
            @if($cheked_header == 'uncheked_header')
            <table style="width: 545pt;margin:180px auto 30px">
            @endif  
            <thead>
                <tr>
                    <td style="width:33.33%;padding-bottom: 10pt;vertical-align: middle;"><b>Debit Memo</b></td>
                    <td style="width:33.33%;padding-bottom: 10pt;vertical-align: middle" align="center"><h2 class="font-big" style="font-weight: bold;">Tax Invoice</h2></td>
                    <td style="width:33.33%;padding-bottom: 10pt;vertical-align: middle" align="right"><b>Triplicate For Supplier</b></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="3">
                        <div style="border: 1px solid #000;width: 100%;">
                            <table cellspacing="0" style="width: 100%;" class="table-top">
                                <tr>
                                    <td style="width: 33.33%;">
                                        <p style="text-decoration: underline;">Details of Receiver (Billed To)</p>
                                        <h3 style="margin-bottom:0">{{$inv_data->company}}</h3>
                                        <h3 style="margin-bottom:2px; margin-top: 2px;">@if($inv_data->account_name < 3)(Upwork Group)@endif</h3>
                                        <p style="margin-bottom: 5px;"><b>Attn: {{$inv_data->b_name}}</b></p>
                                        <p>{!! nl2br(e($inv_data->billing_address)) !!}</p>
                                        <!-- <p>PO Box 1368 <br/>Paradise Point, <br/>4216 Australia</p> -->

                                    </td>
                                    <td style="width: 33.33%;border-left: 1px solid #000;height: 134px;">
                                        @if($inv_data->account_name < 3)
                                        <p style="text-decoration: underline;">Details of payer as a mediator</p>
                                        <h3>Upwork Global Inc</h3>
                                        <p style="margin-bottom: 5px;"><b>Global HQ,</b></p>
                                        <p>475 Brannan St., <br/>Suite 430, San Francisco, <br/>CA, 94107 <br/>United States</p>
                                        @endif
                                    </td>
                                    <td style="width: 33.33%;border-left: 1px solid #000;padding: 0;" valign="middle">
                                       <div style="height: 66px;padding-top:13px">
                                        <table cellspacing="0" style="width: 100%;padding: 0 10px;">
                                            <tr>
                                                <td style="width: 40%;padding: 5px;"><b>Invoice No.</b></td> 
                                                <td style="padding: 5px;"><b>:
                                                    @if($inv_data->account_name == 1 || $inv_data->account_name == 2)
                                                        @if(array_key_exists($inv_data->ref_id, $ti_no_arr))
                                                            @if($_REQUEST['fin_year'] == '2025')
                                                                UP/TI{{$ti_no_arr[$inv_data->ref_id]}}
                                                            @else    
                                                                TI{{$ti_no_arr[$inv_data->ref_id]}}
                                                            @endif    
                                                        @endif
                                                    @endif    

                                                    @if($inv_data->account_name == 4 || $inv_data->account_name == 5)
                                                        @php
                                                            //$localti_no_arr = $localstat_array;
                                                        @endphp
                                                        @if(array_key_exists($inv_data->ref_id, $localstat_array))
                                                            @if(!isset($_GET['financial_year']) || $_GET['financial_year'] == 2025)
                                                                LC/TI{{$localstat_array[$inv_data->ref_id]}}
                                                            @else    
                                                                TI{{$localstat_array[$inv_data->ref_id]}}
                                                            @endif    
                                                        @endif
                                                    @endif

                                                    @if($inv_data->account_name == '3')
                                                        @if(array_key_exists($inv_data->ref_id, $wtstat_array))
                                                            @if(!isset($_GET['financial_year']) || $_GET['financial_year'] == 2025)
                                                                OT/TI{{$wtstat_array[$inv_data->ref_id]}}
                                                            @else    
                                                                TI{{$wtstat_array[$inv_data->ref_id]}}
                                                            @endif    
                                                        @endif
                                                    @endif 

                                                     @if($inv_data->account_name == '6')
                                                        @if(array_key_exists($inv_data->ref_id, $p_no_arr))
                                                            @if(!isset($_GET['financial_year']) || $_GET['financial_year'] == 2025)
                                                                POT/TI{{$p_no_arr[$inv_data->ref_id]}}
                                                            @else    
                                                                PTI{{$p_no_arr[$inv_data->ref_id]}}
                                                            @endif    
                                                        @endif
                                                    @endif
                                                </b></td> 
                                            </tr>
                                            <tr>
                                                <td style="width: 40%;padding: 5px;"><b>Invoice Date</b></td> 
                                                <td style="padding: 5px;"><b>: {{date('d-m-Y',strtotime($date))}}</b></td> 
                                            </tr>
                                        </table>
                                        </div>
                                        <div style="border-top: 1px solid #000;padding-top:13px">
                                        <table cellspacing="0" style="width: 100%;padding: 0 10px;">
                                            @if($inv_data->account_name < 3)
                                            <tr>
                                                <td style="width: 40%;padding: 5px;"><b>Broker</b></td> 
                                                <td style="padding: 5px;">: Upwork Global Inc.</td> 
                                            </tr>
                                            @endif
                                            
                                            @if($inv_data->account_name == 5)
                                            <tr>
                                                <td style="width: 40%;padding: 5px;"><b>Broker</b></td> 
                                                <td style="padding: 5px;"><b> : Freelancer.com </b></td> 
                                                
                                            </tr>
                                            @endif
                                            @if($inv_data->account_name != 3 )
                                                <tr>
                                                    <td style="width: 40%;padding: 5px;"><b>Reference ID</b></td> 
                                                    <td style="padding: 5px;"><b> : {{$inv_data->ref_id}} </b></td> 
                                                </tr>
                                            @endif    
                                        </table>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            <table cellspacing="0" class="priceTable" style="width: 100%;border-bottom: 1px solid #000;">
                                <tr>
                                    <th style="width: 4%;background: #eee;">Sr. No</th>
                                    <th style="width: 29.3%;background: #eee;">Description of Services</th>
                                    <th style="width: 16.65%;background: #eee;">HSN/SAC</th>
                                    <th style="width: 16.65%;background: #eee;">
                                        @if(($inv_data->account_name == 4 || $inv_data->account_name == 5) && $inv_data->currency =='INR')
                                            INR
                                        @elseif($inv_data->currency)
                                            {{$inv_data->currency}}
                                        @else
                                            USD
                                        @endif
                                    </th>
                                    @if(($inv_data->account_name == 4 || $inv_data->account_name == 5) && $inv_data->currency =='INR')
                                    <th style="width: 16.65%;background: #eee;"></th>
                                    @else
                                    <th style="width: 16.65%;background: #eee;">Conversion Rate <br/>In INR</th>
                                    @endif
                                    <th style="width: 16.65%;background: #eee;border-right: 0;">Taxable Amount 
                                        @if($inv_data->account_name < 3)
                                            (Amount in INR)
                                        @endif
                                    </th>
                                </tr>
                                <tr>
                                    <td style="width: 4%;">1</td>
                                    <td style="width: 29.3%;text-align: left;height: 210px;">
                                        <b>Web Design & Development Service</b>
                                        {{$inv_data->description}}
                                    </td>
                                    <td style="width: 16.65%;">
                                        @if($inv_data->hsn)
                                            {{$inv_data->hsn}}
                                        @else
                                            998314
                                        @endif
                                    </td>
                                    <td style="width: 16.65%;">
                                        {{number_format((float)$inv_data->amount, 2, '.', '')}} 
                                        @if(($inv_data->account_name == 4 || $inv_data->account_name == 5 ) && $inv_data->currency =='INR')
                                        INR
                                        @elseif($inv_data->currency)
                                            {{$inv_data->currency}}
                                        @else
                                        USD
                                        @endif
                                    </td>
                                    <td style="width: 16.65%;">
                                        @if(!empty($check_exits_date->price))
                                            @if(($inv_data->account_name == 4 || $inv_data->account_name == 5) && $inv_data->currency =='INR')

                                            @elseif($inv_data->currency)
                                                {{$check_exits_date->$get_currency}}
                                            @else
                                                {{$check_exits_date->price}}
                                            @endif
                                        @else
                                            @if(!empty($check_exits_date1->price))
                                                {{$check_exits_date1->price}}
                                            @endif  
                                        @endif
                                    </td>
                                    <td style="width: 16.65%;border-right: 0;">

                                         @if(!empty($check_exits_date->price))
                                            @php
                                                if($check_exits_date->$get_currency){
                                                    $total = (number_format((float)$inv_data->amount, 2, '.', ''))*($check_exits_date->$get_currency);
                                                }else{
                                                    $total = (number_format((float)$inv_data->amount, 2, '.', ''))*($check_exits_date->price);
                                                }    
                                            @endphp
                                            @if(($inv_data->account_name == 4 || $inv_data->account_name == 5) && $inv_data->currency =='INR')
                                                {{number_format((float)$inv_data->amount, 2, '.', '')}}
                                            @else
                                                {{number_format((float)$total, 4, '.', '')}}
                                            @endif
                                        @else
                                            @if(!empty($check_exits_date1->price))
                                                @php
                                                    $total = (number_format((float)$inv_data->amount, 2, '.', ''))*($check_exits_date1->price);
                                                @endphp
                                                {{number_format((float)$total, 4, '.', '')}}
                                            @endif  
                                        @endif


                                       
                                            
                                    </td>
                                </tr>
                                <!-- <tr>
                                    <td></td>
                                    <td style="width: 29.3%;text-align: left;height: 210px;"></td>
                                    <td style="width: 16.65%;"></td>
                                    <td style="width: 16.65%;"></td>
                                    <td style="width: 16.65%;"></td>
                                    <td style="width: 16.65%;border-right: 0;"></td>
                                </tr> -->
                            </table>
                            <table cellspacing="0" class="priceTable" style="width: 100%;border-bottom: 1px solid #000;">
                                <tr>
                                    <td style="width: 33.3%;">&nbsp;</td>
                                    <td style="width: 16.65%;"></td>
                                    <td style="width: 16.65%;"></td>
                                    <td style="width: 16.65%;"></td>
                                    <td style="width: 16.65%;border-right: 0;"></td>
                                </tr>
                            </table>
                            <table cellspacing="0" class="pricetotalMain" style="width: 100%;border-bottom: 1px solid #000;">
                                <tr>
                                    <td style="width: 66.4%;border-right: 1px solid #000;">
                                        <table cellspacing="0" style="width: 100%;">
                                            <tr>
                                                <td colspan="2" style="background: #eee;width: 50%;border-bottom: 1px solid #000;text-align: center;padding: 3px;">
                                                    <b>Supplier Details</b>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 50%;padding: 5px 5px 0;">
                                                    <table style="width: 100%;">
                                                        <tr>
                                                            <td style="width: 40%;padding-bottom: 8px;">GSTIN</td> 
                                                            <td><b>: 24AQYPG3098E1ZI</b></td> 
                                                        </tr>
                                                        <tr>
                                                            <td style="width: 40%;padding-bottom: 15px;">State</td> 
                                                            <td><b>: 24 - Gujarat</b></td> 
                                                        </tr> 
                                                    </table>
                                                </td>
                                                <td style="width: 50%;padding: 5px 5px 0;">
                                                    <table style="width: 100%;">
                                                        <tr>
                                                            <td style="width: 40%;">PAN No</td> 
                                                            <td><b>: AQYPG3098E</b></td> 
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="width: 50%;border-bottom: 1px solid #000;border-top: 1px solid #000;background: #eee;text-align: center;padding: 3px;"><b>Bank Detail</b></td>
                                            </tr>
                                            <tr>
                                                <td style="width: 50%;padding: 5px 5px 0;">
                                                    <table style="width: 100%;">
                                                        <tr>
                                                            <td style="width: 40%;padding-bottom: 5px;">Bank Name</td> 
                                                            <td><b>: HDFC BANK LIMITED</b></td> 
                                                        </tr>
                                                        <tr>
                                                            <td style="width: 40%;padding-bottom: 5px;">Branch</td> 
                                                            <td><b>: SIHOR</b></td> 
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td style="width: 50%;padding: 0 15px 10px;"></td>
                                            </tr>
                                            <tr>
                                                <td style="width: 50%;padding: 0 5px 10px;">
                                                    <table style="width: 100%;">
                                                        <tr>
                                                            <td style="width: 40%;padding-bottom: 5px;">Account Number</td> 
                                                            <td><b>: 50200058443602</b></td> 
                                                        </tr>
                                                        <tr>
                                                            <td style="width: 40%;padding-bottom: 5px;">Account Type</td> 
                                                            <td><b>: CURRENT</b></td> 
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td style="width: 50%;padding: 0 15px 10px;">
                                                    <table style="width: 100%;">
                                                        <tr>
                                                            <td style="width: 40%;padding-bottom: 5px;">IFSC Code</td> 
                                                            <td><b>: HDFC0002144</b></td> 
                                                        </tr>
                                                        <tr>
                                                            <td style="width: 40%;padding-bottom: 5px;">Swift Code</td> 
                                                            <td><b>: HDFCINBB</b></td> 
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td style="width: 33.2%;">
                                        <table class="amoutPrice" cellspacing="0" style="width: 100%;">
                                            <tr>
                                                <td style="width: 50.1%;border-right: 1px solid #000;padding: 15px 5px 5px">Taxable Amount...</td>
                                                <td style="width: 49.9%;text-align: right;padding: 15px 5px 0">
                                                    @if($inv_data->account_name == 4 || $inv_data->account_name == 5)
                                                        {{number_format((float)$inv_data->amount, 2, '.', '')}}
                                                    @else    
                                                        {{number_format((float)$total, 4, '.', '')}}
                                                    @endif   
                                                </td>
                                            </tr>
                                            @if($inv_data->account_name == 4 || $inv_data->account_name == 5)
                                                @php
                                                    $taxable_amount = (float) ($inv_data->amount ?? 0);
                                                    $cgst = $taxable_amount * ($inv_data->CGST / 100);
                                                    $sgst = $taxable_amount * ($inv_data->SGST / 100);
                                                    $igst = $taxable_amount * ($inv_data->IGST / 100);
                                                @endphp

                                                @if($inv_data->CGST && $inv_data->SGST)
                                                    <tr>
                                                        <td style="width: 50.1%;border-right: 1px solid #000;padding: 0 5px 5px">CGST {{$inv_data->CGST}}%</td>
                                                        <td style="width: 49.9%;text-align: right;padding: 0 5px">{{ number_format($cgst, 2, '.', '') }}</td>
                                                    </tr>
                                                   
                                                    <tr>
                                                        <td style="width: 50.1%;border-right: 1px solid #000;padding: 0 5px 5px">SGST {{$inv_data->SGST}}%</td>
                                                        <td style="width: 49.9%;text-align: right;padding: 0 5px;">{{ number_format($sgst, 2, '.', '') }}</td>
                                                    </tr>
                                                @else
                                                    <tr>
                                                        <td style="width: 50.1%;border-right: 1px solid #000;padding: 0 5px 5px">IGST {{$inv_data->IGST ?? 0}}%</td>
                                                        <td style="width: 49.9%;text-align: right;padding: 0 5px;">{{ number_format($igst, 2, '.', '') }}</td>
                                                    </tr>      
                                                @endif    
                                            @else
                                                <tr>
                                                    <td style="width: 50.1%;border-right: 1px solid #000;padding: 0 5px 5px">IGST {{$inv_data->IGST ?? 0}}%</td>
                                                    <td style="width: 49.9%;text-align: right;padding: 0 5px;">0</td>
                                                </tr>    
                                            @endif    
                                            <tr>
                                                <td style="width: 50.1%;border-right: 1px solid #000;padding: 80px 5px 10px">Round Off... </td>
                                                <td style="width: 49.9%;text-align: right;padding: 80px 5px 10px;">
                                                   @php
                                                    $bill_amo = round(number_format((float)$total, 4, '.', ''));
                                                    $round_off = (number_format((float)$bill_amo, 2, '.', ''))-(number_format((float)$total, 4, '.', ''));
                                                    @endphp
                                                    @if($inv_data->account_name == 4 || $inv_data->account_name == 5 )
                                                        @php
                                                            $taxable_amount = (float) ($inv_data->amount ?? 0);
                                                            $cgst = $taxable_amount * ($inv_data->CGST / 100);
                                                            $sgst = $taxable_amount * ($inv_data->SGST / 100);
                                                            $subtotal = $taxable_amount + $cgst + $sgst;

                                                            $rounded_total = round($subtotal);

                                                            $round_off = $rounded_total - $subtotal;
                                                        @endphp
                                                        
                                                        @if($inv_data->CGST && $inv_data->SGST)
                                                            {{ number_format($round_off, 2, '.', '') }}
                                                        @else
                                                            @php
                                                                $igst = $taxable_amount * ($inv_data->IGST / 100);
                                                                $subtotal = $taxable_amount + $igst;
                                                                $rounded_total = round($subtotal);
                                                                $round_off = $rounded_total - $subtotal;
                                                            @endphp
                                                            {{ number_format($round_off, 2, '.', '') }}
                                                        @endif
                                                    @else    
                                                        {{number_format((float)$round_off, 4, '.', '')}}
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 49%;border-right: 1px solid #000;padding: 8px 5px;border-top: 1px solid #000;background: #eee;"><b>Bill Amount </b></td>
                                                <td style="width: 50.1%;text-align: right;padding: 8px 5px;border-top: 1px solid #000;background: #eee;">
                                                    @if($inv_data->account_name == 4 || $inv_data->account_name == 5)
                                                        @php
                                                            $taxable_amount = (float) ($inv_data->amount ?? 0);
                                                            $cgst = $taxable_amount * ($inv_data->CGST / 100);
                                                            $sgst = $taxable_amount * ($inv_data->SGST / 100);
                                                            $igst = $taxable_amount * ($inv_data->IGST / 100);
                                                            $subtotal = $taxable_amount + $cgst + $sgst;
                                                            $rounded_total = round($subtotal);
                                                            $round_off = $rounded_total - $subtotal;
                                                            $total_bill = $subtotal + $round_off;
                                                        @endphp
                                                        @if($inv_data->CGST && $inv_data->SGST)
                                                            {{ number_format($total_bill, 2, '.', '') }}
                                                        @else
                                                            @php
                                                                $subtotal = $taxable_amount + $igst;
                                                                $rounded_total = round($subtotal);
                                                                $round_off = $rounded_total - $subtotal;
                                                                $total_bill = $subtotal + $round_off;
                                                            @endphp
                                                            {{ number_format($total_bill, 2, '.', '') }}        
                                                        @endif
                                                    @else
                                                        <b>
                                                            {{ number_format((float)$bill_amo, 2, '.', '') }}    
                                                        </b>
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            <table cellspacing="0" style="width: 100%;padding: 0;border-bottom: 1px solid #000;">
                                <tr>
                                    <td style="width: 100%;padding: 5px 10px;">
                                        <b>
                                            @php
                                                $f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
                                                $words = $f->format(number_format((float)$bill_amo, 2, '.', ''))
                                            @endphp

                                            @if($inv_data->account_name == 4 || $inv_data->account_name == 5)
                                                @php
                                                    $taxable_amount = (float) ($inv_data->amount ?? 0);
                                                    $cgst = $taxable_amount * ($inv_data->CGST / 100);
                                                    $sgst = $taxable_amount * ($inv_data->SGST / 100);
                                                    $igst = $taxable_amount * ($inv_data->IGST / 100);
                                                    $subtotal = $taxable_amount + $cgst + $sgst;
                                                    if($inv_data->IGST){
                                                        $subtotal = $taxable_amount + $igst;
                                                    }
                                                    $rounded_total = round($subtotal);
                                                    $round_off = $rounded_total - $subtotal;
                                                    $total_bill = $subtotal + $round_off;
                                                    $words = $f->format(number_format((float)$total_bill, 2, '.', ''))
                                                @endphp
                                                
                                                @if($inv_data->CGST && $inv_data->SGST)
                                                    Bill Amount In Words : {{ucwords($words)}}  Rupees Only
                                                @elseif($inv_data->IGST)
                                                    Bill Amount In Words :  {{ucwords($words)}}  Rupees Only
                                                @else    
                                                    Bill Amount In Words : {{ucwords($words)}}  Rupees Only
                                                @endif
                                            @else
                                                Bill Amount In Words : {{ucwords($words)}}  Rupees Only    
                                            @endif  
                                        </b>
                                    </td>
                                </tr>
                            </table>
                            <table cellspacing="0" style="width: 100%;padding: 10px;">
                                <tr>
                                    <td style="width: 80%;">
                                        <h3 style="margin-top: 0;">Notes:</h3>
                                        <table>
                                            <tr>
                                                <td style="width: 10px;">-</td>
                                                <td style="padding-bottom: 10px;">Foreign Exchange Rates are as per Reserve Bank of India (RBI) <br/>Exchange Rates on https://rbi.org.in/.</td>
                                            </tr>
                                            <tr>
                                                <td>-</td>
                                                <td>This is a computer generated invoice.</td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td style="width: 20%;text-align: center;">
                                        <h3 style="margin-top: 0;margin-bottom: 45pt;">For tviStech</h3>
                                        <h4>Authorised Signatory</h4>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="page-break"></div>
    @endforeach    

<style>
    .page-break {
        /*page-break-after: always;*/
    }

    * {
        margin: 0;
        padding: 0;
        font-family: 'Arial', sans-serif;
        font-size: 8.5pt;
        font-weight: normal;
        letter-spacing: normal;
        line-height: 14px;
    }
    table{
        font-family: 'Arial', sans-serif;
        font-weight: normal;
    }
    b{
        font-weight: bold;
    }
    h1 {
        color: #c8102e;
        font-size: 20pt;
        font-style: normal;
        font-weight: bold;
        line-height: 22px;
        margin-bottom: 13px;
        padding: 0;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    h3 {
        color:#000000;
        font-size:10.5pt;
        font-style:normal;
        font-weight:bold;
        line-height:15px;
        margin-bottom:15px;
        margin-top:15px;
        letter-spacing: 1px;
    }
    h4 {
        color:#000000;
        font-size:8.5pt;
        font-style:normal;
        font-weight:bold;
        line-height:13px;
        margin-bottom:0;
        margin-top:0;
        letter-spacing: 1px;
    }

    td {
        vertical-align: top;
    }
    .font-big{
        font-size:13pt;
        font-style:normal;
        font-variant:normal;
        font-weight:normal;
        line-height:13pt;
        margin-bottom:0;
    }
    
    .p-body-sans-3px a {
        color:#000000;
        text-decoration:underline;
    }
    
    .pCenter{text-align: center;}
    .table-top{
        border-bottom: 1px solid #000;
        padding: 0;
    }
    .table-top td{
        padding: 10px;
    }
    .mb-3pt{margin-bottom: 3pt;}
    .priceTable th,.priceTable td{
        border-right: 1px solid #000;
        text-align: center;
        padding: 5px 5px;
    }
    .priceTable th{
        vertical-align: middle;
        font-weight: bold;
        border-bottom: 1px solid #000;
    }
    .amoutPrice td{
        padding: 0 10px;
    }
</style>       
</body>
</html>


