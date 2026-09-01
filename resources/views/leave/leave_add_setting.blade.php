`@extends('admin.master')

@section('content')
<link rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">    


<section class="content">
  @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
  @endif

  @if(session('already_existsdata'))
    <div class="alert alert-danger">
        {{ session('already_existsdata') }}
    </div>
  @endif
  <a class="btn btn-primary right" href="{{ route('leave_setting') }}">Go Back</a>

  <div class="container-fluid px-1 py-5 mx-auto">
  <h3></h3>

  <div class="project-bg p-4">
   <form class="form-card mb-0" action="{{ route('add_setting') }}" method="POST" class="updateform" enctype="multipart/form-data">
    {{ csrf_field() }}
    
      <!-- <div class="row d-flex justify-content-center">
        <div class="col-md-6">
            <div class="form-group">
             <label for="emp_name" class="form-label">Employee</label>
                <select class="form-control" name="emp_name" id="empid">
                    <option value="option_select" disabled selected>All Employee</option>
                    @foreach($employeerecord as $employee)
                        <option value="{{ $employee->first_name}}" id="{{ $employee->id }}">
                        @if(!empty($employee))
                          {{ $employee->first_name }}
                        @else
                        Not Available  
                        @endif
                        </option>
                    @endforeach
                </select>
                <input type="hidden" name="employee_id" id="employee_id" value="">
            </div>
       </div>
         <div class="col-md-6">
          <div class="form-group">
            <label for="sick_leave" class="form-label">Sick</label>
            <input type="number" class="form-control" id="updatesick_leave" name="sick_leave" value="" placeholder="Sick">
          </div>
        </div>
       </div> -->


      <div class="row d-flex justify-content-center">
        <div class="col-md-4">
          <div class="form-group">
            <label for="sick_leave" class="form-label">Sick</label>
            <input type="number" class="form-control" id="updatesick_leave" name="sick_leave" value="" placeholder="Sick">
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label for="paid_leave" class="form-label">Paid</label>
            <input type="number" class="form-control" id="updatepaid_leave" name="paid_leave" value="" placeholder="Paid">
          </div>
        </div>
  
      <!-- <div class="row d-flex justify-content-center"> -->
        <div class="col-md-4">
          <div class="form-group">
            <label for="casual_leave" class="form-label">Casual</label>
            <input type="number" class="form-control" id="updatecasual_leave" name="casual_leave" value="" placeholder="Casual">
          </div>
         </div>
         <!-- <div class="col-md-6">
          <div class="form-group">
            <label for="previous_year_leave" class="form-label">Previous Year</label>
            <input type="number" class="form-control" id="updateprevious_year_leave" name="previous_year_leave" value="" placeholder="Previous Year">
          </div>
        </div> -->
       <!-- </div> -->

      
      <!-- <div class="row d-flex justify-content-center"> -->
        
        <div class="col-md-4" style="display: none;">
          <div class="form-group">
            <label for="financial_year" class="form-label">Financial Year</label>
            <select id="financial_year" class="form-control" name="financial_year">
               <option name="financial_year" value="" >Select</option>
               <option value="2023-2024">2023-2024</option>
              <option value="2024-2025">2024-2025</option>
            </select>
          </div>
        </div>
      </div>  
      <!-- </div> -->
     
      <div class="col-12 p-0">
        <div class="form-group mb-0">
          <button type="submit" class="btn btn-primary">Add leave</button>
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
