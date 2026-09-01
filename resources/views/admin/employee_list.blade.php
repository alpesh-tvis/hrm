@extends('admin.master')
<link rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
<link href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/5.0.7/sweetalert2.min.css" rel="stylesheet">
@section('content')

    <div class="container-fluid">
        <div class="row mb-2 mt-4">
            
            <div class="col-12 text-right pr-4">
                <a href="{{ route('admin.create') }}" class="btn btn-primary">Add</a>
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
                <div class="card p-0 shadow-none bg-transparent">
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-striped">
                            
                            <thead>
                                <tr>
                                    <th>EMP No</th>
                                    <th>First Name</th>
                                    <th>Last name</th>
                                    <th>Email</th>
                                    <th>Department</th>
                                    <th>Position</th>
                                    <th>Role</th>
                                    <th>Pancard</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            
                            <tbody>
                                @foreach ($employee as $emp)
                                @php
                                
                                $user1 = \App\Models\User::where('email',$emp->company_email)->first();
                                
                                if ($user1 && $user1->role == '2') {
                                    $role = 'Admin';
                                } elseif ($user1 && $user1->role == '1') {
                                    $role = 'Employee';
                                } else {
                                    $role = 'Freelancer';
                                }
                                @endphp
                                <tr>
                                    <td>{{$emp->id}}</td>
                                    <td>{{$emp->first_name}}</td>
                                    <td>{{$emp->last_name}}</td>
                                    <td>{{$emp->personal_email}}</td>
                                    <td>
                                        @if($emp->department == "1")
                                        Sales
                                        @endif
                                        @if($emp->department == "2")
                                        Production
                                        @endif
                                    </td>
                                    <td>{{$emp->position}}</td>
                                    <td>{{$role}}</td>
                                    <td>{{$emp->pancard}}</td>
                                    <td class="action-btn">
                                        <a href="{{ route('admin.edit',[$emp->id]) }}" class="btn  btn-primary">Edit</a>
                                        @if(Auth::user()->email!=$emp->company_email)
                                        <form action="{{ route('admin.destroy',$emp->id) }}" method="post" style="display:inline">
                                            {{csrf_field()}}
                                            <input name="_method" type="hidden" value="DELETE">
                                            <button class="btn btn-danger show-alert-delete-box" type="submit">Delete</button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            
                            <tfoot>
                            <tr>
                                <th>EMP No</th>
                                <th>First Name</th>
                                <th>Last name</th>
                                <th>Email</th>
                                <th>Department</th>
                                <th>Position</th>
                                <th>Role</th>
                                <th>pancard</th>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<script>
jQuery(function ($) {
$("#example1").DataTable({
"responsive": true, "lengthChange": false, "autoWidth": false,
"buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
}).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
$('#example2').DataTable({
"paging": true,
"lengthChange": false,
"searching": false,
"ordering": true,
"info": true,
"autoWidth": false,
"responsive": true,
});
});
</script>
<script type="text/javascript">
$('.show-alert-delete-box').click(function(event){
var form =  $(this).closest("form");
var name = $(this).data("name");
event.preventDefault();
swal({
title: "Are you sure you want to delete this record?",
text: "If you delete this, it will be gone forever.",
icon: "warning",
type: "warning",
buttons: ["Cancel","Yes!"],
confirmButtonColor: '#3085d6',
cancelButtonColor: '#d33',
confirmButtonText: 'Yes, delete it!'
}).then((willDelete) => {
if (willDelete) {
form.submit();
}
});
});
</script>
<!-- AdminLTE App -->
<style type="text/css">
.action-btn{
display: flex;
align-items: baseline;
justify-content: space-between;
}
</style>
@endsection