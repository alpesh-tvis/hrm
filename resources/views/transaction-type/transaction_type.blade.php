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
                    
                    
                    @if(isset($TransactionType))
                    <form action="{{ route('transaction-type.update',$TransactionType) }}" method="POST">
                        @csrf
                        @method('PUT')
                        @else
                        <form action="{{ route('transaction-type.store') }}" method="POST">
                            @csrf
                            @endif
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="start_date">Type</label>
                                            <input type="text" class="form-control" id="type" name="type" value="{{!empty($TransactionType) ? $TransactionType->type : ''}}">
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
        </section>
        @endsection