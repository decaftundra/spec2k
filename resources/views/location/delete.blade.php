@extends('layouts.default')

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <h2>Delete Repair Station</h2>
            
            <form class="form-horizontal" method="POST" action="{{ route('location.destroy', $location->id) }}">
                {{ method_field('DELETE') }}
                {{ csrf_field() }}

                <div class="form-group{{ $errors->has('plant_code') ? ' has-error' : '' }}">
                    <label for="plant_code" class="col-md-4 control-label">Plant Code</label>

                    <div class="col-md-6">
                        <p class="form-control-static">{{ $location->plant_code }}</p>
                    </div>
                </div>
                
                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                    <label for="name" class="col-md-4 control-label">Name</label>

                    <div class="col-md-6">
                        <p class="form-control-static">{{ $location->name }}</p>
                    </div>
                </div>
                
                <div class="form-group{{ $errors->has('sap_location_name') ? ' has-error' : '' }}">
                    <label for="sap_location_name" class="col-md-4 control-label">ERP Name</label>

                    <div class="col-md-6">
                        <p class="form-control-static">{{ $location->sap_location_name }}</p>
                    </div>
                </div>
                
                <div class="form-group{{ $errors->has('timezone') ? ' has-error' : '' }}">
                    <label for="timezone" class="col-md-4 control-label">Timezone</label>

                    <div class="col-md-6">
                        <p class="form-control-static">{{ $location->timezone }}</p>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <a href="{{ route('location.index') }}" type="button" class="btn btn-warning">
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