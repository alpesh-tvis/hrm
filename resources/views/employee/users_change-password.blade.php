@extends('admin.master')

@section('content')
    <section class="content">

    <div class="container-fluid">

        <div class="row">

            <div class="col-md-12">

              

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

                    <form class="form-horizontal" method="POST" action="{{ route('admin_upchange_post') }}">
                        
                        {{ csrf_field() }}
                        

                        <div class="project-bg">
                            <h3 class="font-weight-bolder mb-0">Users Change Password</h3>
                            <div class="p-4">
                            <div class="row">

                                <div class="col-md-4">    

                                    <div class="form-group">

                                        <label for="users">Users</label>

                                        <select class="form-control" name="user">
                                            <option value="">-- Select User Email --</option>
                                            @foreach ($users as $user)
                                                <option value="{{$user->email}}" {{ old("user") == $user->email ? 'selected' : '' }}>{{$user->email}}</option>
                                            @endforeach
                                            
                                        </select>

                                    </div>

                                </div>
                                <div class="col-md-4">    

                                    <div class="form-group">

                                        <label for="new_password">New Password</label>

                                        <input type="password" class="form-control{{ $errors->has('new_password') ? ' has-error' : '' }}" id="new_password" name="new_password" value="" placeholder="Enter New Password" >
                                        @if ($errors->has('new_password'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('new_password') }}</strong>
                                            </span>
                                        @endif

                                    </div>

                                </div>

                                <div class="col-md-4">    

                                    <div class="form-group">

                                        <label for="confirm_password">Confirm New Password</label>

                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" value="" placeholder="Enter Confirm New Password" >

                                    </div>

                                </div>

                            </div>
</div>
                           
                        </div>

                        <div class="card-footer bg-transparent p-0">

                           <button type="submit" class="btn btn-primary">Submit</button>

                        </div>

                    </form>

                    

               

            </div>

        </div>

    </div>

</section>

    
       
@endsection