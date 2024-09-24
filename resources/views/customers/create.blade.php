@extends('layouts.default')

@section('content')
    <div class="row">
        <div class="col-xs-12 col-sm-8 col-sm-offset-2">
            <h2>Create Customer</h2>
            
            <form class="form-horizontal" method="POST" action="{{ route('customer.store') }}">
                {{ csrf_field() }}

                <div class="form-group{{ $errors->has('company_name') ? ' has-error' : '' }}">
                    <label for="company_name" class="col-md-4 control-label">Company Name</label>

                    <div class="col-md-6">
                        <input id="company_name" type="text" class="form-control" name="company_name" value="{{ old('company_name') }}" required autofocus>

                        @if ($errors->has('company_name'))
                            <span class="help-block">
                                <strong>{{ $errors->first('company_name') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="form-group{{ $errors->has('icao') ? ' has-error' : '' }}">
                    <label for="icao" class="col-md-4 control-label">ICAO Code</label>

                    <div class="col-md-6">
                        <input id="icao" type="text" class="form-control" name="icao" value="{{ old('icao') }}" autofocus>
                        
                        <span id="helpBlock" class="help-block">If no ICAO code leave blank, the code will be automatically replaced with 'ZZZZZ'.</span>

                        @if ($errors->has('icao'))
                            <span class="help-block">
                                <strong>{{ $errors->first('icao') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <button type="submit" class="btn btn-primary">
                            Create <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection