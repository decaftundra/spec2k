@extends('layouts.default')

@section('content')
    <div class="row">
        <div class="col-xs-12">
	        <h1>Maintenance Notices</h1>
	        
            <div class="navbar-form">
                <form action="{{ request()->url() }}" method="GET">
                    <div class="form-group">
                        <input name="search" type="text" class="form-control input-sm" placeholder="Search" value="{{ old('search') }}">
					</div>
                    <button type="submit" class="btn btn-primary btn-sm">Submit <i class="fas fa-chevron-right"></i></button>
					<a href="{{ route('maintenance-notice.index', ['reset' => true]) }}" type="button" class="btn btn-info btn-sm"><i class="fas fa-sync-alt"></i> Reset</a>
					
					&nbsp;
					
					@can('create', App\Location::class)
    					<a href="{{ route('maintenance-notice.create') }}" type="button" class="btn btn-sm btn-primary navbar-btn">
        					<i class="fas fa-plus"></i> Add Maintenance Notice
        				</a>
    				@endcan
                </form>  
            </div>
                
            @if(count($maintenanceNotices))
            
                <p class="displaying">Displaying {{ $maintenanceNotices->firstItem() }} to {{ $maintenanceNotices->lastItem() }} of {{ $maintenanceNotices->total() }} Maintenance Notices.</p>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <th>
                            <a title="Order by title" href="{{ request()->fullUrlWithQuery(['orderby' => 'title', 'page' => 1]) }}">
                                Title <i class="fas fa-sort"></i>
                            </a>
                        </th>
                        <th>
                            Display
                        </th>
                        @can('delete', $maintenanceNotices->first())
                            <th>Edit</th>
                        @endcan
                        @can('delete', $maintenanceNotices->first())
                            <th>Delete</th>
                        @endcan
                        
                        @foreach($maintenanceNotices as $maintenanceNotice)
                            <tr>
                                <td valign="middle"><span class="bold">{{ $maintenanceNotice->title }}</span></td>
                                <td valign="middle"><span class="bold">{{ $maintenanceNotice->display ? 'Yes' : 'No' }}</span></td>
                                @can('edit', $maintenanceNotice)
                                    <td>
                                        <a class="btn btn-sm btn-warning" href="{{ route('maintenance-notice.edit', $maintenanceNotice->id) }}">
                                            Edit <i class="fas fa-pencil-alt"></i>
                                        </a>
                                    </td>
                                @endcan
                                @can('delete', $maintenanceNotice)
                                    <td>
                                        <a class="btn btn-sm btn-danger" href="{{ route('maintenance-notice.delete', $maintenanceNotice->id) }}">
                                            Delete <i class="far fa-trash-alt"></i>
                                        </a>
                                    </td>
                                @endcan
                            </tr>
                        @endforeach
                    </table>
                </div>
            
                {{ $maintenanceNotices->appends([
                    'search' => request()->search,
                    'orderby' => request()->orderby,
                    'order' => request()->order == 'asc' ? 'desc' : 'asc'
                ])->links() }}
            @else
                <p>No Maintenance Notices to display.</p>
            @endif
        </div>
    </div>
@endsection