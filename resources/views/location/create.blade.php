@extends('layouts.default')

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <h2 class="col-md-offset-4">Create Repair Station</h2>
            
            <form class="form-horizontal" method="POST" action="{{ route('location.store') }}">
                {{ csrf_field() }}

                <div class="form-group{{ $errors->has('plant_code') ? ' has-error' : '' }}">
                    <label for="plant_code" class="col-md-4 control-label">Plant Code</label>

                    <div class="col-md-6">
                        <input id="plant_code" type="text" class="form-control" name="plant_code" value="{{ old('plant_code') }}" required autofocus>

                        @if ($errors->has('plant_code'))
                            <span class="help-block">
                                <strong>{{ $errors->first('plant_code') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                    <label for="name" class="col-md-4 control-label">Name</label>

                    <div class="col-md-6">
                        <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" autofocus>

                        @if ($errors->has('name'))
                            <span class="help-block">
                                <strong>{{ $errors->first('name') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="form-group{{ $errors->has('sap_location_name') ? ' has-error' : '' }}">
                    <label for="sap_location_name" class="col-md-4 control-label">ERP Name</label>

                    <div class="col-md-6">
                        <input id="sap_location_name" type="text" class="form-control" name="sap_location_name" value="{{ old('sap_location_name') }}" autofocus>

                        @if ($errors->has('sap_location_name'))
                            <span class="help-block">
                                <strong>{{ $errors->first('sap_location_name') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="form-group{{ $errors->has('timezone') ? ' has-error' : '' }}">
                    <label for="timezone" class="col-md-4 control-label">Timezone</label>

                    <div class="col-md-6">
                        <select class="form-control" name="timezone">
                            <option value="">Please select...</option>
                            @foreach ($timezones as $timezone)
                                <?php $selected = old('timezone') == $timezone ? 'selected' : ''; ?>
                                <option value="{{ $timezone }}" {{ $selected }}>{{ $timezone }}</option>
                            @endforeach
                        </select>

                        @if ($errors->has('timezone'))
                            <span class="help-block">
                                <strong>{{ $errors->first('timezone') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                
                @if (count($cageCodes))
                    <div class="form-group{{ $errors->has('cage_codes') ? ' has-error' : '' }}">
                        <label for="cage_codes" class="col-md-4 control-label">Cage Codes</label>
                
                        <div class="col-md-6">
                            @foreach ($cageCodes as $id => $code)
                                
                                <?php $checked = old('cage_codes') && in_array($id, old('cage_codes')) ? 'checked' : ''; ?>
                                
                                <div class="checkbox">
                                  <label>
                                    <input name="cage_codes[]" type="checkbox" value="{{ $id }}" {{ $checked }}>
                                        {{ $code }} - <small class="text-info">{{ App\CageCode::find($id)->info }}</small>
                                  </label>
                                </div>
                            
                            @endforeach
                            
                            @if ($errors->has('cage_codes'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('cage_codes') }}</strong>
                                </span>
                            @endif
                            
                        </div>
                    </div>
                @endif

                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <a href="{{ route('location.index') }}" type="button" class="btn btn-warning">
            	            <i class="fas fa-chevron-left"></i> Cancel
            	        </a>
                        
                        <button type="submit" class="btn btn-primary">
                            Create <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection