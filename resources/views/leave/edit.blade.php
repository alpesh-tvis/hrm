@extends('admin.master')

@section('content')
    
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        @if ($errors->any())
                            <div class="alert alert-danger">
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
                        
                        <form action="{{ route('leave.update',$leave->id) }}" method="POST">
                            @csrf
                            @method('PUT')    
                                <div class="btn-group btn-group-sm">
                                    <label for="leave_type">Name : &nbsp; </label>
                                    <p>{{ $leave->employee->first_name }} {{$leave->employee->last_name}}</p>
                                    
                                </div>    

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="leave_type">Leave Type:</label>
                                            <select name="leave_type" id="leave_type" class="form-control">
                                                <option value="PL" {{$leave->leave_type == 'PL' ? 'selected' : ''}}>Paid Leave</option>
                                                <option value="SL" {{$leave->leave_type == 'SL' ? 'selected' : ''}}>Sick Leave</option>
                                                <option value="CL" {{$leave->leave_type == 'CL' ? 'selected' : ''}}>Casual Leave</option>
                                            </select>
                                        </div>    
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="leave_status">Leave Status:</label>
                                            <select name="leave_status" id="leave_status" class="form-control">
                                                <option value="F" {{$leave->leave_status == 'F' ? 'selected' : ''}}>Full Day</option>
                                                <option value="FH" {{$leave->leave_status == 'FH' ? 'selected' : ''}}>First Half</option>
                                                <option value="SH" {{$leave->leave_status == 'SH' ? 'selected' : ''}}>Second Half</option>
                                            </select>
                                        </div>    
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="leave_type">Leave Date:</label>
                                            <input type="date" name="leave_date" value="{{$leave->leave_date}}" class="mb-2 form-control" />
                                        </div>    
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="leave_type">Leave Reason:</label>
                                            <textarea name="leave_reason"  class="mb-2 form-control" />{{$leave->leave_reason}}</textarea>
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
        </div>
    </section>
@endsection

