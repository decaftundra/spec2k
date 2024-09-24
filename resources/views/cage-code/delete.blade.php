@extends('layouts.default')

@section('content')
    <div class="row">
        <div class="col-xs-12 col-sm-8 col-sm-offset-2">
            <h2>Delete Cage Code</h2>
            
            <form class="form-horizontal" method="POST" action="{{ route('cage-code.destroy', $cageCode->id) }}">
                {{ method_field('DELETE') }}
                {{ csrf_field() }}

                <div class="form-group{{ $errors->has('cage_code') ? ' has-error' : '' }}">
                    <label for="cage_code" class="col-md-4 control-label">Cage Code</label>

                    <div class="col-md-6">
                        <p class="form-control-static">{{ $cageCode->cage_code }}</p>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <a href="{{ route('cage-code.index') }}" type="button" class="btn btn-warning">
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