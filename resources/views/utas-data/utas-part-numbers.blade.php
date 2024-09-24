@extends('layouts.default')

@section('content')
    <div class="row">
        <div class="col-xs-12 col-sm-8 col-sm-offset-2">
            
            <h2>Import Collins/Utas Part Numbers CSV Data</h2>
            
            @if ($errors->any())
                <div class="col-sm-12">
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
            
            @if (session('failures'))
            
                <div class="col-sm-12">
                    <div class="alert alert-danger">
                        <ul>
                
                            @foreach (session('failures') as $failure)
                            
                                <li>Failure at row {{ $failure->row() }}. The '{{ $failure->attribute() }}' attribute has the following errors:
                                
                                    <ul>
                                        @foreach ($failure->errors() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                
                                </li>
                            
                            @endforeach
                
                        </ul>
                    </div>
                </div>
            
            @endif
                
            <form class="form-horizontal" method="POST" enctype="multipart/form-data" action="{{ route('utas-data.import-utas-part-numbers') }}">
                {{ csrf_field() }}

                <div class="form-group{{ $errors->has('file') ? ' has-error' : '' }}">
                    <label for="shopfindings_file" class="col-md-4 control-label">Upload Collins/Utas Part Numbers CSV File</label>

                    <div class="col-md-6">
                        <input type="file" class="form-control" name="file" value="{{ old('shopfindings_file') }}" autofocus>
                        
                        <span id="helpBlock" class="help-block">File must be a MS-DOS comma-separated CSV file.</span>

                        @if ($errors->has('file'))
                            <span class="help-block">
                                <strong>{{ $errors->first('file') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <button type="submit" class="btn btn-primary">
                            Import <i class="far fa-save"></i>
                        </button>
                        
                        <a href="{{ route('utas-data.export-utas-part-numbers') }}" class="btn btn-info">Export <i class="fas fa-download"></i></a>
                    </div>
                </div>
            </form>
                
        </div>
    </div>
@endsection