@extends('layouts.default')

@section('content')
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-sm-offset-4">

            <h2>Edit Message Settings</h2>
            
            

            <div class="panel-body">
                <p>Choose which messages you wish to receive.</p>
                
                <form class="form-horizontal" method="POST" action="{{ route('message.update') }}">
                    {{ method_field('PUT') }}
                    {{ csrf_field() }}

                    <div class="form-group{{ $errors->has('first_name') ? ' has-error' : '' }}">
                        
                        @if (count($messages))
                            <div class="col-md-12">
                                
                                @foreach ($messages as $message)
                                    <div class="checkbox">
                                        <label>
                                            <input name="messages[]" type="checkbox" value="{{ $message->id }}" {{ in_array($message->id, $userMessages) ? 'checked' : '' }}>
                                            {{ $message->name }}
                                        </label>
                                    </div>
                                @endforeach
                                
                            </div>
                        @endif
                        
                    </div>

                    <div class="form-group">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="far fa-save"></i> Update
                            </button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
@endsection