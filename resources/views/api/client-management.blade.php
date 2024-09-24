@extends('layouts.default')

@push('header-scripts')
    <script src="{{ asset('js/app.js') }}" defer></script>
@endpush

@section('content')
    <div id="app" class="row">
        <div class="col-xs-12">
            <h1>API Client Management</h1>
            <passport-clients></passport-clients>
            <passport-authorized-clients></passport-authorized-clients>
            <passport-personal-access-tokens></passport-personal-access-tokens>
        </div>
    </div>
@endsection

@push('footer-scripts')
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script>
        
        
        
        axios.get('/oauth/clients')
        .then(response => {
            console.log(response.data);
        });
        
        
        
        /*
        $.get( "/oauth/clients", function( data ) {
          
          console.log(data);
          
        });
        */
        
    </script>
@endpush