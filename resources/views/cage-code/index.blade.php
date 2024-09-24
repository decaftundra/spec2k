@extends('layouts.default')

@section('content')
    <div class="row">
        <div class="col-xs-12">
	        <h1>Manufacturer Cage Codes</h1>
	        
	        @can ('view-all-locations')
    	        <div class="alert alert-danger" role="alert">
        	        <i class="fas fa-exclamation-triangle"></i> Warning. Any changes made to this data will affect the application globally.
    	        </div>
	        @endcan
	        
            <div class="navbar-form">
                <form action="{{ request()->url() }}" method="GET">
                    <div class="form-group">
                        <input name="search" type="text" class="form-control input-sm" placeholder="Search" value="{{ old('search') }}">
					</div>
                    <button type="submit" class="btn btn-primary btn-sm">Submit <i class="fas fa-chevron-right"></i></button>
					<a href="{{ route('cage-code.index', ['reset' => true]) }}" type="button" class="btn btn-info btn-sm"><i class="fas fa-sync-alt"></i> Reset</a>
					
					&nbsp;
					
					@can('view-all-cage-codes')
    					<a href="{{ route('cage-code.create') }}" type="button" class="btn btn-sm btn-primary navbar-btn">
        					<i class="fas fa-plus"></i> Add Cage Code
        				</a>
    				@endcan
                </form>  
            </div>
                
            @if(count($cageCodes))
            
                <p class="displaying">Displaying {{ $cageCodes->firstItem() }} to {{ $cageCodes->lastItem() }} of {{ $cageCodes->total() }} cage codes.</p>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <th>
                            <a title="Order by code" href="{{ request()->fullUrlWithQuery(['orderby' => 'code', 'page' => 1]) }}">
                                Cage Code <i class="fas fa-sort"></i>
                            </a>
                        </th>
                        <th>
                            <a title="Order by info" href="{{ request()->fullUrlWithQuery(['orderby' => 'info', 'page' => 1]) }}">
                                Info <i class="fas fa-sort"></i>
                            </a>
                        </th>
                        <th>Edit</th>
                        @can('view-all-cage-codes')
                            <th>Delete</th>
                        @endcan
                        
                        @foreach($cageCodes as $cageCode)
                            <tr>
                                <td valign="middle"><span class="bold">{{ $cageCode->cage_code }}</span></td>
                                <td valign="middle"><span class="bold">{{ $cageCode->info }}</span></td>
                                @can('view-all-cage-codes')
                                    <td>
                                        <a class="btn btn-sm btn-warning" href="{{ route('cage-code.edit', $cageCode->id) }}">
                                            Edit <i class="fas fa-pencil-alt"></i>
                                        </a>
                                    </td>
                                @endcan
                                @can('view-all-cage-codes')
                                    <td>
                                        <a class="btn btn-sm btn-danger {{ $cageCode->locations->count() ? 'disabled' : '' }}" href="{{ route('cage-code.delete', $cageCode->id) }}">
                                            Delete <i class="far fa-trash-alt"></i>
                                        </a>
                                    </td>
                                @endcan
                            </tr>
                        @endforeach
                    </table>
                </div>
            
                {{ $cageCodes->appends([
                    'search' => request()->search,
                    'orderby' => request()->orderby,
                    'order' => request()->order == 'asc' ? 'desc' : 'asc'
                ])->links() }}
            @else
                <p>No manufacturer cage codes to display.</p>
            @endif
        </div>
    </div>
@endsection