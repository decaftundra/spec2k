@extends('layouts.default')

@section('content')
    <div class="row">
        <div class="col-xs-12 col-sm-8 col-sm-offset-2">
            <h2>Delete Maintenance Notice</h2>
            
            <form class="form-horizontal" method="POST" action="{{ route('maintenance-notice.destroy', $maintenanceNotice->id) }}">
                {{ method_field('DELETE') }}
                {{ csrf_field() }}

                <div class="form-group">
                    <label for="title" class="col-md-4 control-label">Title</label>

                    <div class="col-md-6">
                        <p class="form-control-static">{{ $maintenanceNotice->title }}</p>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="contents" class="col-md-4 control-label">Contents</label>

                    <div class="col-md-6">
                        <p class="form-control-static">{!! nl2br($maintenanceNotice->contents) !!}</p>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <a href="{{ route('maintenance-notice.index') }}" type="button" class="btn btn-warning">
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