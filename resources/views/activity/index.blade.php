@extends('layouts.default')

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <h1>All Activity</small></h1>
            
            <div class="navbar-form">
                <form action="{{ request()->url() }}" method="GET" class="form-inline">
                    <div class="form-group">
                        <input name="search" type="text" class="form-control input-sm" placeholder="Search" value="{{ old('search') }}">
                        @if (count($types))
                            <select name="type" class="form-control input-sm filter">
                                <option value="">All Types</option>
                                
                                @foreach ($types as $type)
                                    <option {{ old('type') == $type ? 'selected' : '' }} value="{{ $type }}">
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                        @if (count($reportingOrganisations))
                            <select name="pc" class="form-control input-sm filter">
                                
                                @if (count($reportingOrganisations) > 1)
                                    <option value="">All Locations</option>
                                @endif
                                
                                @foreach ($reportingOrganisations as $plantCode => $name)
                                    <option {{ old('pc') == $plantCode ? 'selected' : '' }} value="{{ $plantCode }}">
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                        <label for="date_start">From</label>
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fas fa-calendar-alt"></i></div>
                            <input
                                autocomplete="off"
                                id="date_start"
                                type="text"
                                class="datepicker form-control input-sm filter"
                                placeholder="dd/mm/yyyy"
                                name="date_start"
                                value="{{ old('date_start') }}"
                            >
                        </div>
                        <label for="date_end">To</label>
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fas fa-calendar-alt"></i></div>
                            <input
                                autocomplete="off"
                                id="date_end"
                                type="text"
                                class="datepicker form-control input-sm filter"
                                placeholder="dd/mm/yyyy"
                                name="date_end"
                                value="{{ old('date_end') }}"
                            >
                        </div>
					</div>
                    <button type="submit" class="btn btn-primary btn-sm">Submit <i class="fas fa-chevron-right"></i></button>
					<a href="{{ route('activity.index', ['reset' => true]) }}" type="button" class="btn btn-info btn-sm"><i class="fas fa-sync-alt"></i> Reset</a>
                </form>
            </div>
            
            @if (count($activities))
                <p class="displaying">
                    Displaying {{ $activities->firstItem() }} to {{ $activities->lastItem() }} of {{ number_format($activities->total()) }} activities.
                </p>
                
                <div class="table-responsive">
                    <table class="table table-hover" style="white-space:nowrap;">
                        <tr>
                            <th>
                                <a title="Order by user" href="{{ request()->fullUrlWithQuery(['orderby' => 'user', 'page' => 1]) }}">
                                    User <i class="fas fa-sort"></i>
                                </a>
                            </th>
                            <th>
                                <a title="Order by action" href="{{ request()->fullUrlWithQuery(['orderby' => 'action', 'page' => 1]) }}">
                                    Action <i class="fas fa-sort"></i>
                                </a>
                            </th>
                            <th>Info</th>
                            <th>
                                <a title="Order by notification_id" href="{{ request()->fullUrlWithQuery(['orderby' => 'notification_id', 'page' => 1]) }}">
                                    Notification ID <i class="fas fa-sort"></i>
                                </a>
                            </th>
                            <th>
                                <a title="Order by date" href="{{ request()->fullUrlWithQuery(['orderby' => 'date', 'page' => 1]) }}">
                                    Date <i class="fas fa-sort"></i>
                                </a>
                            </th>
                        </tr>
                        
                        
                        @foreach ($activities as $activity)
                            <tr>
                                <td>{{ $activity->fullname }}</td>
                                <td>{{ ucfirst($activity->name) }} {{ str_replace('_', ' ', $activity->type) }}</td>
                                
                                @if ($activity->action == 'deleted')
                                    <td>
                                        <i class="fas fa-trash-alt"></i> Unavailable
                                    </td>
                                    
                                    <td>
                                        @if ($activity->notification_id)
                                            {{ $activity->notification_id }}
                                        @elseif ($activity->shop_finding_id)
                                            {{ $activity->shop_finding_id }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                @else
                                    @if ($activity->getSubject())
                                        <td>
                                            <i class="fas fa-external-link-alt"></i>
                                            <a rel="noopener" rel="noopener" target="_blank" href="{{ $activity->getSubject()->getActivityUrl() }}">
                                                {{ $activity->getSubject()->getActivityUrlTitle() }}
                                            </a>
                                        </td>
                                    @else
                                        <td><i class="fas fa-trash-alt"></i> Unavailable</td>
                                    @endif
                                    
                                    <td>
                                        @if ($activity->notification_id)
                                         {{ $activity->notification_id }}
                                        @elseif ($activity->shop_finding_id)
                                            {{ $activity->shop_finding_id }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                @endif
                                
                                <td>
                                    {{ $activity->created_at->setTimezone($timezone)->format('d/m/Y H:i:s') }}
                                </td>
                            </tr>
                        @endforeach
                    </table>
                    
                    {{ $activities->appends([
                        'search' => request()->search,
                        'type' => request()->type,
                        'pc' => request()->pc,
                        'date_start' => request()->date_start,
                        'date_end' => request()->date_end,
                        'orderby' => request()->orderby,
                        'order' => request()->order == 'asc' ? 'desc' : 'asc'
                    ])->links() }}
                </div>
            @else
                <p>No activities found.</p>
            @endif
        </div>
    </div>
@endsection

@push ('footer-scripts')
    <script src="{{ asset('js/datepicker.js?v=1.1') }}"></script>
@endpush