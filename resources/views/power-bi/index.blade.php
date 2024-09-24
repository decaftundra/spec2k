@extends('layouts.default')

@section('content')
    <div class="row">
        <div class="col-xs-12">
	        <h1>Power BI CSV Files</h1>
                
            @if(count($files))
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <th>Filename</th>
                        <th>Download</th>
                        
                        @foreach($files as $key => $path)
                            <tr>
                                <td valign="middle"><span class="bold">{{ basename($path) }}</span></td>
                                
                                    <td>
                                        <a class="btn btn-sm btn-primary" href="{{ route('power-bi.download', $key) }}">
                                            Download <i class="fas fa-download"></i>
                                        </a>
                                    </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            @else
                <p>Sorry, no Power BI CSV files could be found.</p>
            @endif
        </div>
    </div>
@endsection