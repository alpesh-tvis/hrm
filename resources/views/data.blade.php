<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>HRM</title>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
        <link rel="stylesheet" href="{{asset('public/plugins/fontawesome-free/css/all.min.css')}}">
        <link rel="stylesheet" href="{{asset('public//css/adminlte.min.css')}}">
        <link rel="stylesheet" href="{{asset('public/plugins/overlayScrollbars/css/OverlayScrollbars.min.css')}}">
    </head>
    <body class="hold-transition sidebar-mini layout-fixed sidebar-collapse">
        <div class="wrapper">
            <div class="preloader flex-column justify-content-center align-items-center">
                <img class="animation__shake" src="{{asset('public/img/AdminLTELogo.png')}}" alt="AdminLTELogo" height="60" width="60">
            </div>
            @include('admin.header')
            @include('admin.sidebar')
            <div class="content-wrapper">
                <div class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1 class="m-0">Statements</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                                    <li class="breadcrumb-item active">Statements</li>
                                </ol>
                            </div>
                            <div class="col-12 text-right">
                                <a href="{{ route('importStatement.create') }}" class="btn btn-primary">Import</a>
                            </div>    
                        </div>
                    </div>
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            {{ $message }}
                        </div>
                    @endif
                </div>
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Statement List</h3>
                                    </div>
                                    <div class="card-body">
                                        <input type="text" name="daterange" class="mb-2" readonly />
                                        <form action="{{route('multi_select')}}" method="POST">
                                            @csrf
                                                    
                                            <!-- multi_select button  -->
                                            <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#multi_select">Add Withdrwal Rate </button>
                                            <button type="button" id="generate_pdf" class="btn btn-primary mb-3">Generate PDF</button>
                                            <button type="button" id="generate_zip" class="btn btn-primary mb-3">Download Zip</button>

                                            <!-- Multi Select Modal -->
                                            <div class="modal fade" id="multi_select" tabindex="-1" role="dialog" aria-hidden="true">
                                                <div class="alert alert-danger" style="display:none"></div>
                                                   
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="multi_select_title">Withdrawal Details</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="card-body">
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <label for="withdrawal_date">Withdrawal Date</label>
                                                                            <input type="date" class="form-control" id="withdrawal_date" name="withdrawal_date" required>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <label for="withdrawal_rate">Withdrawal Rate</label>
                                                                            <input type="number" class="form-control" step="any" id="withdrawal_rate" name="withdrawal_rate" required>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" id="w_submit" class="btn btn-primary">Submit</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Get table Data -->
                                            <div class="table-responsive"> 
                                                <table id="stat_tbl" class="table table-bordered table-striped" style="width:100%">
                                                    <thead>
                                                        <tr class="table-header">
                                                            <th>All<input type="checkbox" id="select_all"></th>
                                                            <th>Date</th>
                                                            <th>Bill Date</th>
                                                            <th >Bill No</th>
                                                            <th>Ref ID</th>
                                                            <th>Type</th>
                                                            <th>Team</th>
                                                            <th>Description</th>
                                                            <th>Acc Name</th>
                                                            <th>Amo($)</th>
                                                            <th>Rate</th>
                                                            <th>Total</th>
                                                            <th>Bill Amo</th>
                                                            <th>Round</th>
                                                            <th>W Date</th>
                                                            <th>W Rate</th>
                                                            <th>W Amo</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody> 
                                                        @foreach ($data as $dat)
                                                        <tr>
                                                            <td>{{$dat->id}}</td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                        </tr>    
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot>
                                                        <tr class="table-header">
                                                            <th></th>
                                                            <th>Date</th>
                                                            <th>Bill Date</th>
                                                            <th>Bill No</th>
                                                            <th>Ref ID</th>
                                                            <th>Type</th>
                                                            <th>Team</th>
                                                            <th>Description</th>
                                                            <th>Acc Name</th>
                                                            <th>Amo($)</th>
                                                            <th>Rate</th>
                                                            <th>Total</th>
                                                            <th>Bill Amo</th>
                                                            <th>Round</th>
                                                            <th>W Date</th>
                                                            <th>W Rate</th>
                                                            <th>W Amo</th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            @include('admin.footer')
            <!-- Control Sidebar -->
            <aside class="control-sidebar control-sidebar-dark">
                <!-- Control sidebar content goes here -->
            </aside>

            <!-- /.control-sidebar -->
        </div>

        <!-- ./wrapper -->

        <!-- jQuery -->

        <script src="{{asset('public/plugins/jquery/jquery.min.js')}}"></script>

        <!-- jQuery UI 1.11.4 -->

        <script src="{{asset('public/plugins/jquery-ui/jquery-ui.min.js')}}"></script>

        <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->

        

        <!-- daterangepicker -->

        <script src="{{asset('public/plugins/moment/moment.min.js')}}"></script>

        <script src="{{asset('public/plugins/daterangepicker/daterangepicker.js')}}"></script>

        <script src="{{asset('public/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js')}}"></script>

        <script src="{{asset('public/js/adminlte.js')}}"></script>

        <!-- <script src="{{asset('public/js/demo.js')}}"></script> -->
    </body>
</html>