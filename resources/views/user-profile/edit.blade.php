@extends('layouts.default')

@section('content')
    <div class="row">
        <div class="col-xs-12 col-sm-8 col-sm-offset-2">

            <h2>Edit Details</h2>

            <div class="panel-body">
                <form class="form-horizontal" method="POST" action="{{ route('user-profile.update') }}">
                    {{ method_field('PUT') }}
                    {{ csrf_field() }}

                    <div class="form-group{{ $errors->has('first_name') ? ' has-error' : '' }}">
                        <label for="first_name" class="col-md-4 control-label">First name</label>

                        <div class="col-md-6">
                            <input type="text" class="form-control" name="first_name" value="{{ old('first_name', auth()->user()->first_name) }}" required>

                            @if ($errors->has('first_name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('first_name') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="form-group{{ $errors->has('last_name') ? ' has-error' : '' }}">
                        <label for="last_name" class="col-md-4 control-label">Last name</label>

                        <div class="col-md-6">
                            <input type="text" class="form-control" name="last_name" value="{{ old('last_name', auth()->user()->last_name) }}" required>

                            @if ($errors->has('last_name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('last_name') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                        <label for="email" class="col-md-4 control-label">Email</label>

                        <div class="col-md-6">
                            <input type="email" class="form-control" name="email" value="{{ old('email', auth()->user()->email) }}" required>

                            @if ($errors->has('email'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="far fa-save"></i> Update Details
                            </button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
@endsection