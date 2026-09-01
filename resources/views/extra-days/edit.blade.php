@extends('admin.master')

@section('content')
<link rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">    

<section class="content">
 @if ($errors->any())
      <div class="alert alert-danger">
          <ul>
              @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
              @endforeach
          </ul>
      </div>
  @endif
 @if(session('success'))
          <div class="alert alert-success">
              {{ session('success') }}
      </div>
    @endif

    @if(session('error'))
      <div class="alert alert-danger">
          {{ session('error') }}
      </div>
    @endif
  <a class="btn btn-primary right" href="{{ route('extra-days.index') }}">Go Back</a>
  <div class="container-fluid px-1 py-5 mx-auto">
     <div class="card project-bg p-0">
      <h3 class="font-weight-bolder mb-0">Extra Days Details</h3>
     <form class="form-card p-4" action="{{ route('extra_days_update', [$extraDay->id]) }}" method="POST" class="updateform" enctype="multipart/form-data">
      {{ csrf_field() }}
        <div class="row d-flex justify-content-center">
            <div class="col-md-4">
              <div class="form-group">
                <label for="emplyoee_id" class="form-label">Emplyoee</label>
                  <select name="employee_id" class="form-control">
                    @foreach($employeerec as $emp)
                        <option value="{{ $emp->id }}" {{ $emp->id == $selectedEmployeeId ? 'selected' : '' }}>
                            {{ $emp->first_name }} {{ $emp->last_name }}
                        </option>
                    @endforeach
                  </select>
              </div>
            </div>
             <div class="col-md-4">
                <div class="form-group">
                  <label for="date" class="form-label">Date</label>
                  <input type="date" class="form-control" id="date" name="date" value="{{$extraDay->date}}" placeholder="Date">
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">  
                  <label for="extra_day" class="form-label"> Extra Day</label>
                  <input type="number" class="form-control" id="extra_day" name="extra_day" min="0.5" step="0.5" value="{{$extraDay->extra_days  }}" placeholder="Day">
                </div>
              </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label for="reason_of_work_description" class="form-label">Reason of Work Description</label>
                <textarea class="form-control" id="reason_of_work_description" name="reason_of_work_description" rows="1">{{$extraDay->reason_of_work_description }}</textarea>
              </div>
            </div>
        </div>  
        <div class="col-12 p-0">
          <div class="form-group mb-0">
            <button type="submit" class="btn btn-primary">Update</button>
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
                                                                 
@endsection
