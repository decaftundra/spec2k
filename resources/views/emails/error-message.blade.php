@extends('layouts.email')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <h1 class="text-danger">Spec2kApp Error Message</h1>
            
    		<div class="panel panel-danger">
        		<div class="panel-heading">{{ $messageName }}</div>
                <div class="panel-body">
                
                    <p>Hi {{ $user->first_name }}</p>
		
            		<p>An {{ get_class($exception) }} error has occured in the <strong>{{ config('app.name') }} {{ config('app.env') }}</strong> environment, in file:
            		<strong>{{ $exception->getFile() }}</strong> at line <strong>{{ $exception->getLine() }}</strong></p>
            		
            		@if ($exception->getMessage())
                		<h2>Message</h2>
                		<p>{{ $exception->getMessage() }}</p>
            		@endif
            		
            		@if ($user->isDataAdmin())
                		<h2>Stack Trace:</h2>
                		<p>{{ $exception->getTraceAsString() }}</p>
            		@endif
                </div>
            </div>
    		
    		<p><small>You can change your message settings here: <a href="{{ route('message.edit') }}">Edit Message Settings</a></small></p>
        </div>
    </div>
</div>
@endsection