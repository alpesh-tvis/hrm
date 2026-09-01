@extends('admin.master')

@section('content')
<link rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">    
<section class="content mailRequest">
    <div class="container-fluid">
       
        @if($leader !='admin')   
        <div class="row ">
            <div class="col-12 text-right">
                <a href="{{ route('request-mail') }}" class="btn btn-primary">Add</a>
            </div>
        </div>
        @endif
    </div>
    <div class="card-body">
        <div class="container-fluid">
            <div class="row mb-2 reasoning">

                @if($leader != 'emp')
                    @if ($user_list->isNotEmpty())
                        <div class="col-2">
                            <label>Employee Name</label>
                            <select name="user_list" class="form-control form-control-sm" id="select_list">
                                <option value="">-- Select --</option>
                                @foreach ($user_list as $user)
                                    <option value="{{ $user['user_id'] }}">{{ $user['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                @endif

                @if ($reasons->isNotEmpty())
                    <div class="col-2">
                        <label>Reasons</label>
                        <select name="reasons" class="form-control form-control-sm" id="reasons">
                            <option value="">-- Select -- </option>
                            @foreach ($reasons as $reason) 
                                <option value="{{$reason['reason']}}">{{$reason['reason']}}</option>
                            @endforeach
                        </select>
                    </div>    
                @endif

                @if ($status->isNotEmpty())
                    <div class="col-2">
                        <label>Status</label>
                        <select name="status" class="form-control form-control-sm" id="status">
                            <option value="">-- Select -- </option>
                            @foreach ($status as $stat) 
                                <option value="{{$stat['status']}}">{{$stat['status']}}</option>
                            @endforeach
                        </select>
                    </div>    
                @endif    
            </div>
        </div>    
        <table id="request_tbl" class="table table-bordered table-striped display">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Date</th>
                    <th>Reason</th>
                    <th>Subject</th>
                    <th>Description</th>
                    <th>Created Date</th>
                    <th>Status</th>
                    @if($leader !='emp') 
                        <th>Action</th>
                        <th style="display: none;" class="request-id">ID</th>
                        <th style="display: none;" class="request-user-id">User ID</th>
                    @endif
                </tr>
            </thead>

            <tbody>
                @foreach($requests as $request)
                    <tr id="row-{{ $request['id'] }}">
                        <td>{{ $request['name'] }}</td>
                        <td>{{ $request['request_date'] }}</td>
                        <td>{{ $request['reason'] }}</td>
                        <td>{{ $request['subject'] }}</td>
                        <td>{{ $request['description'] }}</td>
                        <td>{{ $request['created_at'] }}</td>
                        <td class="status-cell">
                            <p class="{{ $request['status'] == 'Pending' ? 'blink' : '' }}" 
                               style="color: {{ $request['status'] == 'Pending' ? 'orange' : ($request['status'] == 'Approved' ? 'green' : 'red') }}; font-weight: bold;">
                                {{ $request['status'] }}
                            </p>
                        </td>
                        @if($leader !='emp') 
                            <td>
                                <select name="status" class="form-control form-control-sm select_status">
                                    <option value="">--Select--</option>
                                    <option value="pending"   {{ $request['status'] == 'Pending'   ? 'selected' : '' }}>Pending</option>
                                    <option value="approved"  {{ $request['status'] == 'Approved'  ? 'selected' : '' }}>Approve</option>
                                    <option value="cancelled" {{ $request['status'] == 'Cancelled' ? 'selected' : '' }}>Cancel</option>
                                </select>
                            </td>
                            <td class="request-id"      style="display: none">{{ $request['id'] }}</td>
                            <td class="request-user-id" style="display: none">{{ $request['user_id'] }}</td>
                        @endif
                    </tr>
                @endforeach    
            </tbody>
            <tfoot>
                <tr>
                    <th>Name</th>
                    <th>Date</th>
                    <th>Reason</th>
                    <th>Subject</th>
                    <th>Description</th>
                    <th>Created Date</th>
                    <th>Status</th>
                    @if($leader !='emp') 
                        <th>Action</th>
                        <th style="display: none;" class="request-id">ID</th>
                        <th style="display: none;" class="request-user-id">User ID</th>
                    @endif
                </tr>
            </tfoot>
        </table>
    </div>
</section>
@endsection

<!-- DataTables & Plugins -->
<script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
<script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.js')}}"></script>

<!-------sweetalert---->
<link rel="stylesheet" href="{{asset('sweet-alert/css/sweetalert2.min.css')}}">
<script src="{{asset('sweet-alert/js/sweetalert2.min.js')}}"></script>

<style type="text/css">
.request-id, .request-user-id {
    display: none;
}
@keyframes blink {
    0%   { opacity: 1; }
    50%  { opacity: 0; }
    100% { opacity: 1; }
}
.blink {
    animation: blink 1s infinite;
}
</style>

<script type="text/javascript">
    jQuery(function ($) {

        var leader = "<?php echo $leader ?>";

        var table = $("#request_tbl").DataTable({
            language: {
                emptyTable:     "No mail request available in table",  
                loadingRecords: "Please wait ..", 
                zeroRecords:    "No matching records found"
            },
            "paging":       false,
            "responsive":   true,
            "lengthChange": false,
            "autoWidth":    true,
            "searchable":   true,
            "pageLength":   10,
            "order":        [[1, 'desc']]
        });

        // ── Find column indexes dynamically ───────────────────
        var UserIdColIndex  = -1;
        var ReasonIndex     = -1;
        var StatusIndex     = -1;

        $("#request_tbl thead th").each(function (i) {
            var text = $(this).text().trim();
            if (text === "User ID")  UserIdColIndex = i;
            if (text === "Reason")   ReasonIndex    = i;
            if (text === "Status")   StatusIndex    = i;
        });

        // ── Employee Name Filter (matches hidden User ID column) ──
        if ($("#select_list").length && UserIdColIndex !== -1) {
            $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
                var selectedUserId = $('#select_list').val();
                if (selectedUserId === "") return true;

                var rowUserId = data[UserIdColIndex] ? data[UserIdColIndex].trim() : '';
                return rowUserId === String(selectedUserId);
            });

            $("#select_list").change(function () {
                table.draw();
            });
        }

        // ── Reason Filter ──────────────────────────────────────
        if (ReasonIndex !== -1) {
            $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
                var selected = $('#reasons').val();
                if (selected === "") return true;
                return data[ReasonIndex].includes(selected);
            });

            $("#reasons").change(function () {
                table.draw();
            });
        }

        // ── Status Filter ──────────────────────────────────────
        if (StatusIndex !== -1) {
            $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
                var selected = $('#status').val();
                if (selected === "") return true;
                return data[StatusIndex].includes(selected);
            });

            $("#status").change(function () {
                table.draw();
            });
        }

        // ── Status Update (Action column) ─────────────────────
        $(document).on('change', '.select_status', function () {
            var current_val = $(this).val();

            if (!current_val) {
                Swal.fire({
                    title: 'Error!',
                    text:  'Please select a valid status.',
                    icon:  'error',
                    confirmButtonText: 'Try Again'
                });
                return;
            }

            var row     = table.row($(this).closest('tr'));
            var rowData = row.data();
            var row_id  = rowData[8]; // hidden ID column

            $.ajax({
                url:    `/mail-request/${row_id}`,
                method: 'PUT',
                data: {
                    _token:  $('meta[name="csrf-token"]').attr('content'),
                    status:  current_val,
                    confirm: 'no'
                },
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            title:              'Are you sure?',
                            text:               response.message,
                            icon:               'question',
                            showCancelButton:   true,
                            confirmButtonText:  'Yes',
                            cancelButtonText:   'No',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $.ajax({
                                    url:    `/mail-request/${row_id}`,
                                    method: 'PUT',
                                    data: {
                                        _token:  $('meta[name="csrf-token"]').attr('content'),
                                        status:  current_val,
                                        confirm: 'yes'
                                    },
                                    success: function (response) {
                                        if (response.success) {

                                            // Update status cell color & text
                                            var color = current_val === 'approved' ? 'green' : (current_val === 'pending' ? 'orange' : 'red');
                                            $(`#row-${row_id}`).find('.status-cell').html(
                                                `<p style="color:${color}; font-weight:bold;">${ucfirst(current_val)}</p>`
                                            );

                                            Swal.fire({
                                                title:             'Success!',
                                                text:              response.message,
                                                icon:              'success',
                                                confirmButtonText: 'OK'
                                            });

                                            // Update action dropdown in DataTable row
                                            rowData[7] = `
                                                <select name="status" class="form-control form-control-sm select_status">
                                                    <option value="">--Select--</option>
                                                    <option value="pending"   ${current_val === 'pending'   ? 'selected' : ''}>Pending</option>
                                                    <option value="approved"  ${current_val === 'approved'  ? 'selected' : ''}>Approve</option>
                                                    <option value="cancelled" ${current_val === 'cancelled' ? 'selected' : ''}>Cancel</option>
                                                </select>`;
                                            row.data(rowData).draw();

                                        } else {
                                            Swal.fire({ title: 'Error!', text: 'Failed to update.', icon: 'error', confirmButtonText: 'Try Again' });
                                        }
                                    },
                                    error: function () {
                                        Swal.fire({ title: 'Error!', text: 'An error occurred while updating.', icon: 'error', confirmButtonText: 'Try Again' });
                                    }
                                });
                            }
                        });
                    } else {
                        Swal.fire({ title: 'Error!', text: 'Failed to update status.', icon: 'error', confirmButtonText: 'Try Again' });
                    }
                },
                error: function () {
                    Swal.fire({ title: 'Error!', text: 'An error occurred while updating the status.', icon: 'error', confirmButtonText: 'Try Again' });
                }
            });
        });

    });

    function ucfirst(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }
</script>
