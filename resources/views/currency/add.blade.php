@extends('admin.master')

@section('content')
    <div class="content">
        <h2>Currency</h2>
        @if ($errors->any())
            <div class="alert alert-danger">There were some problems with your input.<br><br>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ isset($currency) ? route('currency.update', $currency->id) : route('currency.store') }}" method="POST">
            @csrf

            @if(isset($currency))
                @method('PUT')
            @endif
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="name" class="form-label">Currency Name</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="United States" value="{{ old('name', $currency->name ?? '') }}">
                </div>    
                <div class="col-md-4">
                    <label for="code" class="form-label">Currency Code</label>
                    <input type="text" name="code" id="code"  class="form-control" placeholder="USD" value="{{ old('code', $currency->code ?? '') }}" @if(isset($currency)) disabled @endif>
                </div>
                <div class="col-md-4">
                    <label for="html_symbol" class="form-label">Currency Html Symbol</label>
                    <input type="text" name="html_symbol" id="html_symbol"  class="form-control" placeholder="&amp;#36;" value="{{ old('html_symbol', $currency->html_symbol ?? '') }}">
                </div>    
            </div>
            <button type="submit" class="btn btn-primary">{{ isset($currency) ? 'Update Currency'  : 'Add Currency' }}</button>
        </form>
    </div>
@endsection