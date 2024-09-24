@extends('layouts.default')

@section('content')
    <div class="row">
        <div class="col-xs-12 col-sm-8 col-sm-offset-2">
            
            <h2>Import CSV Data</h2>
            
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-triangle"></i> <strong>Warning!</strong> Any notifications that are already in the system with the same IDs as records in the csv files will be overwritten. This can not be undone.
            </div>
                
            <form class="form-horizontal" method="POST" enctype="multipart/form-data" action="{{ route('csv-importer.store') }}">
                {{ csrf_field() }}

                <div class="form-group{{ $errors->has('shopfindings_file') ? ' has-error' : '' }}">
                    <label for="shopfindings_file" class="col-md-4 control-label">Upload Shop Findings CSV File</label>

                    <div class="col-md-6">
                        <input type="file" class="form-control" name="shopfindings_file" value="{{ old('shopfindings_file') }}" autofocus>
                        
                        <span id="helpBlock" class="help-block">File must be a MS-DOS comma-separated CSV file.</span>

                        @if ($errors->has('shopfindings_file'))
                            <span class="help-block">
                                <strong>{{ $errors->first('shopfindings_file') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="form-group{{ $errors->has('pieceparts_file') ? ' has-error' : '' }}">
                    <label for="pieceparts_file" class="col-md-4 control-label">Upload Piece Parts CSV File</label>

                    <div class="col-md-6">
                        <input type="file" class="form-control" name="pieceparts_file" value="{{ old('pieceparts_file') }}" autofocus>
                        
                        <span id="helpBlock" class="help-block">File must be a MS-DOS comma-separated CSV file.</span>

                        @if ($errors->has('pieceparts_file'))
                            <span class="help-block">
                                <strong>{{ $errors->first('pieceparts_file') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <button type="submit" class="btn btn-primary">
                            Import <i class="far fa-save"></i>
                        </button>
                    </div>
                </div>
            </form>
                
        </div>
    </div>
@endsection