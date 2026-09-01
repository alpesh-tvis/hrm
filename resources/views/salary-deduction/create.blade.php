`@extends('admin.master')

@section('content')
<link rel="stylesheet" href="{{asset('public/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('public/plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('public/plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">    

<section class="content">
  <a class="btn btn-primary right" href="">Go Back</a>
  <div class="container-fluid px-1 py-5 mx-auto">
    
  </div>
</section>

<!-- DataTables  & Plugins -->
<script src="{{asset('public/plugins/jquery/jquery.min.js')}}"></script>
<script src="{{asset('public/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('public/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('public/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('public/plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
<script src="{{asset('public/plugins/bootstrap/js/bootstrap.bundle.js')}}"></script>
<script>

</script>
@endsection
