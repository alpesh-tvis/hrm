@extends('admin.master')

@section('content')
    <section class="content">

    <div class="container-fluid">

        <div class="row">

            <div class="col-md-12">

                <div class="project-bg card card-primary p-0">
                    <h3 class="font-weight-bolder mb-0">Change Password</h3>

                    @if (session('error'))
                                <div class="alert alert-danger">
                                    {{ session('error') }}
                                </div>
                            @endif
                            @if (session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif
                            @if($errors)
                                @foreach ($errors->all() as $error)
                                    <div class="alert alert-danger">{{ $error }}</div>
                                @endforeach
                            @endif
                        <form class="form-horizontal" method="POST" action="{{ route('changePasswordPost') }}">
                        
                        {{ csrf_field() }}
                        

                        <div class="card-body p-4">

                            <div class="row">

                                <div class="col-md-4">

                                    <div class="form-group">

                                        <label for="Current_Password">Current Password</label>

                                        <input type="password" class="form-control{{ $errors->has('current-password') ? ' has-error' : '' }}" id="current-password" name="current-password" value="" placeholder="Enter Current Password" required>
                                        @if ($errors->has('current-password'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('current-password') }}</strong>
                                            </span>
                                        @endif

                                    </div>

                                </div>

                                <div class="col-md-4">    

                                    <div class="form-group">

                                        <label for="new_password">New Password</label>

                                        <input type="password" class="form-control{{ $errors->has('new-password') ? ' has-error' : '' }}" id="new-password" name="new-password" value="" placeholder="Enter New Password" required>
                                        @if ($errors->has('new-password'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('new-password') }}</strong>
                                            </span>
                                        @endif

                                    </div>

                                </div>

                                <div class="col-md-4">    

                                    <div class="form-group">

                                        <label for="new_password">Confirm New Password</label>

                                        <input type="password" class="form-control" id="new-password_confirmation" name="new-password_confirmation" value="" placeholder="Enter Confirm New Password" required>

                                    </div>

                                </div>

                            </div>

                           
                        </div>

                        <div class="pl-4 pb-4">

                           <button type="submit" class="btn btn-primary">Submit</button>

                        </div>

                    </form>

                    

                </div>

            </div>

        </div>

    </div>

</section>

    
       
@endsection