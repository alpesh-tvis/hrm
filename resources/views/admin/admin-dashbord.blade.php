@extends('admin.master')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success">
                            <p>{{ $message }}</p>
                        </div>
                    @endif
                        
                        
                    @if(isset($emp))
                        <form action="{{ route('admin.update',$emp) }}" method="POST">
                            @csrf
                            @method('PUT')   
                        @else    
                        <form action="{{ route('admin.store') }}" method="POST">
                            @csrf
                    @endif    
                    
                    <div class="project-bg">
                        <h3 class="font-weight-bolder mb-0">Personal Information</h3>
                        <div class="p-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firsname">First Name</label>
                                        <input type="Text" class="form-control" id="firstname" name="first_name" value="{{ old('first_name', $emp->first_name ?? '') }}" placeholder="Enter First name">
                                        @error('first_name')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">    
                                    <div class="form-group">
                                        <label for="lastname">Last Name</label>
                                        <input type="text" class="form-control" id="lastname" name="last_name" value="{{ old('last_name', $emp->last_name ?? '') }}" placeholder="Enter Last name">
                                    </div>
                                </div>
                                <div class="col-md-4">        
                                    <div class="form-group">
                                        <label for="fullname">Full Name</label>
                                        <input type="text" class="form-control" id="fullname" name="full_name" value="{{ old('full_name', $emp->full_name ?? '') }} " placeholder="Enter Full name">
                                        @error('full_name')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror    
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="project-bg">
                        <h3 class="font-weight-bolder mb-0">Contact Information</h3>
                        <div class="p-4">
                            <div class="row">
                                <div class="col-md-4">    
                                    <div class="form-group">
                                        <label for="mobile">Mobile Number</label>
                                        <input type="number" class="form-control" id="mobile" name="mobile" value=" {{ old('mobile', $emp->mobile ?? '') }} " placeholder="Enter Mobile Number">
                                    </div>
                                </div>
                                <div class="col-md-4">        
                                    <div class="form-group">
                                        <label for="company_email">Company Email</label>
                                        <input type="email" class="form-control" id="company_email" name="company_email" value="{{ old('company_email', $emp->company_email ?? '') }}" placeholder="Enter Company Email" {{!empty($emp->company_email) ? 'readonly' : ''}}>
                                        @error('company_email')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror    
                                    </div>
                                </div>
                                <div class="col-md-4">        
                                    <div class="form-group">
                                        <label for="perosnal_email">Personal Email</label>
                                        <input type="email" class="form-control" id="perosnal_email" name="personal_email" value="{{!empty($emp->personal_email) ? $emp->personal_email : old('personal_email')}}" placeholder="Enter Personal Email">
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
                                        <label for="emp_bday">Birth Date</label>
                                        <input type="date" class="form-control" id="bday" name="bday" placeholder="Enter Birth Date" value="{{!empty($emp->bday) ? $emp->bday : old('bday')}}">
                                    </div>
                                </div>
                                <div class="col-md-4">        
                                    <div class="form-group">
                                        <label for="linkdin">Linkdin</label>
                                        <input type="text" class="form-control" id="linkdin" name="linkdin" value="{{!empty($emp->linkdin) ? $emp->linkdin : old('linkdin')}}" placeholder="Enter Linkdin">
                                    </div>
                                </div>
                                <div class="col-md-4">    
                                    <div class="form-group">
                                        <label for="pancard">Enter PAN Number</label>
                                        <input type="text" class="form-control" id="pancard" name="pancard" value="{{!empty($emp->pancard) ? $emp->pancard : old('pancard')}}" placeholder="Enter PAN Number">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="project-bg">
                        <h3 class="font-weight-bolder mb-0">Address</h3>
                        <div class="p-4">
                            <div class="row">
                                <div class="col-md-12">     
                                    <div class="form-group">
                                        <label for="postal_address">Postal Address</label>
                                        <textarea class="form-control" id="postal_address" name="postal_address" placeholder="Enter Postal Address" rows="5">{{!empty($emp->postal_address) ? $emp->postal_address : old('postal_address')}}</textarea>
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
                                        <label for="service_start_date">Service Start Date</label>
                                        <input type="date" class="form-control" id="service_start_date" name="service_start_date" value="{{!empty($emp->service_start_date) ? $emp->service_start_date : old('service_start_date')}}" placeholder="Enter Service Start Date">
                                    </div>
                                </div>
                                <div class="col-md-4">        
                                    <div class="form-group">
                                        <label for="service_enddate">Service End Date</label>
                                        <input type="date" class="form-control" id="service_enddate" name="service_enddate" value="{{!empty($emp->service_enddate) ? $emp->service_enddate : old('service_enddate')}}" placeholder="Enter Service Start End Date">
                                    </div>
                                </div>
                                <div class="col-md-4"> 
                                    <div class="form-group">
                                        <label for="department">Department</label>
                                        <select class="form-control" name="department">
                                            <option value="">-- Select Department --</option>
                                            <option value="1" {{ old('department') == '1' ? 'selected' : '' }} @if(!empty($emp)) @if ($emp->department == "1")) selected="selected" @endif @endif>Sales</option>
                                            <option value="2" {{ old('department') == '2' ? 'selected' : '' }} @if(!empty($emp)) @if ($emp->department == "2")) selected="selected" @endif @endif>Production</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">        
                                    <div class="form-group">
                                        <label for="position">Position (Job Title)</label>
                                        <input type="text" class="form-control" id="position" name="position" value="{{!empty($emp->position) ? $emp->position : old('position')}}" placeholder="Enter Position">
                                    </div>
                                </div>
                                <div class="col-md-4">     
                                    <div class="form-group">
                                        <label for="reporting_person">Reporting Person</label>
                                        @php
                                            if(!empty($emp->reporting_person)){
                                                $reporting_person_name = \App\Models\Employee::where('id',$emp->reporting_person)->first();
                                            }
                                        @endphp
                                        <select class="form-control" name="reporting_person">
                                            <option value="">-- Select Reporting Person --</option>
                                            @foreach($emp_list as $empl)
                                                <option value="{{$empl->id}}" {{ old('reporting_person') == $empl->id ? 'selected' : '' }}
                                                    @if(!empty($emp->reporting_person))
                                                        @if ($reporting_person_name->id == $empl->id)) selected="selected"
                                                        @endif
                                                    @endif> 
                                                    {{$empl->full_name}}
                                                </option>
                                            @endforeach 
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Role</label>
                                        <select class="form-control" id="role" name="role">
                                            <option value="1" {{ !empty($emp->role) ? (( $emp->role == "1") ? 'selected' : '') : (old('role') == '1' ? 'selected' : '') }}>Employee</option>
                                            <option value="2" {{ !empty($emp->role) ? (( $emp->role == "2") ? 'selected' : '') : (old('role') == '2' ? 'selected' : '') }}>Admin</option>
                                            <option value="3" {{ !empty($emp->role) ? (( $emp->role == "3") ? 'selected' : '') : (old('role') == '3' ? 'selected' : '') }}>Freelancer</option>
                                        </select>
                                    </div>
                                </div>
                            </div> 
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Sift Type</label>
                                        <select class="form-control" id="sift_type" name="sift_type">
                                            <option value="1" {{ !empty($emp->sift_type) ? (( $emp->sift_type == "1") ? 'selected' : '') : (old('sift_type') == '1' ? 'selected' : '') }}>Regular</option>
                                            <option value="2" {{ !empty($emp->sift_type) ? (( $emp->sift_type == "2") ? 'selected' : '') : (old('sift_type') == '2' ? 'selected' : '') }}>Shift</option>
                                            <option value="3" {{ !empty($emp->sift_type) ? (( $emp->sift_type == "3") ? 'selected' : '') : (old('sift_type') == '3' ? 'selected' : '') }}>Hourly</option>
                                        </select>
                                    </div>

                                    <div class="col-12"
                                         id="hourlySettingDiv"
                                         @if(
                                            (isset($emp) && $emp->sift_type == 3 && $emp->role == 1) ||
                                            (old('sift_type') == 3 && old('role') == 1)
                                         )
                                         @else
                                            style="display:none;"
                                         @endif>

                                        <div class="form-group">
                                            <label>&nbsp;</label><br>
                                            <button type="button" id="showHourlySettings" class="btn btn-info">
                                                Settings
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row" id="hourlySettingsFields" style="display:none;">
                                        <div class="col-md-8">        
                                            <div class="form-group">
                                                <label>Total Weekly Hours</label>
                                                <input type="text" name="total_working_hours" value="{{ old('total_working_hours', $user->total_hours ?? '') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-8">        
                                            <div class="form-group">
                                                <label>Minimum Full Day Hour</label>
                                                <input type="text" name="min_full_day_hour" value="{{ old('min_full_day_hour', $user->min_full_day_hour ?? '') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-8"> 
                                            <div class="form-group">
                                                <label>Minimum Half Day Hour</label>
                                                <input type="text" name="min_half_day_hour" value="{{ old('min_half_day_hour', $user->min_half_day_hour ?? '') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-8"> 
                                            <div class="form-group">
                                                <label>Maximum Carry Forward</label>
                                                <input type="text" name="max_carry_forward" value="{{ old('max_carry_forward', $user->max_carry_forward ?? '') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if(!isset($emp))
                                    <div class="col-md-4">    
                                        <div class="form-group">
                                            <label for="pancard">Password</label>
                                            <input type="password" class="form-control" id="password" name="password" value="" placeholder="Enter Password">
                                            @error('password')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @if(isset($emp))
                        <div class="project-bg">
                            <div class="p-4">
                                <div class="row">
                                    <div class="col-md-12">    
                                        <div class="form-group mb-0">
                                            <label for="upwork_password">Upwork Details
                                                <span class="upwork_profile">
                                                    <input type="checkbox" id="upwork_profile" name="upwork_profile" value="{{$emp->upwork_profile}}" {{ ($emp->upwork_profile) == '1' ? 'checked' : '' }} >
                                                </span>
                                            </label> 
                                            <div class="upwork_password"{{($emp->upwork_profile) == '0' ? 'style=display:none' : ''}}>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group mb-0"> 
                                                            <input type="text" class="form-control" id="upwork_username" name="upwork_username" value="{{!empty($emp->upwork_username) ? $emp->upwork_username : old('upwork_username')}}" placeholder="Enter Upwork User Name">
                                                            <small class="w-100">Upwork User Name</small>
                                                        </div>    
                                                    </div>
                                                    <div class="col-md-4"> 
                                                        <div class="form-group mb-0">
                                                            <input type="text" class="form-control" id="upwork_password" name="upwork_password" value="{{!empty($emp->upwork_password) ? $emp->upwork_password : old('upwork_password')}}" placeholder="Enter Upwork Password">
                                                            <small class="w-100">Upwork Password</small>
                                                        </div>    
                                                    </div>
                                                </div>        
                                            </div>    
                                        </div>
                                    </div>
                                </div>        
                            </div>
                        </div>
                    @endif

                    <div class="card-footer bg-transparent p-0">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
<script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
<script>
    jQuery(document).ready(function() {
        $('#upwork_profile').change(function() {
            if (this.checked) {
                $(".upwork_password").show();
                $(this).val('1');
            } else {
                $(this).val('0');
                $(".upwork_password").hide();
            }
        });
    });      
$(document).ready(function () {

    function toggleSettingsButton() {
        var role = $('#role').val();
        var shift = $('#sift_type').val();

        if (parseInt(role) === 1 && parseInt(shift) === 3) {
            $('#hourlySettingDiv').show();
        } else {
            $('#hourlySettingDiv').hide();
            $('#hourlySettingsFields').hide();
        }
    }

    // run on page load
    toggleSettingsButton();

    // run on change
    $('#role, #sift_type').on('change', function () {
        toggleSettingsButton();
    });

    // toggle settings fields
    $(document).on('click', '#showHourlySettings', function () {
        $('#hourlySettingsFields').slideToggle();
    });

});
</script>