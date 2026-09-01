@extends('admin.master')

@section('content')
    <link rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">    
    
    <section class="content">
        <div class="row mb-2">
            <div class="col-12 text-right">
                <a href="{{ route('currency.create') }}" class="btn btn-primary">Add Currency</a>
            </div>
        </div>
        
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            
            <table id="currency_tbl" class="table table-bordered table-striped display">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Code</th>
                        <th>Symbol</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @php
                        $i = 0;
                    @endphp
                    
                    @foreach($currencies as $currency )
                        @if($currency)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{$currency->name}}</td>
                                <td>{{$currency->code}}</td>
                                <td>{!! $currency->html_symbol!!}</td>
                                <td>
                                    <a href="{{ route('currency.edit', $currency->id) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-pencil-alt"></i> Edit
                                    </a>
                                    <!-- <form action="{{ route('currency.destroy', $currency->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </button>
                                    </form> -->
                                </td>
                            </tr>    
                        @endif
                    @endforeach
                </tbody>

                <tfoot>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Code</th>
                        <th>Symbol</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </section>

    <script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
    <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
    <script src="{{asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
    <script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.js')}}"></script>

    <script>
        jQuery(function ($) {
            $("#currency_tbl").DataTable({
              language: {
               emptyTable: "No Currency available in table",  
               loadingRecords: "Please wait .. ", 
               zeroRecords: "No Currency matching records found"
              }, "paging": true, "responsive": true,"lengthChange": false, "autoWidth": false, "searchable": true, "pageLength": 10,
            })
        });
    </script>
@endsection
