@extends('admin.master')
    <link rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
@section('content')
    <div class="container-fluid"></div>
    <!-- Add  Rate Modal -->
    <div class="modal fade" id="add_rate" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal_title">Rate Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
        
                <!-- Form -->
                <form name="rate_post" id="rate_post" class="form-horizontal">
                    <input type="hidden" name="rate_id" id="rate_id"> 
                    <div class="modal-body">
                        <!-- Error message -->
                        <div class="alert alert-danger print-error-msg" style="display:none">
                            <ul></ul>
                        </div>
                        <div class="print-success-msg alert alert-success" style="display:none"></div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="withdrawal_date">Date</label>
                                        <input type="date" class="form-control" id="rate_date" name="rate_date" value="">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    @foreach($results as $result)
                                        <div class="form-group">
                                            <label>{{strtoupper($result)}} Rate</label>
                                            <input type="number" class="form-control" step="any" id="{{$result}}" name="{{$result}}" value="">
                                        </div>
                                    @endforeach
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="rate_submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End Add Rate Modal -->

    @if ($errors->any())
        <div class="alert alert-danger">There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
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
                <div class="col-12 text-right">
                    <a href="{{ route('rates.create') }}" class="btn btn-primary">Import</a>
                    <a class="btn btn-primary" id="add_rate_btn" data-toggle="modal" data-target="#add_rate">
                        Add Rate
                    </a>
                    <div class="row mb-2">
                        <div class="col-md-3">
                            <select id="fy_filter" class="form-control form-control-sm">
                                <option value="">Select Financial Year</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="month_filter" class="form-control form-control-sm">
                                <option value="">Select Month</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <table id="rates_tbl" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        @foreach($results as $result)
                                            <th>{{strtoupper($result)}}</th>
                                        @endforeach
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($rates as $rate)
                                        <tr>
                                            <td>{{$rate->rate_date}}</td>
                                            @foreach($results as $result)
                                                <td>{{ $rate->$result ?? '-' }}</td>
                                            @endforeach
                                            <td>
                                                <a class="btn btn-primary edit-rate-btn" 
                                                    data-toggle="modal" 
                                                    data-target="#add_rate"
                                                    data-id="{{ $rate->id }}"
                                                    data-rate_date="{{ $rate->rate_date }}"
                                                    @foreach($results as $result)
                                                        data-{{ $result }}="{{ $rate->$result ?? '' }}"
                                                    @endforeach
                                                >
                                                    Edit
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Date</th>
                                        @foreach($results as $result)
                                            <th>{{strtoupper($result)}}</th>
                                        @endforeach
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- DataTables  & Plugins -->
    <script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
    <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
    <script src="{{asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
    <script src="{{asset('plugins/datatables-buttons/js/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js')}}"></script>
    <script src="{{asset('plugins/jszip/jszip.min.js')}}"></script>
    <script src="{{asset('plugins/pdfmake/pdfmake.min.js')}}"></script>
    <script src="{{asset('plugins/pdfmake/vfs_fonts.js')}}"></script>
    <script src="{{asset('plugins/datatables-buttons/js/buttons.html5.min.js')}}"></script>
    <script src="{{asset('plugins/datatables-buttons/js/buttons.print.min.js')}}"></script>
    <script src="{{asset('plugins/datatables-buttons/js/buttons.colVis.min.js')}}"></script>
    <script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.js')}}"></script> <!-- Modal -->
    <script>
        var table;
        var editRow = null;

        const rateFields = @json($results);
        // console.log(rateFields);
        jQuery(document).ready(function ($) {

            /* =========================
               DATATABLE INIT
            ========================== */
            table = $("#rates_tbl").DataTable({
                responsive: true,
                lengthChange: true,
                autoWidth: false,
                destroy: true,
                retrieve: true,
                order: [[0, 'desc']],
                buttons: ["copy", "csv", "excel", "pdf", "colvis"]
            });

            table.buttons().container()
                .appendTo('#rates_tbl_wrapper .col-md-6:eq(0)');

            /* =========================
               ADD RATE BUTTON
            ========================== */
            $('#add_rate_btn').on('click', function () {
                editRow = null;
                $('#modal_title').text('Add Rate');
                $('#rate_submit').text('Save');
                $('#rate_post')[0].reset();
                $('#rate_id').val('');
                $('#rate_date').prop('readonly', false);
                $('.print-error-msg, .print-success-msg').hide();
            });

            /* =========================
               EDIT RATE BUTTON
            ========================== */
            $(document).on('click', '.edit-rate-btn', function () {
                editRow = table.row($(this).closest('tr'));

                let btn = $(this);
                $('#modal_title').text('Edit Rate');
                $('#rate_submit').text('Update');

                $('#rate_id').val(btn.data('id'));
                $('#rate_date').val(btn.data('rate_date')).prop('readonly', true);
                
                rateFields.forEach(function (field) {
                    $('#' + field).val(btn.data(field));
                });

                $('.print-error-msg, .print-success-msg').hide();
            });

            /* =========================
               SUBMIT (ADD / UPDATE)
            ========================== */
            $('#rate_submit').on('click', function (e) {
                e.preventDefault();

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('add_rate') }}",
                    type: "POST",
                    data: $('#rate_post').serialize(),
                    success: function (response) {
                        if (response.error) {
                            printErrorMsg(response.error);
                            return;
                        }

                        let rateId = $('#rate_id').val();
                        let date   = $('#rate_date').val();
                        
                        let fieldValues = {};
                            rateFields.forEach(function (field) {
                                fieldValues[field] = $('#' + field).val();
                            });
                        
                        let actionBtn = `<a class="btn btn-primary edit-rate-btn"
                            data-id="${rateId}"
                            data-rate_date="${date}"
                            data-toggle="modal"
                            data-target="#add_rate">
                            Edit
                        </a>`;
                        Object.keys(fieldValues).forEach(function(field) {
                            let value = fieldValues[field];
                            actionBtn = actionBtn.replace('>', ` data-${field}="${value}">`);
                        });

                        let rowData = [date];
                        rateFields.forEach(function (field) {
                            rowData.push(fieldValues[field]);
                        });

                        rowData.push(actionBtn);
                        if (editRow) {
                            editRow.data(rowData).draw(false);
                        } else {
                            table.row.add(rowData).draw(false);
                        }

                        $('.print-success-msg')
                            .html(response.success)
                            .fadeIn()
                            .delay(2000)
                            .fadeOut();

                        setTimeout(function () {
                            $('#add_rate').modal('hide');
                        }, 300);
                    }
                });
            });

            /* =========================
               ERROR MESSAGE
            ========================== */
            function printErrorMsg(msg) {
                $(".print-error-msg ul").html('');
                $(".print-error-msg").show();
                $.each(msg, function (key, value) {
                    $(".print-error-msg ul").append('<li>' + value + '</li>');
                });
            }

            /* =========================
               MODAL CLEANUP
            ========================== */
            $('#add_rate').on('hidden.bs.modal', function () {
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
            });
            
            /* =========================
               LOAD FY DROPDOWN
            ========================= */
            $.ajax({
                url: "{{ route('rates.index') }}",
                type: "GET",
                data: {
                    type: "fy_list"
                },
                success: function (res) {

                    let fy = $('#fy_filter');

                    fy.find('option:not(:first)').remove();

                    $.each(res, function (key, value) {

                        fy.append(
                            `<option value="${value}">
                                ${value}
                            </option>`
                        );

                    });

                    fy.val("{{ $currentFY }}");

                    loadInitialFY();
                    
                }
            });


            function loadInitialFY() {

                let fy = $('#fy_filter').val();

                if (fy == '') return;

                $.ajax({
                    url: "{{ route('rates.index') }}",
                    type: "GET",
                    data: {
                        type: "months",
                        fy: fy
                    },
                    success: function (res) {

                        let month = $('#month_filter');
                        month.find('option:not(:first)').remove();

                        $.each(res, function (key, value) {
                            month.append(
                                `<option value="${value.value}">
                                    ${value.label}
                                </option>`
                            );
                        });

                        let selectedMonth = "{{ $currentMonth }}";

                        if (res.latest_month) {
                            selectedMonth = res.latest_month;
                        }
                        
                        month.val(selectedMonth).trigger('change');

                    }
                });
            }
            /* =========================
               FY CHANGE
            ========================= */
            $('#fy_filter').on('change', function () {

                let fy = $(this).val();

                if (fy == '') return;

                $.ajax({
                    url: "{{ route('rates.index') }}",
                    type: "GET",
                    data: {
                        type: "months",
                        fy: fy
                    },
                    success: function (res) {

                        let month = $('#month_filter');
                        month.find('option:not(:first)').remove();

                        $.each(res, function (key, value) {
                            month.append(
                                `<option value="${value.value}">
                                    ${value.label}
                                </option>`
                            );
                        });

                        let latestMonth = null;

                        if (res.latest_month) {
                            latestMonth = res.latest_month;
                        } else {
                            // fallback: last option in dropdown
                            latestMonth = month.find('option:last').val();
                        }

                        if (month.find(`option[value="${latestMonth}"]`).length > 0) {
                            month.val(latestMonth);
                        } else {
                            month.prop('selectedIndex', 0);
                        }

                        // Trigger change AFTER setting month so loadRates runs correctly
                        month.trigger('change');

                        loadRates();
                    }
                });

            });

            /* =========================
               MONTH CHANGE
            ========================= */
            $('#month_filter').on('change', function () {
                loadRates();
            });

            /* =========================
               LOAD FILTERED RATES
            ========================= */
            function loadRates() {

                let selectedFY = $('#fy_filter').val();
                let selectedMonth = $('#month_filter').val();

                if (selectedFY == '' || selectedMonth == '') {
                    return;
                }

                $.ajax({

                    url: "{{ route('rates.index') }}",
                    type: "GET",

                    data: {
                        type: "rates",
                        fy: selectedFY,
                        month: selectedMonth
                    },

                    success: function (res) {

                        table.clear();

                        $.each(res, function (key, rate) {

                            let rowData = [];
                            rowData.push(rate.rate_date);
                            rateFields.forEach(function(field){

                                rowData.push(
                                    rate[field] ?? '-'
                                );
                            });

                            let actionBtn = `
                                <a class="btn btn-primary edit-rate-btn"
                                    data-toggle="modal"
                                    data-target="#add_rate"
                                    data-id="${rate.id}"
                                    data-rate_date="${rate.rate_date}">
                                    Edit 
                                </a>
                            `;

                            rateFields.forEach(function(field){

                                let value = rate[field] ?? '';

                                actionBtn = actionBtn.replace(
                                    '>',
                                    ` data-${field}="${value}">`
                                );

                            });

                            rowData.push(actionBtn);
                            table.row.add(rowData);
                        });

                        table.draw(false);
                    }

                });

            }

        });
    </script>

@endsection