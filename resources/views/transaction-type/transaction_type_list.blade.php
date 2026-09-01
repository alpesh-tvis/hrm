@extends('admin.master')
<link rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
@section('content')

    <div class="container-fluid">
        
    </div>
    @if ($message = Session::get('success'))
    <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        {{ $message }}
    </div>
    @endif

<section class="content">
    <div class="row mb-2">
            <div class="col-12 text-right">
                <a href="{{ route('transaction-type.create') }}" class="btn btn-primary">Add</a>
            </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    
                    
                    <div class="card-body">
                        <table id="transaction_type" class="table table-bordered table-striped">
                            
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <!-- <th>Action</th> -->
                                </tr>
                            </thead>
                            
                            <tbody>
                                @foreach ($TransactionType as $type)
                                <tr>
                                    <td>{{$type->type}}</td>
                                    
                                    <!-- <td>
                                        <a href="{{ route('transaction-type.edit',[$type->id]) }}" class="btn btn-block btn-secondary" style="display:inline">Edit</a>
                                        <form action="{{ route('transaction-type.destroy',$type->id) }}" method="post" style="display:inline">
                                            {{csrf_field()}}
                                            <input name="_method" type="hidden" value="DELETE">
                                            <button class="btn btn-danger" type="submit" onclick="return confirm('Are you sure you want to delete this Type?')">Delete</button>
                                        </form>
                                    </td>
                                    -->
                                </tr>
                                @endforeach
                            </tbody>
                            
                            <tfoot>
                            <tr>
                                <th>Type</th>
                                <!-- <th>Action</th> -->
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
<script>
jQuery(function ($) {
$("#transaction_type").DataTable({
"responsive": true, "lengthChange": false, "autoWidth": false,
"buttons": ["copy", "csv", "excel", "pdf", "colvis"]
}).buttons().container().appendTo('#transaction_type_wrapper .col-md-6:eq(0)');

});
</script>
<!-- AdminLTE App -->

@endsection