@extends('admin.master')

@section('content')
    <div class="content">
        <div class="project-bg">
            <h3 class="font-weight-bolder mb-0">Generate Invoice</h3>
            <div class="p-4"> 
                <!-- @if ($errors->any())
                    <div class="alert alert-danger">There were some problems with your input.<br><br>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif -->
                
                <form action="{{ route('store_invoice') }}" method="POST">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="date" class="form-label">Invoice Date</label>
                            <input type="date" name="date" id="date" class="form-control" value="{{old('date')}}">
                            @error('date')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>    
                        <div class="col-md-4">
                            <label for="billing_date" class="form-label">Billing Date</label>
                            <input type="date" name="billing_date" id="billing_date"  class="form-control" value="{{old('billing_date')}}">
                            @error('billing_date')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="ref_id" class="form-label">Reference ID</label>
                            <input type="text" name="ref_id" class="form-control" value="{{old('ref_id')}}">
                            @error('ref_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>    
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="type" class="form-label">Transaction Types</label>
                            <select name="type" class="form-control">
                                <option value="">-- Select --</option>
                                @foreach ($TransactionType as $id => $name)
                                    <option value="{{ $id }}" {{ old('type') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('type')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="team" class="form-label">Clients</label>
                            <select name="team" class="form-control">
                                <option value="">-- Select --</option>
                                @foreach ($clients as $id => $client)
                                    <option value="{{ $id }}" {{ old('team') == $id ? 'selected' : '' }}>{{ $client }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">
                                <a href="{{ url('client/create') }}">Add Client</a>
                            </div>
                            
                            @error('team')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="account_name" class="form-label" id="account_name">Account Names</label>
                            
                            <select name="account_name" class="form-control">
                                <option value="">-- Select --</option>
                                @foreach ($AccountNames as $id => $AccountName)
                                    <option value="{{ $id }}" {{ old('account_name') == $id ? 'selected' : '' }} >{{ $AccountName }}</option>
                                @endforeach
                            </select>
                            @error('account_name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>   
                    <div class="row mb-3">
                        <div class="col-md-4 currency {{ old('account_name') ? '' : 'd-none' }} ">
                            @php
                                $selectedAccount = old('account_name');
                                $currencyVal = old('currency');
                                
                            @endphp
                            <label for="type" class="form-label">Currency</label>
                            <select name="currency" class="form-control" id="currency">
                                <option value="">-- Select --</option>
                                @foreach ($Currency as  $name)
                                    <option value="{{ $name->code }}"
                                        {{ old('currency') == $name->code ? 'selected' : '' }} 
                                        
                                        {{-- If account is NOT 4, disable only INR --}}
                                        @if($selectedAccount != 4 && $name->code == 'INR')
                                            disabled
                                        @endif

                                        {{-- If account is 4, disable all except INR --}}
                                        @if($selectedAccount == 4 && $name->code != 'INR')
                                            disabled
                                        @endif
                                        
                                    >
                                        {{ $name->name }} ({!! $name->html_symbol !!})
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">
                                <a href="{{ url('currency/create') }}">Add Currency</a>
                            </div>
                            @error('currency')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror

                        </div>
                        <div class="col-md-4 business_location {{ old('currency') == 'INR' ? '' : 'd-none' }}">
                            <label for="business_location" class="form-label">Business Location</label>
                            <select name="business_location" class="form-control">
                                <option value="">-- Select --</option>
                                <option value="in_g" {{ old('business_location') == 'in_g' ? 'selected' : '' }}>Inside Gujarat</option>
                                <option value="out_g" {{ old('business_location') == 'out_g' ? 'selected' : '' }}>Outside Gujarat</option>
                            </select>
                            @error('business_location')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="amount" class="form-label">Amount</label>
                            <input type="number" step="0.01" name="amount" class="form-control" value="{{old('amount')}}">
                            @error('amount')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3 inside-g {{ old('business_location') == 'in_g' ? '' : 'd-none' }}">
                        <div class="col-md-6">
                            <label for="CGST" class="form-label">CGST(%)</label>
                            <input type="number" step="0.01" name="CGST" class="form-control" value="{{ old('CGST', 9) }}">
                            @error('CGST')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="SGST" class="form-label">SGST(%)</label>
                            <input type="number" step="0.01" name="SGST" class="form-control" value="{{old('SGST',9)}}">
                            @error('SGST')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div> 
                    </div>
                    <div class="mb-3 outside-g d-none">
                        <label for="IGST" class="form-label">IGST(%)</label>
                        <input type="number" step="0.01" name="IGST" class="form-control" value="{{old('IGST',18)}}">
                        @error('IGST')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                    </div>    
            
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="hsn" class="form-label">HSN/SAC</label>
                            <input type="text" name="hsn" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" class="form-control"></textarea>
                        </div>    
                    </div>

                    <button type="submit" class="btn btn-primary">Add Invoice</button>
                </form>
            </div>
        </div>
    </div>
    <style>
        #currency option:disabled {
          background-color: #f8f9fa;
          color: #adb5bd;
        }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        
        jQuery(".business_location select").change(function(){
            var val = jQuery(this).val();
            
            if (val === 'in_g') {
                jQuery(".inside-g").removeClass('d-none');
                jQuery(".outside-g").addClass('d-none');
            } else if (val === 'out_g') {
                jQuery(".outside-g").removeClass('d-none');
                jQuery(".inside-g").addClass('d-none');
            } else {
                jQuery(".inside-g, .outside-g").addClass('d-none');
            }
        });

        /*jQuery('select[name="account_name"]').on('change', function () {
            var selectedAccount = jQuery(this).val();


            if(selectedAccount){
                jQuery(".currency").removeClass("d-none");
                
                jQuery('#currency option').each(function () {
                    var currency_element = jQuery(this);
                    var currencyVal = jQuery(this).val();
                    
                    if(currencyVal){
                        if (selectedAccount === '4') {
                            jQuery(this).prop('disabled', currencyVal !== 'INR' && currencyVal !== '');
                            console.log(currencyVal);
                            if (currencyVal == 'INR') {
                                console.log("inr-in");
                                jQuery(this).prop('disabled', false);
                                setTimeout(() => {
                                    jQuery(this).prop('selected', true);
                                }, 500);
                            }
                        } else {
                            jQuery(this).prop('disabled', currencyVal === 'INR');
                        }
                    }
                    
                });

            }else{
                jQuery(".currency").addClass("d-none");
                jQuery(".business_location").addClass("d-none");
            }    

            jQuery('#currency').val('');

            // Refresh UI if using bootstrap-select
            if (jQuery('#currency').hasClass('selectpicker')) {
                jQuery('#currency').selectpicker('refresh');
            }
        });*/

        jQuery('select[name="account_name"]').on('change', function () {
            var selectedAccount = jQuery(this).val();

            if (selectedAccount) {
                jQuery(".currency").removeClass("d-none");

                // Reset currency dropdown first
                jQuery('#currency').val('');
                jQuery('#currency option').prop('disabled', false);

                jQuery('#currency option').each(function () {
                    var currencyVal = jQuery(this).val();

                    if (currencyVal) {
                        if (selectedAccount === '4') {
                            // For account 4, only INR should be selectable
                            if (currencyVal !== 'INR' && currencyVal !== '') {
                                jQuery(this).prop('disabled', true);
                            } else if (currencyVal === 'INR') {
                                jQuery(this).prop('selected', true);
                            }
                        } 
                        else if (selectedAccount === '5') {
                            // For account FL, enable all currencies, set INR as default
                            jQuery(this).prop('disabled', false);
                            if (currencyVal === 'INR') {
                                jQuery(this).prop('selected', true);
                            }
                        }
                        else if (selectedAccount === '1' || selectedAccount === '2') {
                            // Only USD enabled
                            jQuery(this).prop('disabled', currencyVal !== 'USD' && currencyVal !== '');
                            if (currencyVal === 'USD') {
                                jQuery(this).prop('selected', true);
                                jQuery(".business_location").addClass("d-none");
                            }
                        }
                        else if (selectedAccount === '3') {
                            // Only USD enabled
                            jQuery(this).prop('disabled', currencyVal === 'INR');
                            
                        }
                        else {
                            // For other accounts, INR is disabled
                            if (currencyVal === 'INR') {
                                jQuery(this).prop('disabled', true);
                            }
                        }
                    }
                });

                var value = jQuery('#currency').val();
                if (value === 'INR') {
                    console.log('INR selected');
                   jQuery(".business_location").removeClass("d-none"); 
                }else{
                   jQuery(".business_location").addClass("d-none"); 
                }
                // Refresh UI if using bootstrap-select
                if (jQuery('#currency').hasClass('selectpicker')) {
                    jQuery('#currency').selectpicker('refresh');
                }

                // Show dependent field (example: business location)
                // jQuery(".business_location").removeClass("d-none");

            } else {
                // Hide fields if no account selected
                jQuery(".currency").addClass("d-none");
                jQuery(".business_location").addClass("d-none");
            }
        });



        jQuery("#currency").change(function(){
            var val = jQuery(this).val();
            
            if(val == 'INR'){
                jQuery(".business_location").removeClass('d-none');
            }
            else {
                jQuery(".business_location").addClass('d-none');
                jQuery(".inside-g, .outside-g").addClass('d-none');
                jQuery(".business_location select").val('');
            }
        });

    </script>
@endsection



