@extends('admin.master')
@section('content')
<link rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">    
<section class="content">
  @if(session('update_sett_success'))
    <div class="alert alert-success">
        <i class="fas fa-check"></i> {{ session('update_sett_success') }}
    </div>
  @endif

<a class="btn btn-primary right" href="{{ route('leave_setting') }}">Go Back</a>

  <div class="container-fluid px-1 py-5 mx-auto">
    <div class="card">
     <form class="form-card" id="" action="{{ route('leave_update_settings') }}" method="post" class="updateform" enctype="multipart/form-data">
      {{ csrf_field() }}
      
        <div class="row d-flex justify-content-center">
          <div class="col-md-6">
            <div class="form-group">
              <label for="sick_leave" class="form-label">Sick</label>
              <input type="number" class="form-control" id="updatesick_leave" name="sick_leave" value="" placeholder="Sick">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="paid_leave" class="form-label">Paid</label>
              <input type="number" class="form-control" id="updatepaid_leave" name="paid_leave" value="" placeholder="Paid">
            </div>
          </div>
        </div>  
        
          <div class="col-md-6">
            <div class="form-group">
              <label for="casual_leave" class="form-label">Casual</label>
              <input type="number" class="form-control" id="updatecasual_leave" name="casual_leave" value="" placeholder="Casual">
            </div>
           </div>
         
        <div class="col-12">
          <div class="form-group">
            <button type="submit" class="btn btn-primary">Update leave</button>
          </div>
        </div>
      </form>
    </div>
  </div>

</section>

<!-- DataTables  & Plugins -->
<script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
<script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.js')}}"></script>
<script>
  $("#empid").change(function() {
  var id = $(this).children(":selected").attr("id");
  $('#employee_id').val(id); 
});
</script>
@endsection
