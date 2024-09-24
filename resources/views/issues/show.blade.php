@extends('layouts.default')

@section('content')
    <div class="row">
        <div class="col-xs-12 col-sm-8 col-sm-offset-2">
            <h1>{{ $issue->getTitle() }} <small>Posted by: {{ $issue->getPostedBy() }}</small></h1>
            
            <ul class="list-group">
                <li class="list-group-item">{!! $issue->getStatus() !!}</li>
                <li class="list-group-item">{!! $issue->getKind() !!}</li>
                <li class="list-group-item">{!! $issue->getPriority() !!}</li>
            </ul>
			
			<h3>Description</h3>
			
			<div class="well well-lg">
    			<p>{!! $issue->getContent() !!}</p>
			</div>
            
            @if (!empty($issue->comments) && count($issue->comments))
                
                <h3>Comments</h3>
                
                @foreach ($issue->comments as $comment)
                    
                    <div class="well well-sm">
                        <span class="text-muted">{{ date('d/m/y', strtotime($comment->created_at)) }}</span> : {{ nl2br(ucfirst($comment->content)) }}
                    </div>
                @endforeach
            @else
                <p>No comments found.</p>
            @endif
            
            <a style="margin-bottom:20px;" class="btn btn-primary" href="{{ route('issue-tracker.index') }}">
                <i class="fas fa-chevron-left"></i> Issues
            </a>
        </div>
    </div>
@endsection
