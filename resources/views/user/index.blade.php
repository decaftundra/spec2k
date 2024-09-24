@extends('layouts.default')

@section('content')
    <div class="row">
        <div class="col-xs-12">
	        <h1>Users</h1>
            <div class="navbar-form">
                <form action="{{ request()->url() }}" method="GET">
                    <div class="form-group">
                        <input name="search" type="text" class="form-control input-sm" placeholder="Search" value="{{ old('search') }}">
                        
                        @if (count($reportingOrganisations))
                            <select name="pc" class="form-control input-sm filter">
                                @if (count($reportingOrganisations) > 1)
                                    <option value="All">All Locations</option>
                                @endif
                                @foreach ($reportingOrganisations as $plantCode => $name)
                                    <option {{ old('pc', 'All') == $plantCode ? 'selected' : '' }} value="{{ $plantCode }}">
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                        
                        <select id="filter" class="form-control input-sm" name="filter">
    						<option value="">All</option>
    						@foreach($roles as $key => $value)
        						<option {{ old('filter', 'all') == $key ? 'selected' : '' }} value="{{ $key }}">
            						{{ ucwords(str_replace('_', ' ', Str::plural($value))) }}
                				</option>
    						@endforeach
    					</select>
					</div>
                    <button type="submit" class="btn btn-primary btn-sm">Submit <i class="fas fa-chevron-right"></i></button>
					<a href="{{ route('user.index', ['reset' => true]) }}" type="button" class="btn btn-info btn-sm"><i class="fas fa-sync-alt"></i> Reset</a>
					
					&nbsp;
					
					@can('create', App\User::class)
    					<a href="{{ route('user.create') }}" type="button" class="btn btn-primary btn-sm navbar-btn">
        					<i class="fas fa-plus"></i> Add User
        				</a>
                    @endcan
                </form>  
            </div>
                
            @if(count($users))
            
                <p class="displaying">Displaying {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} users.</p>
                
                <div class="table-responsive">
                    <table style="font-size:13px;" class="table table-hover">
                        <th style="padding-right:0;">
                            <a title="Order by name" href="{{ request()->fullUrlWithQuery(['orderby' => 'name', 'page' => 1]) }}">
                                Name <i class="fas fa-sort"></i>
                            </a>
                        </th>
                        <th style="padding-right:0;">
                            <a title="Order by email" href="{{ request()->fullUrlWithQuery(['orderby' => 'email', 'page' => 1]) }}">
                                Email <i class="fas fa-sort"></i>
                            </a>
                        </th>
                        <th style="padding-right:0;">
                            <a title="Order by role" href="{{ request()->fullUrlWithQuery(['orderby' => 'role', 'page' => 1]) }}">
                                Role <i class="fas fa-sort"></i>
                            </a>
                        </th>
                        <th style="padding-right:0;">
                            <a title="Order by location" href="{{ request()->fullUrlWithQuery(['orderby' => 'location', 'page' => 1]) }}">
                                Location <i class="fas fa-sort"></i>
                            </a>
                        </th>
                        <th style="padding-right:0;">
                            <a title="Order by group" href="{{ request()->fullUrlWithQuery(['orderby' => 'group', 'page' => 1]) }}">
                                Group <i class="fas fa-sort"></i>
                            </a>
                        </th>
                        <th style="padding-right:0;">
                            <a title="Order by last login" href="{{ request()->fullUrlWithQuery(['orderby' => 'last', 'page' => 1]) }}">
                                Last Active <i class="fas fa-sort"></i>
                            </a>
                        </th>
                        @foreach($users as $user)
                            <tr>
                                <td style="padding-right:0;" valign="middle"><span class="bold">{{ $user->fullname }}</span></td>
                                <td style="padding-right:0;" valign="middle">{{ $user->email }}</td>
                                <td style="padding-right:0;" valign="middle">{{ ucwords(str_replace('_', ' ', $user->role)) }}</td>
                                <td style="padding-right:0;" valign="middle">{{ $user->location ?? '-' }}</td>
                                <td style="padding-right:0;" valign="middle">{{ $user->planner_group }}</td>
                                <td style="padding-right:0;" valign="middle">{{ $user->last_active_at ? date('d/m/y', strtotime($user->last_active_at)) : '-' }}</td>
                                <td style="padding-right:0;" align="right">
                                    @can('show', App\User::find($user->id))
                                        <a class="btn btn-xs btn-primary {{ $user->activity_count ? '' : 'disabled' }}" href="{{ route('activity.show', $user->id) }}" role="button">
                                            Activity <span class="badge">{{ $user->activity_count }}</span> <i class="fas fa-briefcase"></i>
                                        </a>
                                    @endcan
                                    @can('update', App\User::find($user->id))
                                        <a class="btn btn-xs btn-warning" href="{{ route('user.edit', $user->id) }}" role="button">
                                            Edit <i class="fas fa-pencil-alt"></i>
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            
                {{ $users->appends([
                    'search' => request()->search,
                    'filter' => request()->filter,
                    'orderby' => request()->orderby,
                    'pc' => request()->pc,
                    'order' => request()->order == 'asc' ? 'desc' : 'asc'
                ])->links() }}
            @else
                <p>No users to display.</p>
            @endif
        </div>
    </div>
@endsection