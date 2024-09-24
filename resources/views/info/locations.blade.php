@extends('layouts.default')

@section('content')
    <div class="row">
        <div class="col-xs-12">
	        <h1>Repair Stations</h1>
            <div class="navbar-form">
                <form action="{{ request()->url() }}" method="GET">
                    <div class="form-group">
                        <input name="search" type="text" class="form-control input-sm" placeholder="Search" value="{{ old('search') }}">
					</div>
                    <button type="submit" class="btn btn-primary btn-sm">Submit <i class="fas fa-chevron-right"></i></button>
					<a href="{{ route('info.locations', ['reset' => true]) }}" type="button" class="btn btn-info btn-sm"><i class="fas fa-sync-alt"></i> Reset</a>
                </form>  
            </div>
                
            @if(count($locations))
            
                <p class="displaying">Displaying {{ $locations->firstItem() }} to {{ $locations->lastItem() }} of {{ $locations->total() }} locations.</p>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <th>
                            <a title="Order by name" href="{{ request()->fullUrlWithQuery(['orderby' => 'name', 'page' => 1]) }}">
                                Name <i class="fas fa-sort"></i>
                            </a>
                        </th>
                        <th>
                            <a title="Order by ERP name" href="{{ request()->fullUrlWithQuery(['orderby' => 'sap_name', 'page' => 1]) }}">
                                ERP Name <i class="fas fa-sort"></i>
                            </a>
                        </th>
                        <th>
                            <a title="Order by plant code" href="{{ request()->fullUrlWithQuery(['orderby' => 'code', 'page' => 1]) }}">
                                Plant Code <i class="fas fa-sort"></i>
                            </a>
                        </th>
                        <th>
                            <a title="Order by timezone" href="{{ request()->fullUrlWithQuery(['orderby' => 'timezone', 'page' => 1]) }}">
                                Timezone <i class="fas fa-sort"></i>
                            </a>
                        </th>
                        
                        @foreach($locations as $location)
                            <tr>
                                <td valign="middle"><span class="bold">{{ $location->name }}</span></td>
                                <td valign="middle"><span class="bold">{{ $location->sap_location_name }}</span></td>
                                <td valign="middle">{{ $location->plant_code }}</td>
                                <td valign="middle">{{ $location->timezone }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            
                {{ $locations->appends([
                    'search' => request()->search,
                    'orderby' => request()->orderby,
                    'order' => request()->order == 'asc' ? 'desc' : 'asc'
                ])->links() }}
            @else
                <p>No repair stations to display.</p>
            @endif
        </div>
    </div>
@endsection