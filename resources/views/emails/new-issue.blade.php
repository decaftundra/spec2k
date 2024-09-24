@extends('layouts.email')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <h1>New issue raised.</h1>
            
            <p>A new issue has been raised on Spec2kApp {{ config('app.env') }} by {{ $issue->getPostedBy() }}</p>
            
            <h3>{{ $issue->getTitle() }}</h3>
            
            <ul class="list-group">
                <li class="list-group-item">{!! $issue->getStatus() !!}</li>
                <li class="list-group-item">{!! $issue->getKind() !!}</li>
                <li class="list-group-item {{ in_array($issue->priority, ['major', 'critical', 'blocker']) ? 'text-danger' : '' }}">{!! $issue->getPriority() !!}</li>
            </ul>
            
            <h3>Description</h3>
            
            <div class="well well-lg">
                <p>{!! $issue->getContent() !!}</p>
            </div>
            
            <a style="margin-bottom:20px;" class="btn btn-primary" href="{{ route('issue-tracker.index') }}">View Issues</a>
        </div>
    </div>
</div>
@endsection