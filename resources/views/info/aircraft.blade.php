@extends('layouts.default')

@section('content')
    <div class="row">
        <div class="col-xs-12">
	        <h1>Aircraft List</h1>
            <div class="navbar-form">
                <form action="{{ request()->url() }}" method="GET">
                    <div class="form-group">
                        <input name="search" type="text" class="form-control input-sm" placeholder="Search" value="{{ old('search') }}">
					</div>
					@if (count($manufacturerCodes))
                        <select name="manufacturer_code" class="form-control filter input-sm">
                            <option value="">All Manufacturer Codes</option>
                            
                            @foreach ($manufacturerCodes as $code => $name)
                                <option {{ old('manufacturer_code') == $code ? 'selected' : '' }} value="{{ $code }}">
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                    <button type="submit" class="btn btn-primary btn-sm">Submit <i class="fas fa-chevron-right"></i></button>
					<a href="{{ route('info.aircraft', ['reset' => true]) }}" type="button" class="btn btn-info btn-sm"><i class="fas fa-sync-alt"></i> Reset</a>
                </form>  
            </div>
                
            @if(count($aircraftDetails))
            
                <p class="displaying">Displaying {{ $aircraftDetails->firstItem() }} to {{ $aircraftDetails->lastItem() }} of {{ $aircraftDetails->total() }} aircraft.</p>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <th>
                            <a title="Order by aircraft fully qualified registration number" href="{{ request()->fullUrlWithQuery(['orderby' => 'reg', 'page' => 1]) }}">
                                Registration No. <i class="fas fa-sort"></i>
                            </a>
                        </th>
                        <th>
                            <a title="Order by aircraft identification number" href="{{ request()->fullUrlWithQuery(['orderby' => 'id', 'page' => 1]) }}">
                                Aircraft ID. <i class="fas fa-sort"></i>
                            </a>
                        </th>
                        <th>
                            <a title="Order by manufacturer name" href="{{ request()->fullUrlWithQuery(['orderby' => 'name', 'page' => 1]) }}">
                                Mfr. Name <i class="fas fa-sort"></i>
                            </a>
                        </th>
                        <th>
                            <a title="Order by manufacturer code" href="{{ request()->fullUrlWithQuery(['orderby' => 'code', 'page' => 1]) }}">
                                Mfr. Code <i class="fas fa-sort"></i>
                            </a>
                        </th>
                        <th>
                            <a title="Order by aircraft model identifier" href="{{ request()->fullUrlWithQuery(['orderby' => 'model', 'page' => 1]) }}">
                                Model ID. <i class="fas fa-sort"></i>
                            </a>
                        </th>
                        <th>
                            <a title="Order by aircraft series identifier" href="{{ request()->fullUrlWithQuery(['orderby' => 'series', 'page' => 1]) }}">
                                Series ID. <i class="fas fa-sort"></i>
                            </a>
                        </th>
                        
                        @foreach($aircraftDetails as $aircraftDetail)
                            <tr>
                                <td valign="middle"><span class="bold">{{ $aircraftDetail->aircraft_fully_qualified_registration_no }}</span></td>
                                <td valign="middle">{{ $aircraftDetail->aircraft_identification_no }}</td>
                                <td valign="middle">{{ $aircraftDetail->manufacturer_name }}</td>
                                <td valign="middle">{{ $aircraftDetail->manufacturer_code }}</td>
                                <td valign="middle">{{ $aircraftDetail->aircraft_model_identifier }}</td>
                                <td valign="middle">{{ $aircraftDetail->aircraft_series_identifier }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            
                {{ $aircraftDetails->appends([
                    'search' => request()->search,
                    'manufacturer_code' => request()->manufacturer_code,
                    'orderby' => request()->orderby,
                    'order' => request()->order == 'asc' ? 'desc' : 'asc'
                ])->links() }}
            @else
                <p>No aircraft to display.</p>
            @endif
        </div>
    </div>
@endsection