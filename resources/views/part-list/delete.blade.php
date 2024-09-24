@extends('layouts.default')

@section('content')
    <div class="row">
        <div class="col-xs-12 col-sm-8 col-sm-offset-2">
            <h2>Delete Excluded Part Number List</h2>
            
            <form class="form-horizontal" method="POST" action="{{ route('part-list.destroy', $partList->id) }}">
                {{ method_field('DELETE') }}
                {{ csrf_field() }}

                <div class="form-group{{ $errors->has('location') ? ' has-error' : '' }}">
                    <label for="location" class="col-md-4 control-label">Location</label>

                    <div class="col-md-6">
                        <p class="form-control-static">{{ $partList->location->name }}</p>
                    </div>
                </div>
                
                <div class="form-group{{ $errors->has('context') ? ' has-error' : '' }}">
                    <label for="context" class="col-md-4 control-label">Context</label>

                    <div class="col-md-6">
                        <p class="form-control-static">{{ $partList->context }}</p>
                    </div>
                </div>
                
                <div class="form-group{{ $errors->has('parts') ? ' has-error' : '' }}">
                    <label for="timezone" class="col-md-4 control-label">Parts</label>

                    <div class="col-md-6">
                        <p class="form-control-static">{{ $partList->parts }}</p>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <a href="{{ route('part-list.index') }}" type="button" class="btn btn-warning">
            	            <i class="fas fa-chevron-left"></i> Cancel
            	        </a>
                        
                        <button type="submit" class="btn btn-danger">
                            Delete <i class="far fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection