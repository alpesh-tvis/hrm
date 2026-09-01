@extends('admin.master')

@section('content')
<link rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">    

<section class="content">
<a class="btn btn-primary right" href="{{ route('holiday.index') }}">Go Back</a>
<div class="container-fluid px-1 py-5 mx-auto">
  @if(session('holiday_update_success'))
    <div class="alert alert-success">
        {{ session('holiday_update_success') }}
    </div>
     @elseif($errors->has('holiday_date'))
     <p class="alert alert-danger">{{ $errors->first('holiday_date') }}</p>
  @endif
   @if($errors->has('remark'))
  <p class="alert alert-danger">{{ $errors->first('remark') }}</p>
  @endif
<div class="card">
  <form class="form-card" action="{{ route('holiday.update', [$allHoliday->id]) }}" method="POST" class="updateform" enctype="multipart/form-data">
    {{ csrf_field() }}
      <div class="row d-flex justify-content-center">
        <div class="col-md-6">
          <div class="form-group">
            <label for="holiday_date" class="form-label">Date</label>
            <input type="date" class="form-control" id="holiday_date" name="holiday_date" value="{{$allHoliday->holiday_date}}" placeholder="Holiday Date">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label for="remark" class="form-label">Remark</label>
            <textarea class="form-control" id="remark" name="remark" rows="4"> {{$allHoliday->remark}}</textarea>

          </div>
        </div>
      </div>  
      <div class="col-12">
        <div class="form-group">
          <button type="submit" class="btn btn-primary">Update Holiday</button>
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
