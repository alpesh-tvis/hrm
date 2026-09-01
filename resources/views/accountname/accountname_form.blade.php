@extends('admin.master')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        @if ($errors->any())
                            <div class="alert alert-danger">There were some problems with your input.<br><br>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        @if ($message = Session::get('success'))
                            <div class="alert alert-success">
                                <p>{{ $message }}</p>
                            </div>
                        @endif
                        @if (session('error'))
                                <div class="alert alert-danger">
                                    {{ session('error') }}
                                </div>
                            @endif

                        @if(isset($account_name))
                            <form action="{{ route('accountname.update',$account_name) }}" method="POST">
                            @csrf
                            @method('PUT')   
                        @else
                        <form action="{{ route('accountname.store') }}" method="POST">
                         @csrf
                        @endif
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="accountname">Account Name</label>
                                                <input type="text" class="form-control" id="accountname" name="accountname" value="{{!empty($account_name) ? $account_name->accountname : ''}}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                        </form>
                        
                    </div>
                </div>
            </div>
            <!-- <a href="{{route('download')}}">Download sample file</a> -->
        </div>
    </section>
@endsection