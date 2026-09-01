`@extends('admin.master')

@section('content')
<link rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">    

<section class="content">
  <a class="btn btn-primary right" href="{{ route('holiday.index') }}">Go Back</a>
  <div class="container-fluid px-1 py-3 mx-auto">
  <!-- <h3></h3> -->

  @if(session('holiday_add'))
    <div class="alert alert-success">
        {{ session('holiday_add') }}
    </div>
  @elseif($errors->has('holiday_date'))
     <p class="alert alert-danger">{{ $errors->first('holiday_date') }}</p>
  @endif
  
  @if($errors->has('remark'))
  <p class="alert alert-danger">{{ $errors->first('remark') }}</p>
  @endif
  
  <div class="card project-bg">
    <h3 class="font-weight-bolder mb-0">Create Holiday</h3>
   <form class="form-card p-4 mb-0" action="{{route('holiday.store')}}" method="POST" class="updateform" enctype="multipart/form-data">
    {{ csrf_field() }}
      <div class="row d-flex justify-content-center">
        <div class="col-md-3">
          <div class="form-group">
            <label for="holiday_date" class="form-label">Date</label>
            <input type="date" class="form-control" id="holiday_date" name="holiday_date" value="" placeholder="Holiday Date">
          </div>
        </div>
        <div class="col-md-9">
          <div class="form-group">
            <label for="remark" class="form-label">Remark</label>
            <textarea class="form-control" id="remark" name="remark" rows="1"></textarea>
          </div>
        </div>
      </div>  
      <div class="col-12 p-0">
        <div class="form-group mb-0">
          <button type="submit" class="btn btn-primary">Add holiday</button>
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

</script>
@endsection
