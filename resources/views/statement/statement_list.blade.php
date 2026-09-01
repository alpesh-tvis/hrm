@extends('admin.master')

@php
    
    if(!empty($stat)){
        if(!empty($get_largest_date) || !empty($get_smallest_date)){
            $lowest_date = $get_smallest_date->date;
            $ex_month = explode('-', $get_smallest_date->date);
            $ex_month1 = explode('-', $get_largest_date->date);

            if ($ex_month[1] <= '3') {
                $startdate = $ex_month[0]-1;
            } else {
                $startdate = $ex_month[0];
            }

            if ($ex_month1[1] <= '3') {
                $enddate = $ex_month1[0];
            } else {
                $enddate = $ex_month1[0] + 1;
            }
    
            $years = range($startdate, $enddate);

            if(request()->has('financial_year')){
                $c_year = request()->financial_year;
            }else{
                    $c_year = date('Y');
                
            }                                                        
        }    
    }
@endphp

<link rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">

@section('content')
    <div class="container-fluid">
        <div class="row px-4 pt-3 justify-content-between pb-0">
            <a href="{{ route('importStatement.create') }}" class="btn btn-primary mr-2">Import</a>
            <a href="{{ route('add_invoice') }}" class="btn btn-primary">Add Invoice</a>
        </div>
    </div>
    
    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            {{ $message }}
        </div>
    @endif
    
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <input type="text" name="daterange"  readonly />
                                </div>    
                                <div class="col-md-6">
                                    <input type="hidden" name="fin_year" id="fin_year" value="{{$c_year}}">
                                    
                                    @if(!empty($get_largest_date) || !empty($get_smallest_date))
                                        <form action="{{route('importStatement.index')}}" method="get" class="form-inline justify-content-md-end mb-0">
                                            <div class="form-group">
                                                <label for="financial_year" class="pr-2"></label>
                                                <select class="form-control mr-2" id="financial_year" name="financial_year">
                                                    <option value="">Select Financial Year</option>
                                                    @foreach ($years as $year)
                                                        @if ((substr($year, -2) + 1) <= substr($enddate, -2))
                                                            <option value="{{$year}}" >{{$year . ' - ' . (substr($year, -2) + 1)}}</option> 
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            
                                            <button type="submit" id="fin_submit"  class="btn btn-primary form-control">Submit</button>
                                        </form>
                                    @endif
                                </div>       
                            </div>

                            <form action="{{route('multi_select')}}" method="POST">
                                @csrf
                                <div class="btn-group mb-3 align-items-center">
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#multi_select">
                                        Add Withdrwal Rate
                                    </button>
                                    <button type="button" id="generate_pdf" class="btn btn-primary ">Generate PDF</button>
                                    <button type="button" id="generate_zip" class="btn btn-primary mr-2 ">Download Zip</button>
                                    <input type="checkbox" name="print_header" id="print_header" value="" class="mr-1"><span>Print Header?</span>
                                </div>
                                
                                <div class="modal fade" id="multi_select" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="alert alert-danger" style="display:none"></div>
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="multi_select_title">Withdrawal Details</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="withdrawal_date">Withdrawal Date</label>
                                                                    <input type="date" class="form-control" id="withdrawal_date" name="withdrawal_date" required>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="withdrawal_rate">Withdrawal Rate</label>
                                                                    <input type="number" class="form-control" step="any" id="withdrawal_rate" name="withdrawal_rate" required>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" id="w_submit" class="btn btn-primary">Submit</button>
                                                </div>
                                            </div>
                                        </div>
                                </div>

                                <div class="table-responsive"> 
                                    <table id="stat_tbl" class="table table-bordered table-striped" style="width:100%">
                                        <thead>
                                            <tr class="table-header">
                                                <th>All<input type="checkbox" id="select_all"></th>
                                                <th>Date</th>
                                                <th class="bill_date">Bill Date</th>
                                                <th class="rate_date">Rate Date</th>
                                                <th >Bill No</th>
                                                <th>Ref ID</th>
                                                <th>Type</th>
                                                <th>Team</th>
                                                <th>Description</th>
                                                <th>Acc Name</th>
                                                <th id="amo_total">Amo($)</th>
                                                <th>Rate</th>
                                                <th id ="total">Total</th>
                                                <th id="bill_ammout_total">Bill Amo</th>
                                                <th>Round</th>
                                                <th>W Date</th>
                                                <th class="w_rate">W Rate</th>
                                                <th id="w_amo">W Amo</th>
                                            </tr>
                                        </thead>
                                        <tbody> 
                                            @foreach($stat as $data)
                                                    @php
                                                        $currencyColumn = $data->currency_column;
                                                        $check_exits_date = $ratesByDate[$data->billing_date] ?? null;
                                                        $date = $data->billing_date;
                                                        $check_exits_date1 = $data->check_exits_date1;

                                                        if(!empty($check_exits_date->$currencyColumn)){
                                                            $bill_date = date('d-m-Y',strtotime($check_exits_date->rate_date));
                                                        }else if(!empty($data->check_exits_date1)){
                                                            $bill_date = date('d-m-Y',strtotime($data->check_exits_date1->rate_date));
                                                        }else{
                                                            $bill_date = date('d-m-Y',strtotime($data->billing_date));
                                                        }
                                                        $dateString = $data->billing_date;
                                                        $date1 = \Carbon\Carbon::createFromFormat('Y-m-d', $dateString);
                                                    @endphp
                                                    <tr> 
                                                        
                                                        <td class="all">

                                                            @if($data->show_checkbox)
                                                                <input type="checkbox" name="multi_id[]" value="{{$data->ref_id}}" class="select_checkbox" data-acname="{{$data->accountname}}" date-attr= "{{ $bill_date }}">
                                                                
                                                            @endif
                                                        </td>   
                                                        <td class="date">{{date('d-m-Y',strtotime($data->date))}}</td>
                                                        <td class="bill_date">
                                                            @php
                                                                $isSpecialType = in_array($data->type, [
                                                                    'Withdrawal Fee','Withdrawal','Membership Fee','Payment'
                                                                ]);

                                                                $isMarchEnd = $date1->format('m-d') === '03-31';

                                                                $rate  = $check_exits_date ?? null;
                                                                $rate1 = $data->check_exits_date1 ?? null;

                                                                // default
                                                                $finalDate = $data->billing_date;

                                                                if (!$isMarchEnd) {

                                                                    if ($isSpecialType) {
                                                                        $finalDate = $rate->rate_date ?? $rate1->rate_date ?? $data->billing_date;

                                                                    } else {

                                                                        if (!empty($rate->$currencyColumn)) {
                                                                            $finalDate = $rate->rate_date;

                                                                        } elseif (!empty($rate1->rate_date)) {

                                                                            if (strtotime($data->date) < strtotime($cutoffDate)) {
                                                                                $finalDate = $rate1->rate_date;
                                                                            } else {
                                                                                $finalDate = $data->billing_date;
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            @endphp

                                                            {{ date('d-m-Y', strtotime($finalDate)) }}
                                                        </td>
                                                        <td class="rate_date">{{ date('d-m-Y',strtotime($check_exits_date1->rate_date)) ?? 'N/A' }} 
                                                            </td>    
                                                        <td class="bill_no">
                                                            {{ $data->ti_label }}
                                                            {{ $data->wt_ti_label }}
                                                            {{ $data->local_ti_label }}
                                                            {{ $data->gst_label }}
                                                            {{ $data->membership_gst_label }}
                                                            {{ $data->com_label }}
                                                            {{ $data->wht_label }}
                                                            {{ $data->withdrawal_label }}
                                                            {{ $data->withdrawal_fee_label }}
                                                            {{ $data->payment_label }}
                                                            {{ $data->mf_label }}
                                                            {{ $data->adj_label }}
                                                            {{ $data->amf_label }}
                                                            {{ $data->refund_label }}
                                                            {{ $data->refund_label_cn }}
                                                        
                                                            @if(str_contains($data->description, "Service Fee return for Refund"))
                                                                @php
                                                                    $ref_num = explode('Ref ID',$data->description);
                                                                    $ref_num_trim = trim($ref_num[1]);
                                                                @endphp
                                                                @if(array_key_exists($ref_num_trim, $type_refund_no_arr))
                                                                    CN{{$type_refund_no_arr[$ref_num_trim] ?? ''}}-TDS{{$type_refund_no_arr1[$data->ref_id] ?? ''}}
                                                                @endif    
                                                            @endif

                                                            @if(str_contains($data->description, "return for Refund") && $data->type=="WHT")
                                                                @php
                                                                    $ref_num = explode('Ref ID',$data->description);
                                                                    $ref_num_trim = trim($ref_num[2]);
                                                                @endphp

                                                                @if(array_key_exists($ref_num_trim, $type_refund_no_arr))
                                                                    CN{{$type_refund_no_arr[$ref_num_trim]}}-TDS{{$type_refund_no_arr1[$data->ref_id] ?? ''}}
                                                                @endif    
                                                            @endif
                                                        </td>
                                                        <td class="ref_ids">{{$data->ref_id}}</td>
                                                        <td class="type">{{ $data->display_type }}</td>
                                                        <td class="team">{{$data->company}}</td>
                                                        <td class="description">{{$data->description}}</td>
                                                        <td class="acc-name">{{ $data->display_name }}</td>
                                                        <td class="text-right amo">{{ $data->formatted_amount }}</td>
                                                        <td class="text-right rate">{{ $check_exits_date1->$currencyColumn}}</td>
                                                        <td class="text-right total">
                                                            {{number_format((float)$check_exits_date1->$currencyColumn*$data->amount, 4, '.', '')}}
                                                        </td>
                                                        <td class="bill_ammout">
                                                            {{ number_format(round((float)$check_exits_date1->$currencyColumn * $data->amount), 2, '.', '') }}
                                                        </td>
                                                        @php
                                                            $value = (float)$check_exits_date1->$currencyColumn * $data->amount;

                                                            $exact = round($value, 4);
                                                            $rounded = round($value);

                                                            $diff = $exact - $rounded;
                                                        @endphp
                                                        <td class="round">{{ number_format($diff, 4, '.', '') }}</td>
                                                        <td class="w_date">
                                                            @if($data->withdrawal_date)
                                                                {{date('d-m-Y',strtotime($data->withdrawal_date))}}
                                                                <a type="button"  class="btn btn-danger"  onclick="return confirm('Are you sure?')" href="/delete_w_rate/{{$data->id}}">
                                                                    <i class="fa fa-trash"></i>
                                                                </a>
                                                            @endif
                                                        </td>
                                                        <td class="w_rate {{ empty($data->withdrawal_rate) ? 'blank_wrate' : '' }}">{{ $data->withdrawal_rate ?? '-' }}</td>
                                                        <td class="w_amo">
                                                            @if($data->withdrawal_rate)
                                                                {{number_format((float)$data->amount*$data->withdrawal_rate, 2, '.', '')}}
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            
                                        </tbody>
                                        <tfoot>
                                            <tr class="table-header">
                                                <th></th>
                                                <th>Date</th>
                                                <th>Bill Date</th>
                                                <th>Rate Date</th>
                                                <th>Bill No</th>
                                                <th>Ref ID</th>
                                                <th>Type</th>
                                                <th>Team</th>
                                                <th>Description</th>
                                                <th>Acc Name</th>
                                                <th id="amo_total">Amo($)</th>
                                                <th>Rate</th>
                                                <th id ="total">Total</th>
                                                <th id="bill_ammout_total">Bill Amo</th>
                                                <th>Round</th>
                                                <th>W Date</th>
                                                <th>W Rate</th>
                                                <th id="w_amo">W Amo</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- <script src="{{asset('plugins/jquery/jquery.min.js')}}"></script> -->
    <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{asset('plugins/datatables-buttons/js/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js')}}"></script>
    <script src="{{asset('plugins/jszip/jszip.min.js')}}"></script>
    <script src="{{asset('plugins/datatables-buttons/js/buttons.html5.min.js')}}"></script>
    <script src="{{asset('plugins/datatables-buttons/js/buttons.print.min.js')}}"></script>
    <script src="{{asset('plugins/datatables-buttons/js/buttons.colVis.min.js')}}"></script>
    <script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.js')}}"></script> <!-- Modal -->
    <!-- <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script> -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <script>
        jQuery(document).ready(function ($) {
            jQuery(function () {
                function cbDropdown(column) {
                    // console.log(column);
                    return $('<ul>', {
                        'class': 'cb-dropdown'
                     }).appendTo($('<div>', {
                        'class': 'cb-dropdown-wrap'
                     }).appendTo(column));
                }

                var table = $("#stat_tbl").DataTable({
                    rowCallback: function (row, data) {
                        setTimeout(function(){
                            // console.log(row);
                            //Bill Amount column
                            var total_bill_ammout = 0;
                            $('#stat_tbl .bill_ammout').each(function(){
                               total_bill_ammout += parseFloat($(this).text());
                            });
                            $('#bill_ammout_total').text("Bill Amo: "+total_bill_ammout.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));

                            //Amo Column
                            var amo_total = 0;
                            $('#stat_tbl .amo').each(function(){
                               amo_total += parseFloat($(this).text());
                            });
                            $('#amo_total').text("Amo: "+amo_total.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));

                            //Total column
                            var total = 0;
                            $('#stat_tbl .total').each(function(){
                               total += parseFloat($(this).text());
                            });
                            $('#total').text("Total: "+total.toFixed(4).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));

                            //W Amo
                            var w_amo = 0;
                            $('#stat_tbl .w_amo').each(function(){
                               w_amo += parseFloat($(this).text());
                            });
                            $('#w_amo').text("W Amo: "+w_amo.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
                        },500);
                    },
                    initComplete: function() {
                        this.api().columns([2,6,7,9,14,16]).every(function() {
                            $( '.cb-dropdown-wrap' ).click( function(e) {
                                e.stopPropagation();
                            });

                            var column = this;
                            var ddmenu = cbDropdown($(column.header())).on('change', ':checkbox', function() {
                                var active;
                                var vals = $(':checked', ddmenu).map(function(index, element) {
                                    active = true;
                                    return $.fn.dataTable.util.escapeRegex($(element).val());
                                }).toArray().join('|');
                                
                                if(vals=== 'blank'){
                                    vals='-';
                                }
                                
                                // console.log(vals);
                                column.search(vals.length > 0 ? '^(' + vals + ')$' : '', true, false).draw();

                                // Highlight the current item if selected.
                                if (this.checked) {
                                    $(this).closest('li').addClass('active');
                                } else {
                                  $(this).closest('li').removeClass('active');
                                }

                                // Highlight the current filter if selected.
                                var active2 = ddmenu.parent().is('.active');

                                if (active && !active2) {
                                    ddmenu.parent().addClass('active');
                                } else if (!active && active2) {
                                    ddmenu.parent().removeClass('active');
                                }
                            });
                            
                            if (column[0][0] == 2) {
                                var data = column.data().unique();
                            } else {
                                var data = column.data().unique().sort();
                            }

                            ddmenu.append($('<li class="stat_all"><label><span>All</span><input type="checkbox" value=""></label></li>'));

                            data.each(function(d, j) {
                                // console.log(d);
                                if(d=== '-'){
                                    d='blank';
                                }
                                var // wrapped
                                $label = $('<label>'),
                                $text = $('<span>', {
                                    html: d
                                }),

                                $cb = $('<input>', {
                                    type: 'checkbox',
                                    value: d
                                });
                                $text.appendTo($label);
                                $cb.appendTo($label);
                                ddmenu.append($('<li>').append($label));
                            });

                            $('.cb-dropdown').each(function(){
                                var selectAll = $(this).find('li.stat_all input');
                                var selectSingle = $(this).find('li').not('.stat_all');
                                var checkboxGroupLi = $(this).find("li");
                                var stat_all = $(this).find('.stat_all')

                                selectSingle.find('input').change(function() {
                                    if (selectSingle.find('input:checked').length === selectSingle.find('input').length) {
                                        selectAll.prop('checked', true);
                                        checkboxGroupLi.addClass('active');
                                    } else {
                                        selectAll.prop('checked', false);
                                        stat_all.removeClass('active');
                                    }
                                });

                                // Check or uncheck other checkboxes based on "Select All"
                                selectAll.change(function() {
                                    if ($(this).prop('checked')) {
                                        selectSingle.find('input').prop('checked', true);
                                        checkboxGroupLi.addClass('active');
                                    } else {
                                        selectSingle.find('input').prop('checked', false);
                                        checkboxGroupLi.removeClass('active');
                                    }
                                });
                            });
                        });
                    },
                    "lengthMenu": [[-1,500, 1000,], ["All", 500, 1000,]], 
                    "responsive": true, "lengthChange": false, "autoWidth": false,
                    scrollX: false,
                    scrollCollapse: true,
                    "order": [5, 'desc'],
                    "bPaginate": true,
                    "searching": true,
                    "columnDefs": [
                        {
                            "targets": [0],
                            "orderable": false,
                        },
                        {
                            "targets": [1,8],
                            "visible": false
                        },
                    ],
                    "buttons": ["copy", "csv", "excel", "pdf", "colvis"]
                }).buttons().container().appendTo('#stat_tbl_wrapper .col-md-6:eq(0)');
                 
                // Select All checkbox
                $("#select_all").click(function () {
                    $(".select_checkbox").prop('checked', $(this).prop('checked'));
                });

                $('.select_checkbox').click(function(){
                    if($(".select_checkbox").length == $(".select_checkbox:checked").length) { 
                        //if the length is same then untick 
                        $("#select_all").prop("checked", true);
                    }else {
                        $("#select_all").prop("checked", false);            
                    }
                });

                $("#generate_pdf").click(function () {
                    var pdf_id = $(this).attr('id');
                    if($("#print_header").is(':checked')){
                        // console.log('yes');
                        cheked_header = 'cheked_headers';
                    }
                    else{
                        cheked_header = 'uncheked_header';
                    }
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });    
                
                    var allVals = [];
                    var ac_name = [];
                    $('.select_checkbox:checked').each(function(){
                        allVals.push($(this).val());
                        ac_name.push($(this).data('acname'));
                    });
                    var year = $("#fin_year").val();
                    $.ajax({
                        method: 'post',
                        url: '/generate_pdf',
                        data: {'ref_id': allVals, 'fin_year' : year, 'print_header' : cheked_header, 'pdf_id' : pdf_id, 'ac_name' : ac_name },
                        dataType: "json",
                        beforeSend: function() {
                            $(".spinner-wrapper").show();
                        },
                        success: function(data) {
                            // console.log(data.stat_ids);
                            if(data.pdf)
                            {
                                // window.open('https://hrm.stgdeven.com/print?pdf='+data.stat_ids+'', '_blank');
                                let url = "{{ route('print') }}?pdf=" + data.stat_ids;
                                //window.open(url, '_blank');
                                window.open(data.pdf + '?pdf=' + data.stat_ids, '_blank');
                                $('.select_checkbox').prop('checked', false);
                            }
                            $(".spinner-wrapper").hide();
                        }
                    });
                });

                $("#generate_zip").click(function () {
                    var pdf_id = $(this).attr('id'); 
                    if($("#print_header").is(':checked')){
                        cheked_header = 'cheked_headers';
                    }
                    else{
                        cheked_header = 'uncheked_header';
                    }
                    var year = $("#fin_year").val();
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });    
                
                    var allVals = [];
                    var ac_name = [];
                    $('.select_checkbox:checked').each(function(){
                        allVals.push($(this).val());
                        ac_name.push($(this).data('acname'));
                    });

                    $.ajax({
                        method: 'post',
                        url: '/generate_pdf',
                        data: {'ref_id': allVals,'fin_year' : year,'print_header' : cheked_header, 'pdf_id' : pdf_id, 'ac_name' : ac_name },
                        dataType: "json",
                        beforeSend: function() {
                            $(".spinner-wrapper").show();
                        },
                        success: function(data) {
                            if(data.pdf)
                            {
                                window.open(data.pdf + '?pdf=' + data.stat_ids, '_blank');
                                $('.select_checkbox').prop('checked', false);
                            }
                            $(".spinner-wrapper").hide();
                        }
                    });
                });    
            });
         });

        $(document).ready(function(){
            $(function() {
                var s_val = $("#fin_year").val();
                var m = $('#financial_year option[value="'+s_val+'"]').prop('selected', true);
                $('input[name="daterange"]').daterangepicker({
                    opens: 'center',
                    locale: {
                        format: 'DD/MM/YYYY'
                    }
                });
                // End Datepicker

                // $(".applyBtn").click(function () {
                $(document).on('click','.applyBtn',function(){
                    $("tr:not('.table-header')").removeClass('show_row');//hide all rows
                    drp_selected = $('.drp-selected').text();
                    result = $(drp_selected.split('-'));
                    day = 1000*60*60*24;
                    date1e = result[0].split('/');
                    date2e = result[1].split('/');
                    date1 = new Date(date1e[2].split(/\s/).join('')+'-'+date1e[1]+'-'+date1e[0]);
                    date2 = new Date(date2e[2].split(/\s/).join('')+'-'+date2e[1]+'-'+date2e[0].split(/\s/).join(''));
                    var diff = (date2.getTime()- date1.getTime())/day;
                    
                    for(var i=0;i<=diff; i++)
                    {
                        var xx = date1.getTime()+day*i;
                        var yy = new Date(xx);
                            
                        if(yy.getMonth() < 10){
                            getmonth = "0"+(yy.getMonth()+1);
                        }   
                        else{
                            getmonth = (yy.getMonth()+1);
                        }

                        if(yy.getDate() < 10){
                            getdate = "0"+yy.getDate();
                        }
                        else{
                            getdate = yy.getDate();
                        }
                            
                        var year = yy.getFullYear();
                        var full_date = getdate+"-"+getmonth+"-"+year;
                        var attr  = $('.select_checkbox').attr('date-attr');
                            
                        $("tr:not('.table-header')").hide();//hide all rows
                        $('.select_checkbox[date-attr="'+full_date+'"]').each(function(i){
                            $(this).closest('tr').addClass('show_row');
                            $(this).prop('checked', true);
                        });

                        //Bill Amount column
                        var total_bill_ammout = 0;
                        $('#stat_tbl tr.show_row .bill_ammout').each(function(){
                           total_bill_ammout += parseFloat($(this).text());
                        });

                        $('#bill_ammout_total').text("Bill Amo: "+total_bill_ammout.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));

                        //Amo Column
                        var amo_total = 0;
                        $('#stat_tbl tr.show_row .amo').each(function(){
                           amo_total += parseFloat($(this).text());
                        });
                        $('#amo_total').text("Amo: "+amo_total.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));

                        //Total column
                        var total = 0;
                        $('#stat_tbl tr.show_row .total').each(function(){
                            total += parseFloat($(this).text());
                        });
                        $('#total').text("Total: "+total.toFixed(4).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));

                        //W Amo
                        var w_amo = 0;
                        $('#stat_tbl tr.show_row .w_amo').each(function(){
                            w_amo += parseFloat($(this).text());
                        });
                        $('#w_amo').text("W Amo: "+w_amo.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
                    }
                });

                $(document).on('click','.cancelBtn',function(){
                    $('#stat_tbl tr').removeClass('show_row');
                    $("tr:not('.table-header')").show();
                    $(".select_checkbox").prop('checked', false);
                    
                    var today_date = new Date();
                    $('input[name="daterange"]').val('');
                    $('input[name="daterange"]').daterangepicker({ startDate: today_date, endDate: today_date });
                    
                    //Bill Amount column
                    var total_bill_ammout = 0;
                    $('#stat_tbl .bill_ammout').each(function(){
                        total_bill_ammout += parseFloat($(this).text());
                    });
                    $('#bill_ammout_total').text("Bill Amo: "+total_bill_ammout.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));

                    //Amo Column
                    var amo_total = 0;
                    $('#stat_tbl .amo').each(function(){
                        amo_total += parseFloat($(this).text());
                    });
                    $('#amo_total').text("Amo: "+amo_total.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));

                    //Total column
                    var total = 0;
                    $('#stat_tbl .total').each(function(){
                        total += parseFloat($(this).text());
                    });
                    $('#total').text("Total: "+total.toFixed(4).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));

                    //W Amo
                    var w_amo = 0;
                    $('#stat_tbl .w_amo').each(function(){
                        w_amo += parseFloat($(this).text());
                    });
                    $('#w_amo').text("W Amo: "+w_amo.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
                });
            });
        });
    </script>
    
    <style type="text/css">
        .cb-dropdown-wrap {
            max-height: 80px; 
            position: relative;
            height: 30px;
            width: max-content;
        }
        .cb-dropdown,
        .cb-dropdown li {
            margin: 0 5px;
            padding: 0;
            list-style: none;
        }
        .cb-dropdown {
            position: relative;
            z-index: 1;
            width: 100%;
            height: 100%;
            overflow: hidden;
            background: #fff;
            border: 1px solid #888;
        }
        .cb-dropdown-wrap:hover .cb-dropdown {
            height: auto;
            overflow: auto;
            max-height: 200px;
            transition: 0.2s height ease-in-out;
        }
        .cb-dropdown li.active {
            background: #6c757d;
        }
        .cb-dropdown li.active label span
        {
            color: #fff;
        }
        .cb-dropdown li label {
            display: block;
            position: relative;
            cursor: pointer;
            line-height: 2; 
            margin: 0;
        }
        .cb-dropdown li label > input {
            position: absolute;
            left: 0;
            top: 6;
            width: 16px;
        }
        .cb-dropdown li label > span {
            display: block;
            margin-left: 20px;
            margin-right: 0px; 
            font-family: sans-serif;
            font-size: 0.8em;
            font-weight: normal;
            text-align: left;
        }
        .table-fit {
            width: 1px;
        }
        .dataTables_scrollHead{
            overflow: inherit !important;
        }
        .cb-dropdown-wrap{
            height: 30px;
        }
        .cb-dropdown-wrap .cb-dropdown {
           margin: 0;
        }
        .dataTables_scrollBody table {
           width: 100%;
        }
        .show_row
        {
            display: table-row !important;
        }
        .cb-dropdown li label > span.b 
        {
            display: none;
        }
    </style>
@endsection