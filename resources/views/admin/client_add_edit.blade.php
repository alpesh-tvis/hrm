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
                    
                    
                    @if(isset($clis))
                    <form action="{{ route('client.update',$clis) }}" method="POST">
                        @csrf
                        @method('PUT')
                        @else
                        <form action="{{ route('client.store') }}" method="POST">
                            @csrf
                            
                            @endif
                            
                            
                            <div class="card-body">
                                <div class="project-bg">
                                  <h3 class="font-weight-bolder mb-0">Personal Details</h3>
                                    <div class="p-4">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="firsname">First Name</label>
                                                    <input type="Text" class="form-control @error('first_name') is-invalid @enderror" id="firstname" name="first_name" value="{{!empty($clis->first_name) ? $clis->first_name : old('first_name')}}"  placeholder="Enter First name">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="lastname">Last Name</label>
                                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="lastname" name="last_name" value="{{!empty($clis) ? $clis->last_name : old('last_name')}}" placeholder="Enter Last name">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="mobile">Mobile</label>
                                                    <input type="text" class="form-control" id="mobile" name="mobile" value="{{!empty($clis) ? $clis->mobile : old('mobile')}}" placeholder="Enter Mobile Number">
                                                </div>
                                            </div>
                                        </div>
                                   
                                
                                <div class="row">
                                    <!--                               <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="fullname">Full Name</label>
                                            <input type="text" class="form-control" id="fullname" name="full_name" value="{{!empty($clis) ? $clis->full_name : ''}}" placeholder="Enter Full name">
                                        </div>
                                    </div> -->
                                    
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="email">Personal Email</label>
                                            <input type="text" class="form-control" id="email" name="email" value="{{!empty($clis) ? $clis->email : old('email')}}" placeholder="Enter Personal Email">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="linkdin">Linkdin</label>
                                            <input type="text" class="form-control" id="linkdin" name="linkdin" value="{{!empty($clis) ? $clis->linkdin : old('linkdin')}}" placeholder="Enter Linkdin">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="company">Company</label>
                                            <input type="text" class="form-control" id="p_company" name="p_company" value="{{!empty($clis) ? $clis->p_company : old('p_company')}}" placeholder="Enter Company Name">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <!-- <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="address">Address</label>
                                            <textarea class="form-control" id="b_address" name="b_address" placeholder="Enter Address">{{!empty($clis) ? $clis->b_address : ''}}</textarea>
                                        </div>
                                        
                                    </div> -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="source_portal">Source Portal</label>
                                            <input type="text" class="form-control" id="source_portal" name="source_portal" value="{{!empty($clis) ? $clis->source_portal : old('source_portal')}}" placeholder="Enter Source Portal">
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="billing_address">Other Details</label>
                                            <textarea class="form-control" id="other_details" name="other_details" placeholder="Enter Other Details">{{!empty($clis) ? $clis->other_details : old('other_details')}}</textarea>
                                        </div>
                                    </div>
                                    
                                    
                                </div>
                                 </div>
                               </div>
                             <div class="project-bg">
                                <h3 class="font-weight-bolder mb-0">Address</h3>
                                <div class="p-4">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="address_line1">Address Line 1</label>
                                                <input type="text" class="form-control" id="address_line1" name="address_line1" value="{{!empty($clis) ? $clis->address_line1 : old('address_line1')}}" placeholder="Enter Address Line 1">
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="address_line2">Address Line 2</label>
                                                <input type="text" class="form-control" id="address_line2" name="address_line2" value="{{!empty($clis) ? $clis->address_line2 : old('address_line2')}}" placeholder="Enter Address Line 2">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="p_city">City/Distict</label>
                                                <input type="text" class="form-control" id="p_city" name="p_city" value="{{!empty($clis) ? $clis->p_city : old('p_city')}}" placeholder="Enter City/Distict">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="p_state">State/Province</label>
                                                <input type="text" class="form-control" id="p_state" name="p_state" value="{{!empty($clis) ? $clis->p_state : old('p_state')}}" placeholder="Enter State/Province">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="p_postalcode">Postal Code</label>
                                                <input type="text" class="form-control" id="p_postalcode" name="p_postalcode" value="{{!empty($clis) ? $clis->p_postalcode : old('p_postalcode')}}" placeholder="Enter Postal Code">
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="p_country">Country</label>
                                                <input type="text" class="form-control" id="p_country" name="p_country" value="{{!empty($clis) ? $clis->p_country : old('p_country')}}" placeholder="Enter Country">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="project-bg">
                                <h3 class="font-weight-bolder mb-0">Billing Details</h3>
                                <div class="p-4">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="company_name">Company Name</label>
                                            
                                            <input type="text" class="form-control @error('company') is-invalid @enderror" id="company" name="company" value="{{!empty($clis) ? $clis->company : old('company')}}" placeholder="Enter Company Name">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="b_name">Name</label>
                                            <input type="text" class="form-control @error('b_name') is-invalid @enderror" id="b_name" name="b_name" value="{{!empty($clis) ? $clis->b_name : old('b_name')}}" placeholder="Enter Billing Name">
                                        </div>
                                    </div>
                                     <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="b_email">Email</label>
                                            <input type="text" class="form-control" id="b_email" name="b_email" value="{{!empty($clis) ? $clis->b_email : old('b_email')}}" placeholder="Enter Billing Email">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="vat">VAT</label>
                                            <input type="text" class="form-control" id="b_vat" name="b_vat" value="{{!empty($clis) ? $clis->b_vat : old('b_vat')}}" placeholder="Enter VAT">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="b_mobile">Mobile</label>
                                            <input type="text" class="form-control" id="b_mobile" name="b_mobile" value="{{!empty($clis) ? $clis->b_mobile : old('b_mobile')}}" placeholder="Enter Billing Mobile">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="billing_address">Billing Address</label>
                                            <textarea class="form-control @error('billing_address') is-invalid @enderror" id="billing_address" name="billing_address" placeholder="Enter Billing Address">{{!empty($clis) ? $clis->billing_address : old('billing_address')}}</textarea>
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