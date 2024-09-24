@extends('layouts.default')

@section('content')
    <div class="row">
        <div class="col-xs-12 col-sm-8 col-sm-offset-2">
            
            <h2>Edit User</h2>
                
            <form class="form-horizontal" method="POST" action="{{ route('user.update', $user->id) }}">
                {{ method_field('PUT') }}
                {{ csrf_field() }}

                <div class="form-group{{ $errors->has('first_name') ? ' has-error' : '' }}">
                    <label for="first_name" class="col-md-4 control-label">First Name</label>

                    <div class="col-md-6">
                        <input id="first_name" type="text" class="form-control" name="first_name" value="{{ old('first_name', $user->first_name) }}" required autofocus>

                        @if ($errors->has('first_name'))
                            <span class="help-block">
                                <strong>{{ $errors->first('first_name') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="form-group{{ $errors->has('last_name') ? ' has-error' : '' }}">
                    <label for="last_name" class="col-md-4 control-label">Last Name</label>

                    <div class="col-md-6">
                        <input id="last_name" type="text" class="form-control" name="last_name" value="{{ old('last_name', $user->last_name) }}" required autofocus>

                        @if ($errors->has('last_name'))
                            <span class="help-block">
                                <strong>{{ $errors->first('last_name') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    <label for="email" class="col-md-4 control-label">E-Mail Address</label>

                    <div class="col-md-6">
                        <input id="email" type="email" class="form-control" name="email" value="{{ old('email', $user->email) }}" required>

                        @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="form-group{{ $errors->has('location_id') ? ' has-error' : '' }}">
                    <label for="location_id" class="col-md-4 control-label">Location</label>

                    <div class="col-md-6">
                        <select class="form-control" name="location_id" id="location_id">
                            @if(count($locations))
                                <option value="">Please select...</option>
                                @foreach($locations as $id => $name)
                                    <option value="{{ $id }}" {{ old('location_id', $user->location_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            @endif
                        </select>

                        @if ($errors->has('location_id'))
                            <span class="help-block">
                                <strong>{{ $errors->first('location_id') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('role_id') ? ' has-error' : '' }}">
                    <label for="role_id" class="col-md-4 control-label">Role</label>

                    <div class="col-md-6">
                        <select class="form-control" name="role_id" id="role_id">
                            @if(count($roles))
                                @foreach($roles as $value => $name)
                                    <option value="{{ $value }}" {{ old('role_id', $user->role_id) == $value ? 'selected' : '' }}>{{ ucwords(str_replace('_', ' ', $name)) }}</option>
                                @endforeach
                            @endif
                        </select>

                        @if ($errors->has('role_id'))
                            <span class="help-block">
                                <strong>{{ $errors->first('role_id') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="form-group{{ $errors->has('planner_group') ? ' has-error' : '' }}">
                    <label for="planner_group" class="col-md-4 control-label">Planner Group</label>

                    <div class="col-md-6">
                        <input id="planner_group" type="text" class="form-control" name="planner_group" value="{{ old('planner_group', $user->planner_group) }}" autofocus>

                        @if ($errors->has('planner_group'))
                            <span class="help-block">
                                <strong>{{ $errors->first('planner_group') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <a href="{{ route('user.index') }}" type="button" class="btn btn-warning">
                            <i class="fas fa-chevron-left"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            Save changes <i class="far fa-save"></i>
                        </button>
                    </div>
                </div>
            </form>
                
        </div>
    </div>
@endsection