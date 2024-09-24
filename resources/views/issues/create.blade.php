@extends('layouts.default')

@section('content')
    <div class="row">
        <div class="col-xs-12 col-sm-8 col-sm-offset-2">
            <h2>Create New Issue</h2>

            <form class="form-horizontal" method="POST" action="{{ route('issue-tracker.store') }}">
                {{ csrf_field() }}

                <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                    <label for="title" class="col-md-4 control-label">Title <span class="text-danger">*</span></label>

                    <div class="col-md-6">
                        <input id="title" type="text" class="form-control" name="title" value="{{ old('title') }}" required autofocus>

                        @if ($errors->has('title'))
                            <span class="help-block">
                                <strong>{{ $errors->first('title') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="form-group{{ $errors->has('content') ? ' has-error' : '' }}">
                    <label for="content" class="col-md-4 control-label">Content <span class="text-danger">*</span></label>

                    <div class="col-md-6">
                        <textarea id="content"class="form-control" rows="5" name="content" required autofocus>{{ old('content') }}</textarea>

                        @if ($errors->has('content'))
                            <span class="help-block">
                                <strong>{{ $errors->first('content') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('kind') ? ' has-error' : '' }}">
                    <label for="kind" class="col-md-4 control-label">Type <span class="text-danger">*</span></label>

                    <div class="col-md-6">
                        
                        <select class="form-control" name="kind" id="kind">
                            @if(count($kinds))
                                <option value="">Please select...</option>
                                @foreach($kinds as $id => $name)
                                    <option value="{{ $id }}" {{ old('priority') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            @endif
                        </select>

                        @if ($errors->has('kind'))
                            <span class="help-block">
                                <strong>{{ $errors->first('kind') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="form-group{{ $errors->has('priority') ? ' has-error' : '' }}">
                    <label for="priority" class="col-md-4 control-label">Priority <span class="text-danger">*</span></label>

                    <div class="col-md-6">
                        
                        <select class="form-control" name="priority" id="priority">
                            @if(count($priorities))
                                <option value="">Please select...</option>
                                @foreach($priorities as $id => $name)
                                    <option value="{{ $id }}" {{ old('priority') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            @endif
                        </select>

                        @if ($errors->has('priority'))
                            <span class="help-block">
                                <strong>{{ $errors->first('priority') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <a class="btn btn-info" href="{{ route('issue-tracker.index') }}"><i class="fas fa-chevron-left"></i> Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            Create <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection