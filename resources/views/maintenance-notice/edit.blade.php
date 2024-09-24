@extends('layouts.default')

@section('content')
    <div class="row">
        <div class="col-xs-12 col-sm-8 col-sm-offset-2">
            <h2>Edit Maintenance Notice</h2>
            
            <form class="form-horizontal" method="POST" action="{{ route('maintenance-notice.update', $maintenanceNotice->id) }}">
                {{ method_field('PUT') }}
                {{ csrf_field() }}

                <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                    <label for="title" class="col-md-4 control-label">Title</label>

                    <div class="col-md-6">
                        <input id="title" type="text" class="form-control" name="title" value="{{ old('title', $maintenanceNotice->title) }}" required autofocus>

                        @if ($errors->has('title'))
                            <span class="help-block">
                                <strong>{{ $errors->first('title') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="form-group{{ $errors->has('contents') ? ' has-error' : '' }}">
                    <label for="contents" class="col-md-4 control-label">Message</label>

                    <div class="col-md-6">
                        <textarea class="form-control" id="contents" name="contents" rows="5">{{ old('contents', $maintenanceNotice->contents) }}</textarea>

                        @if ($errors->has('contents'))
                            <span class="help-block">
                                <strong>{{ $errors->first('contents') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="cage_codes" class="col-md-4 control-label">Display Notice</label>
                
                    <div class="col-md-6">
                        <div class="checkbox">
                            <label>
                                <?php $checked = old('display', $maintenanceNotice->display) ? 'checked' : ''; ?>
                                
                                <input name="display" type="checkbox" value="1" {{ $checked }}>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <a href="{{ route('maintenance-notice.index') }}" type="button" class="btn btn-warning">
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