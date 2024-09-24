@extends('layouts.default')

@section('content')
    <div class="row">
        <div class="col-xs-12 col-sm-8 col-sm-offset-2">
            <h2>Edit Issue</h2>

            <form class="form-horizontal" method="POST" action="{{ route('issue-tracker.update', $issue->id) }}">
                
                {{ method_field('PUT') }}
                
                {{ csrf_field() }}

                <div class="form-group">
                    <label for="title" class="col-md-4 control-label">Title</label>

                    <div class="col-md-6">
                        <p class="form-control-static">{{ $issue->getTitle() }}</p>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="content" class="col-md-4 control-label">Content</label>

                    <div class="col-md-6">
                        <p class="form-control-static">{!! $issue->getContent() !!}</p>

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
                                    <option value="{{ $id }}" {{ old('priority', $issue->kind) == $id ? 'selected' : '' }}>{{ $name }}</option>
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
                                    <option value="{{ $id }}" {{ old('priority', $issue->priority) == $id ? 'selected' : '' }}>{{ $name }}</option>
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
                
                <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
                    <label for="priority" class="col-md-4 control-label">Status <span class="text-danger">*</span></label>

                    <div class="col-md-6">
                        
                        <select class="form-control" name="status" id="status">
                            @if(count($statuses))
                                <option value="">Please select...</option>
                                @foreach($statuses as $id => $name)
                                    <option value="{{ $id }}" {{ old('status', $issue->status) == $id ? 'selected' : '' }}>{{ ucfirst($id) }}</option>
                                @endforeach
                            @endif
                        </select>

                        @if ($errors->has('status'))
                            <span class="help-block">
                                <strong>{{ $errors->first('status') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                
                @if (!empty($issue->comments) && count($issue->comments))
                    <div class="form-group">
                        <label for="comments" class="col-md-4 control-label">Comments</label>
                        
                        <div class="col-md-6">
                            @foreach ($issue->comments as $comment)
                                <p class="form-control-static">{{ nl2br($issue->content) }}</p>
                                <hr>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                <div class="form-group">
                    <label for="content" class="col-md-4 control-label">Add Comment</label>

                    <div class="col-md-6">
                        <textarea id="comment"class="form-control" rows="5" name="comment" autofocus>{{ old('comment') }}</textarea>

                        @if ($errors->has('comment'))
                            <span class="help-block">
                                <strong>{{ $errors->first('comment') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>


                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <a class="btn btn-info" href="{{ route('issue-tracker.index') }}"><i class="fas fa-chevron-left"></i> Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            Update <i class="fas fa-pencil-alt"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection