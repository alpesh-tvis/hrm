@extends('admin.master')

@section('content')
  <link rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
  <link rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
  <link rel="stylesheet" href="{{asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">    

  <section class="content">
    <a class="btn btn-primary right" href="{{route('mail-request.index')}}">Go Back</a>
    <div class="container-fluid px-1 py-5 mx-auto">
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

      <div class="card">
        <form class="form-card" action="{{ route('mail-request.store') }}" method="POST">
          {{ csrf_field() }}
          <div class="row d-flex justify-content-center">
            <div class="col-md-6">
              <div class="form-group">
                <label for="reason" class="form-label">Reason</label>
                <select name="reason" class="form-control" id="reason">
                  <option value="">--- Select Reason ---</option>
                  <option value="late_coming" {{ old('reason') == 'late_coming' ? 'selected' : '' }}>Late Coming</option>
                  <option value="early_going" {{ old('reason') == 'early_going' ? 'selected' : '' }}>Early Going</option>
                  <option value="leave_status_change" {{ old('reason') == 'leave_status_change' ? 'selected' : '' }}>Leave Status</option>
                  <option value="leave_reason_change" {{ old('reason') == 'leave_reason_change' ? 'selected' : '' }}>Leave Reason</option>
                  <option value="other" {{ old('reason') == 'other' ? 'selected' : '' }}>Other</option>
                </select>
              </div>

              <div class="form-group" id="date-group" style="{{ $errors->has('request_date') || in_array(old('reason'), ['late_coming', 'early_going', 'leave_status_change', 'leave_reason_change']) ? 'display: block;' : 'display: none;' }}">
                <label for="request_date" class="form-label">Leave Date</label>
                <input type="date" class="form-control" id="request_date" name="request_date" placeholder="Enter Leave Date" value="{{ old('request_date') }}">
              </div>
              <div class="form-group">
                <label for="subject" class="form-label">Subject</label>
                <input type="text" class="form-control" id="subject" name="subject" placeholder="Subject" value="{{ old('subject') }}">
              </div>
            </div>  
            <div class="col-md-6">
              <div class="form-group">  
                <label for="description" class="form-label">Details</label>
                <textarea class="form-control" name="description" rows="5">{{ old('description') }}</textarea>
              </div>
            </div>  
          </div>  
          <div class="col-12">
            <div class="form-group">
              <button type="submit" class="btn btn-primary">Submit Request</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </section>

  <script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
  <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
  <script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
  <script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
  <script src="{{asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
  <script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.js')}}"></script>
  <script>
    jQuery(document).ready(function () {
      jQuery('#reason').on('change', function () {
        const selectedValue = jQuery(this).val();
        const showDate = ['late_coming', 'early_going', 'leave_status_change', 'leave_reason_change'];
        
        if (showDate.includes(selectedValue)) {
          jQuery('#date-group').show();
        } else {
          jQuery('#date-group').hide();
        }
      });
    });
  </script>
@endsection
