@extends('admin.master')

@section('content')
<link rel="stylesheet" href="{{asset('public/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('public/plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('public/plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">    

<section class="content">
  @if(session('success'))
      <div class="alert alert-success">
          {{ session('success') }}
      </div>
      
  @endif
<a class="btn btn-primary right" href="{{ route('leave_setting') }}">Go Back</a>

<div class="container-fluid px-1 py-5 mx-auto">
<div class="card">
  @if($employeedata)
   <form class="form-card" action="{{ route('leave_update_setting', [$employeedata->id]) }}" method="POST" class="updateform" enctype="multipart/form-data">
    {{ csrf_field() }}
       
      <div class="row d-flex justify-content-center">
        <div class="col-md-6">
          <div class="form-group">
            <label for="sick_leave" class="form-label">Employee</label>
            <input type="text" class="form-control" id="emp_name" name="emp_name" value="{{ $employeedata->emp_name }}" readonly>
            <input type="hidden" class="form-control" id="employee_id" name="employee_id" value="{{ $employeedata->employee_id }}">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label for="sick_leave" class="form-label">Sick Leave</label>
            <input type="text" class="form-control" id="sick_leave" name="sick_leave" value="{{ $employeedata->sick_leave }}">
            </div>
        </div>
       </div>

      <div class="row d-flex justify-content-center">
        <div class="col-md-6">
          <div class="form-group">
            <label for="paid_leave" class="form-label">Paid Leave</label>
            <input type="text" class="form-control" id="paid_leave" name="paid_leave" value="{{ $employeedata->paid_leave }}">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label for="casual_leave" class="form-label">Casual Leave</label>
            <input type="text" class="form-control" id="casual_leave" name="casual_leave" value="{{ $employeedata->casual_leave }}">
          </div>
         </div>
      </div>


      <div class="row d-flex justify-content-center">
          <div class="col-md-6">
            <div class="form-group">
              <label for="previous_year_leave" class="form-label">Previous Year Leave</label>
              <input type="text" class="form-control" id="previous_year_leave" name="previous_year_leave" value="{{ $employeedata->previous_year_leave }}">
            </div>
          </div>
          @php
          $count_extra_days =  \App\Models\ExtraDays::where('employee_id',$employeedata->employee_id)->where('financial_year',$employeedata->financial_year)->sum('extra_days'); 
          @endphp
          <div class="col-md-6">
            <div class="form-group">
              <label for="extra_days" class="form-label">Extra Day's (Note: You can make changes in the Extra Day's section.)</label>
              <input type="text" class="form-control" id="extra_days" name="extra_days" value="{{ $count_extra_days }}" readonly>
            </div>
          </div>
        </div>

        <div class="row d-flex justify-content-center">
          <div class="col-md-6">
            <div class="form-group">
              <label for="financial_year" class="form-label">Financial Year</label>
              <!-- <select id="financial_year" class="form-control" name="financial_year">
                  <option name="financial_year">Select</option>
                  <option value="2023-2024" {{ $employeedata->financial_year =="2023-2024" ? 'selected' : '' }}>2023-2024</option>
                  <option value="2024-2025" {{ $employeedata->financial_year =="2024-2025" ? 'selected' : '' }}>2024-2025</option>
              </select> -->
              <select id="financial_year" class="form-control" name="financial_year">
                  <option value="{{ $employeedata->financial_year }}" {{ $employeedata->financial_year =="" ? 'selected' : '' }}>{{ $employeedata->financial_year }}</option>
              </select>
            </div>
          </div>
          <div class="col-md-6">
               <div class="form-group"> </div>
          </div>
        </div>
      
      <div class="col-12">
        <div class="form-group">
          <button type="submit" class="btn btn-primary">Update leave</button>
        </div>
      </div>
    </form>
    @else
    <p>No employee data available.</p>
    @endif
  </div>
  </div>
 
</section>

<!-- DataTables  & Plugins -->
<script src="{{asset('public/plugins/jquery/jquery.min.js')}}"></script>
<script src="{{asset('public/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('public/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('public/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('public/plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
<script src="{{asset('public/plugins/bootstrap/js/bootstrap.bundle.js')}}"></script>

@endsection
