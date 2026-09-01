@extends('admin.master')

<link rel="stylesheet" href="{{asset('public/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{route('multi_select')}}" method="POST">
                                @csrf
                                <div class="table-responsive"> 
                                    <table id="stat_tbl" class="table table-bordered table-striped" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>date</th>
                                                <th>date</th>
                                                <th>date</th>
                                                <th>date</th>
                                                <th>date</th>
                                                <th>date</th>
                                                <th>date</th>
                                                <th>date</th>
                                            </tr>
                                        </thead>
                                        <tbody> 
                                        </tbody>
                                    </table>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://hrm.tvistech.com/public/plugins/jquery/jquery.min.js"></script>
<script src="https://hrm.tvistech.com/public/plugins/datatables/jquery.dataTables.min.js"></script>
<script type="text/javascript">
    jQuery(function ($) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var table = $('#stat_tbl').DataTable({
            "order": [],
            processing: true,
            serverSide: true,
            ajax: "{{ route('ajax_statement') }}",
            "lengthMenu": [[-1,500, 1000,], ["All", 500, 1000,]],
            initComplete: function() {
                // columns: [
                //     {data: 'date', name: 'date'},
                // ]
            }   
        });
    });        
</script>