@extends('layouts.default')

@section('content')
    <div class="row">
        <div class="col-xs-12 col-sm-8 col-sm-offset-2">
            <h2>Delete User</h2>
        
			<h3>Are you sure you want to delete the user: <strong>{{ $user->fullname }}</strong>?</h3>
			
			<p>
    			<strong>Name:</strong> {{ $user->fullname }}<br>
    			<strong>Email:</strong> {{ $user->email }}<br>
    			<strong>Role:</strong> {{ ucfirst($user->role->name) }}<br>
			</p>
            
            <form class="form-horizontal" action="{{ route('user.destroy', $user->id) }}" method="POST">
    			
    			{{ method_field('DELETE') }}
    			{{ csrf_field() }}
    			
                <div class="col-sm-12 form-group">
    	            <hr/>
    	            <a href="{{ route('user.index') }}" type="button" class="btn btn-warning">
        	            <i class="fas fa-chevron-left"></i> Cancel
        	        </a>
                    <button type="submit" class="btn btn-danger">
                        Delete <i class="far fa-trash-alt"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection