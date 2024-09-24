@extends('layouts.default')

@section('content')
    <div class="row">
        <div class="col-xs-12 col-sm-8 col-sm-offset-2">
            <h2>Edit Excluded Part Number List</h2>
            
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-triangle"></i> <strong>Warning!</strong> Any notifications using the part numbers listed below will be moved to the deleted listing page and will have to be un-deleted manually or by support.
            </div>
            
            <form class="form-horizontal" method="POST" action="{{ route('part-list.update', $partList) }}">
                {{ method_field('PUT') }}
                {{ csrf_field() }}
                
                <div class="form-group{{ $errors->has('location_id') ? ' has-error' : '' }}">
                    <label for="location_id" class="col-md-4 control-label">Location</label>

                    <div class="col-md-6">
                        <p class="form-control-static">{{ $partList->location->name }}</p>
                    </div>
                </div>
                
                <div class="form-group{{ $errors->has('parts') ? ' has-error' : '' }}">
                    <label for="parts" class="col-md-4 control-label">Exclude these parts</label>

                    <div class="col-md-6">
                        <textarea class="form-control" rows="20" name="parts" placeholder="List of part numbers to exclude separated by a comma">{{ old('parts', $partList->parts) }}</textarea>
                        
                        <p class="help-block">List of part numbers to exclude separated by a comma. <br/>An asterisk '*' wildcard can be used to specify any character.</p>

                        @if ($errors->has('parts'))
                            <span class="help-block">
                                <strong>{{ $errors->first('parts') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <a href="{{ route('part-list.index') }}" type="button" class="btn btn-warning">
            	            <i class="fas fa-chevron-left"></i> Cancel
            	        </a>
                        
                        <button type="submit" class="btn btn-primary">
                            Update <i class="far fa-save"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection