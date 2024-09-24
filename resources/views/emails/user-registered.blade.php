@extends('layouts.email')

@section('content')
<div>
    <div>
        <div>
            <h1>New user account registered.</h1>
            
            <p>Hi {{ $user->first_name }}</p>
		
    		<p>You have been registered on the <strong>{{ config('app.name') }}</strong>.</p>
    		
    		<p>Before you can login you will need to reset your password by clicking on the URL below (or copying it into a browser).</p>
    		
    		<p>{{ route('password.request').'?email='.$user->email }}</p>


        </div>
    </div>
</div>
@endsection