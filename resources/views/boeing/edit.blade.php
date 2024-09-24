@extends('layouts.default')

@section('content')
    <div class="row">
        <div class="col-xs-12 col-sm-8 col-sm-offset-2">
            
            <h2>Update Boeing Aircraft Data</h2>
            
            <div class="alert alert-danger" role="alert">
    	        <i class="fas fa-exclamation-triangle"></i> Warning. Any changes made to this data will affect the application globally.
	        </div>
                
            <form class="form-horizontal" method="POST" enctype="multipart/form-data" action="{{ route('boeing.update') }}">
                {{ method_field('PUT') }}
                {{ csrf_field() }}

                <div class="form-group{{ $errors->has('file') ? ' has-error' : '' }}">
                    <label for="file" class="col-md-4 control-label">Upload CSV File</label>

                    <div class="col-md-6">
                        <input type="file" class="form-control" name="file" value="{{ old('file') }}" required autofocus>
                        
                        <span id="helpBlock" class="help-block">File must be a MS-DOS comma-separated CSV file.</span>

                        @if ($errors->has('file'))
                            <span class="help-block">
                                <strong>{{ $errors->first('file') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <button type="submit" class="btn btn-primary">
                            Save <i class="far fa-save"></i>
                        </button>
                    </div>
                </div>
            </form>
                
        </div>
    </div>
@endsection