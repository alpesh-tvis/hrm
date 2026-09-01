@extends('admin.master')
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        There were some problems with your input.<br><br>
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
                    
                    <form action="{{ route('profile.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <div class="project-bg">
                                <h3 class="font-weight-bolder mb-0">Personal Information</h3>
                                <div class="p-4">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>First Name</label>
                                                <input type="Text" class="form-control" name="first_name" value="{{!empty($get_user->first_name) ? $get_user->first_name : old('first_name')}}" placeholder="Enter First name">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Last Name</label>
                                                <input type="text" class="form-control"  name="last_name" value="{{!empty($get_user->last_name) ? $get_user->last_name : old('last_name')}}" placeholder="Enter Last name">
                                            </div>
                                        </div>
                                         <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Full Name</label>
                                                <input type="text" class="form-control" name="full_name" value="{{!empty($get_user->full_name) ? $get_user->full_name : old('full_name')}}" placeholder="Enter your name as pancard">
                                            </div>
                                        </div>
                                    </div>
                                 </div>
                            </div>
                            <div class="project-bg">
                                <h3 class="font-weight-bolder mb-0"> Contact Information</h3>
                                <div class="p-4">
                                    <div class="row">
                                       <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Mobile Number</label>
                                                <input type="number" class="form-control" name="mobile" value="{{!empty($get_user->mobile) ? $get_user->mobile : old('mobile')}}" placeholder="Enter Mobile Number">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Company Email</label>
                                                <input type="email" class="form-control" value="{{!empty($get_user->company_email) ? $get_user->company_email : old('company_email')}}" readonly >
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Personal Email</label>
                                                <input type="email" class="form-control" name="personal_email" value="{{!empty($get_user->personal_email) ? $get_user->personal_email : old('personal_email')}}" placeholder="Enter Personal Email">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="project-bg">
                                <h3 class="font-weight-bolder mb-0"> Additional Details</h3>
                                <div class="p-4">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Birth Date</label>
                                                <input type="date" class="form-control"  name="bday" placeholder="Enter Birth Date" value="{{!empty($get_user->bday) ? $get_user->bday : old('bday')}}" max="<?= date('Y-m-d'); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Enter PAN Number</label>
                                                <input type="text" class="form-control" name="pancard" value="{{!empty($get_user->pancard) ? $get_user->pancard : old('pancard')}}" placeholder="Enter PAN Number">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Linkdin</label>
                                                <input type="text" class="form-control" id="linkdin" name="linkdin" value="{{!empty($get_user->linkdin) ? $get_user->linkdin : old('linkdin')}}" placeholder="Enter Linkdin">
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="project-bg">
                                <h3 class="font-weight-bolder mb-0"> Address</h3>
                                  <div class="p-4">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Postal Address</label>
                                                <textarea class="form-control" name="postal_address" placeholder="Enter Postal Address" rows="5" >{{!empty($get_user->postal_address) ? $get_user->postal_address : old('postal_address')}}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                   </div>
                            </div>
                            <div class="project-bg">
                                 <h3 class="font-weight-bolder mb-0">Employment Details</h3>
                                  <div class="p-4">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Service Start Date</label>
                                                <input type="date" class="form-control" name="service_start_date" value="{{!empty($get_user->service_start_date) ? $get_user->service_start_date : old('service_start_date')}}" placeholder="Enter Service Start Date" max="<?= date('Y-m-d'); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Service End Date</label>
                                                <input type="date" class="form-control" name="service_enddate" value="{{!empty($get_user->service_enddate) ? $get_user->service_enddate : old('service_enddate')}}" placeholder="Enter Service End Date" max="<?= date('Y-m-d'); ?>">
                                            </div>
                                        </div>
                                         <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Department</label>
                                                @php
                                                if($get_user->department == "1"){
                                                $department = 'Sales';
                                                }
                                                if($get_user->department == "2"){
                                                $department = 'Production';
                                                }
                                                @endphp
                                                <input type="text" value="{{$department}}" class="form-control" readonly>
                                                
                                            </div>
                                        </div>
                                    </div>
                                     <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Position (Job Title)</label>
                                                <input type="text" class="form-control" value="{{!empty($get_user->position) ? $get_user->position : old('position')}}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Reporting Person</label>
                                                
                                                <input type="text" class="form-control" value="{{$reporting_person->full_name}}" readonly>
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="project-bg">
                                <h3 class="font-weight-bolder mb-0">Profile Image</h3>
                                  <div class="p-4">
                                    <div class="row">
                                      <div class="col-md-6">
                                            <div class="form-group d-flex justify-content-between row">
                                                <div class="label-profile col-sm-9">
                                                    <label>Profile Image</label>
                                                    <input type="file" class="form-control" name="profile_image">
                                                </div>
                                                <div class="profile-image col-sm-3">
                                                    @if(isset($get_user->profile_image))
                                                        <img src="{{ asset($get_user->profile_image) }}" style="max-height: 200px;" class="img-fluid" alt="">
                                                        
                                                    @else
                                                        <p>No image available</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                     </div>
                            </div>
                           </div> 
                        <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                    
                
            </div>
        </div>
    </div>
</section>
@endsection