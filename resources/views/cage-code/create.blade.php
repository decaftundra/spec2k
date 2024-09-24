@extends('layouts.default')

@section('content')
    <div class="row">
        <div class="col-xs-12 col-sm-8 col-sm-offset-2">
            <h2>Create Cage Code</h2>
            
            <form class="form-horizontal" method="POST" action="{{ route('cage-code.store') }}">
                {{ csrf_field() }}

                <div class="form-group{{ $errors->has('cage_code') ? ' has-error' : '' }}">
                    <label for="cage_code" class="col-md-4 control-label">Cage Code</label>

                    <div class="col-md-6">
                        <input id="cage_code" type="text" class="form-control" name="cage_code" value="{{ old('cage_code') }}" required autofocus>

                        @if ($errors->has('cage_code'))
                            <span class="help-block">
                                <strong>{{ $errors->first('cage_code') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="form-group{{ $errors->has('info') ? ' has-error' : '' }}">
                    <label for="info" class="col-md-4 control-label">Info</label>

                    <div class="col-md-6">
                        <textarea id="info" name="info" rows="3" placeholder="" class="form-control" >{{ old('info') }}</textarea autofocus>

                        @if ($errors->has('info'))
                            <span class="help-block">
                                <strong>{{ $errors->first('info') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <a href="{{ route('cage-code.index') }}" type="button" class="btn btn-warning">
            	            <i class="fas fa-chevron-left"></i> Cancel
            	        </a>
                        
                        <button type="submit" class="btn btn-primary">
                            Create <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection