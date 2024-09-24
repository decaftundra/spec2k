@extends('layouts.default')

@section('content')
    <div class="row">
        <div class="col-xs-12">
	        <h1>Engines List</h1>
            <div class="navbar-form">
                <form action="{{ request()->url() }}" method="GET">
                    <div class="form-group">
                        <input name="search" type="text" class="form-control input-sm" placeholder="Search" value="{{ old('search') }}">
					</div>
					@if (count($manufacturerCodes))
                        <select name="engine_manufacturer_code" class="form-control filter input-sm">
                            <option value="">All Manufacturer Codes</option>
                            
                            @foreach ($manufacturerCodes as $code => $name)
                                <option {{ old('engine_manufacturer_code') == $code ? 'selected' : '' }} value="{{ $code }}">
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                    <button type="submit" class="btn btn-primary btn-sm">Submit <i class="fas fa-chevron-right"></i></button>
					<a href="{{ route('info.engine-details', ['reset' => true]) }}" type="button" class="btn btn-info btn-sm"><i class="fas fa-sync-alt"></i> Reset</a>
                </form>  
            </div>
                
            @if(count($engineDetails))
            
                <p class="displaying">Displaying {{ $engineDetails->firstItem() }} to {{ $engineDetails->lastItem() }} of {{ $engineDetails->total() }} engines.</p>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <th>
                            <a title="Order by Engine Manufacturer" href="{{ request()->fullUrlWithQuery(['orderby' => 'manufacturer', 'page' => 1]) }}">
                                Engine Manufacturer <i class="fas fa-sort"></i>
                            </a>
                        </th>
                        <th>
                            <a title="Order by Engine Manufacturer Code" href="{{ request()->fullUrlWithQuery(['orderby' => 'code', 'page' => 1]) }}">
                                Engine Manufacturer Code <i class="fas fa-sort"></i>
                            </a>
                        </th>
                        <th>
                            <a title="Order by Engine Type" href="{{ request()->fullUrlWithQuery(['orderby' => 'type', 'page' => 1]) }}">
                                Engine Type <i class="fas fa-sort"></i>
                            </a>
                        </th>
                        <th>
                            <a title="Order by Engines Series" href="{{ request()->fullUrlWithQuery(['orderby' => 'series', 'page' => 1]) }}">
                                Engines Series <i class="fas fa-sort"></i>
                            </a>
                        </th>
                        
                        @foreach($engineDetails as $engineDetail)
                            <tr>
                                <td valign="middle"><span class="bold">{{ $engineDetail->engine_manufacturer }}</span></td>
                                <td valign="middle">{{ $engineDetail->engine_manufacturer_code }}</td>
                                <td valign="middle">{{ $engineDetail->engine_type }}</td>
                                <td valign="middle">{{ $engineDetail->engines_series }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            
                {{ $engineDetails->appends([
                    'search' => request()->search,
                    'engine_manufacturer_code' => request()->engine_manufacturer_code,
                    'orderby' => request()->orderby,
                    'order' => request()->order == 'asc' ? 'desc' : 'asc'
                ])->links() }}
            @else
                <p>No engine details to display.</p>
            @endif
        </div>
    </div>
@endsection