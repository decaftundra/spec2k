@extends('layouts.email')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <h1>Reset your password.</h1>
            
            <p>Hi {{ $user->first_name }}</p>
		
    		<p>A password reset has been requested on your account, if you did not request a password reset please ignore this email.</p>
    		
    		<p>To reset your password click the button below. <strong>This link will expire in 60 minutes.</strong></p>
    		
    		<p>
        		<a class="btn btn-primary" href="{{ route('password.reset', $token) }}">
            		<i class="fas fa-key"></i> Reset password
                </a>
            </p>
    		
    		<p><small>If you're having trouble clicking the "Reset password" button, copy and paste the URL below into your web browser:
    		{{ route('password.reset', $token) }}</small></p>
        </div>
    </div>
</div>
@endsection