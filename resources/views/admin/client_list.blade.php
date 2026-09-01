@extends('admin.master')
<link rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
@section('content')

    <div class="container-fluid">
        <div class="row">
            <div class="col-12 text-right pr-4 mt-4">
                <a href="{{ route('client.create') }}" class="btn btn-primary">Add</a>
            </div>
        </div>
    </div>
    @if ($message = Session::get('success'))
    <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        {{ $message }}
    </div>
    @endif

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
              
                    
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-striped">
                            
                            <thead>
                                <tr>
                                    <th>Company</th>
                                    <th>Billing Name</th>
                                    <th>Billing Email</th>
                                    <th>First Name</th>
                                    <th>Last name</th>
                                    <th>Personal Email</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            
                            <tbody>
                                @foreach ($cli as $data)
                                <tr>
                                    <td>{{$data->company}}</td>
                                    <td>{{$data->b_name}}</td>
                                    <td>{{$data->b_email}}</td>
                                    <td>{{$data->first_name}}</td>
                                    <td>{{$data->last_name}}</td>
                                    <td>{{$data->email}}</td>
                                    <td class="dFlex">
                                        <a href="{{ route('client.edit',[$data->id]) }}" class="btn  btn-primary" style="display:inline">Edit</a>
                                        <form action="{{ route('client.destroy',$data->id) }}" method="post" style="display:inline" class="margin0">
                                            {{csrf_field()}}
                                            <input name="_method" type="hidden" value="DELETE">
                                            <button class="btn btn-danger" type="submit" onclick="return confirm('Are you sure you want to delete this Client?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            
                            <tfoot>
                            <tr>
                                <th>Company</th>
                                <th>Billing Name</th>
                                <th>Billing Email</th>
                                <th>First Name</th>
                                <th>Last name</th>
                                <th>Personal Email</th>
                                <th>Action</th>
                            </tr>
                            </tfoot>
                        </table>
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
$("#example1").DataTable({
"responsive": true, "lengthChange": false, "autoWidth": false,
"buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
}).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

});
</script>
<!-- AdminLTE App -->

@endsection