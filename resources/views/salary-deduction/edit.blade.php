@extends('admin.master')

@section('content')
<link rel="stylesheet" href="{{asset('public/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('public/plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('public/plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">    

@php
foreach($employeerec as $empval){
$empFirstName = $empval->first_name;
$empId = $empval->id;
}
@endphp

<section class="content">

  <a class="btn btn-primary right" href="{{ route('salary-deduction.index') }}">Go Back</a>
  <div class="container-fluid px-1 py-5 mx-auto">
   @if(session('Salary_updateSuccess'))
      <div class="alert alert-success">
          {{ session('Salary_updateSuccess') }}
      </div>
       @elseif($errors->has('emp_name'))
       <p class="alert alert-danger">{{ $errors->first('emp_name') }}</p>
    @endif
     @if($errors->has('remark'))
    <p class="alert alert-danger">{{ $errors->first('remark') }}</p>
    @endif
     <div class="card">
     <form class="form-card" action="{{ route('salary-deduction-update', [$salaryDeductionData->id]) }}" method="POST" class="updateform" enctype="multipart/form-data">
      {{ csrf_field() }}
        <div class="row d-flex justify-content-center">
          <div class="col-md-6">
            <div class="form-group">
              <i class="fas fa-user prefix grey-text"></i>
              <label for="emplyoee_id" class="form-label">Emplyoee</label>
                <input type="text" class="form-control" id="emp_name" name="emp_name" value="{{$salaryDeductionData->employee_name}}" readonly>
                <input type="hidden" class="form-control" id="employee_id" name="employee_id" value="{{ $salaryDeductionData->employee_id }}">
            </div>

            <div class="form-group">
              <label for="date" class="form-label">Date</label>
              <input type="date" class="form-control" id="date" name="date" value="{{$salaryDeductionData->date}}" placeholder="Date">
            </div>

            <div class="form-group">  
              <label for="extra_day" class="form-label"> Month</label>
              <input type="text" class="form-control" id="month" name="month" value="{{$salaryDeductionData->month}}" placeholder="Month">
            </div>

            <div class="form-group">
              <label for="leave_type" class="form-label">Leave Type</label>
                <select name="leave_type" class="form-control">
                 <option selected disabled hidden>Select Leave type</option>
                 <option name="leave_type" value="CL" {{ $salaryDeductionData->leave_type == 'CL' ? 'selected' : '' }}>CL</option>
                 <option name="leave_type" value="SL" {{ $salaryDeductionData->leave_type == 'SL' ? 'selected' : '' }}>SL</option>
                 <option name="leave_type" value="PL" {{ $salaryDeductionData->leave_type == 'PL' ? 'selected' : '' }}>PL</option>
                </select>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              <label for="reason" class="form-label">Reason</label>
              <textarea class="form-control" id="reason" name="reason" rows="5" cols="50" style="resize: none;">{{$salaryDeductionData->reason }}</textarea>
            </div>
            <div class="form-group">
              <label for="salary_deduction" class="form-label">Select Salary Deduction</label>
                <select name="salary_deduction" class="form-control">
                 <option selected disabled hidden>Select Salary Deduction</option>
                 <option name="salary_deduction" value="First Half" {{ $salaryDeductionData->salary_deduction == 'First Half' ? 'selected' : '' }}>First Half </option>
                 <option name="salary_deduction" value="Second Half" {{ $salaryDeductionData->salary_deduction == 'Second Half' ? 'selected' : '' }}>Second Half</option>
                 <option name="salary_deduction" value="Full Day" {{ $salaryDeductionData->salary_deduction == 'Full Day' ? 'selected' : '' }}>Full Day</option>
                  <option name="salary_deduction" value="Half Day" {{ $salaryDeductionData->salary_deduction == 'Half Day' ? 'selected' : '' }}>Half Day</option>
                </select>
            </div>
          </div>
        </div>  

        <div class="col-12">
          <div class="form-group">
            <button type="submit" class="btn btn-primary">Update</button>
          </div>
        </div>
      </form>
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
