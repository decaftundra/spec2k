@extends('layouts.default')

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <h1>Complete</h1>
            
            <div class="navbar-form">
                <form action="{{ request()->url() }}" method="GET">
                    <div class="form-group">
                        <input name="search" type="text" class="form-control" placeholder="Search" value="{{ old('search') }}">
					</div>
					<div class="form-group">
    					<select name="filter" class="filter form-control">
        					<option value="">Select filter...</option>
        					<option value="valid" {{ old('filter') == 'valid' ? 'selected' : '' }}>Valid</option>
        					<option value="invalid" {{ old('filter') == 'invalid' ? 'selected' : '' }}>Invalid</option>
    					</select>
					</div>
					@if (count($reportingOrganisations))
                        <select name="roc" class="form-control filter">
                            <option value="All">All Locations</option>
                            @foreach ($reportingOrganisations as $code => $name)
                                <option {{ old('roc', array_key_exists(auth()->user()->defaultLocation(), $reportingOrganisations) ? auth()->user()->defaultLocation() : 'All' ) == $code ? 'selected' : '' }} value="{{ $code }}">
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
                            class="datepicker form-control filter"
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
                            class="datepicker form-control filter"
                            placeholder="dd/mm/yyyy"
                            name="date_end"
                            value="{{ old('date_end') }}"
                        >
                    </div>
                    <button type="submit" class="btn btn-default">Submit <i class="fas fa-chevron-right"></i></button>
					<a href="{{ route('spec-2000-reports.index', ['reset' => true]) }}" type="button" class="btn btn-default"><i class="fas fa-sync-alt"></i> Reset</a>
                </form>  
            </div>
            
            @if (count($reports))
                <p class="displaying">
                    Displaying {{ $reports->firstItem() }} to {{ $reports->lastItem() }} of {{ number_format($reports->total()) }} reports.
                </p>
                
                <div class="table-responsive">
                    <table class="table table-hover" style="white-space:nowrap;">
                        <tr>
                            <th>Val.</th>
                            <th>Action</th>
                            <th>
                                <a title="Order by ID" href="{{ request()->fullUrlWithQuery(['orderby' => 'id', 'page' => 1]) }}">
                                    ID <i class="fas fa-sort"></i>
                                </a>
                            </th>
                            <th>
                                <a title="Order by Material" href="{{ request()->fullUrlWithQuery(['orderby' => 'material', 'page' => 1]) }}">
                                    Material <i class="fas fa-sort"></i>
                                </a>
                            </th>
                            <th>
                                <a title="Order by Serial Number" href="{{ request()->fullUrlWithQuery(['orderby' => 'serial', 'page' => 1]) }}">
                                    Serial No. <i class="fas fa-sort"></i>
                                </a>
                            </th>
                            <th>
                                <a title="Order by Reporting Organisation Code" href="{{ request()->fullUrlWithQuery(['orderby' => 'roc', 'page' => 1]) }}">
                                    Rep. Org. Code <i class="fas fa-sort"></i>
                                </a>
                            </th>
                            <th>
                                <a title="Order by Reporting Organisation Name" href="{{ request()->fullUrlWithQuery(['orderby' => 'ron', 'page' => 1]) }}">
                                    Rep. Org. Name <i class="fas fa-sort"></i>
                                </a>
                            </th>
                            <th>
                                <a title="Order by Receipt Date" href="{{ request()->fullUrlWithQuery(['orderby' => 'date', 'page' => 1]) }}">
                                    Receipt Date <i class="fas fa-sort"></i>
                                </a>
                            </th>
                        </tr>
                        
                        @foreach ($reports as $report)
                            <tr>
                                <td>{{ $report->isValid() ? tick() : cross() }}</td>
                                <td>
                                    <a href="{{ route('header.edit', $report['rcsSFI']) }}" class="btn btn-sm btn-danger">
                                        <i class="fas fa-pencil-alt"></i> Edit Report
                                    </a>
                                </td>
                                <td>{{ $report->get_RCS_SFI() ?: '-' }}</td>
                                <td>{{ $report->get_RCS_MPN() ?: '-' }}</td>
                                <td>{{ $report->get_RCS_SER() ?: '-' }}</td>
                                <td>{{ $report->get_HDR_ROC() ?: '-' }}</td>
                                <td>{{ $report->get_HDR_RON() ?: '-' }}</td>
                                <td>{{ $report->get_RCS_MRD() ?: '-' }}</td>
                            </tr>
                        @endforeach
                    </table>
                    
                    {{ $reports->appends([
                        'search' => request()->search,
                        'filter' => request()->filter,
                        'roc' => request()->roc,
                        'date_start' => request()->date_start,
                        'date_end' => request()->date_end,
                        'orderby' => request()->orderby,
                        'order' => request()->order == 'asc' ? 'desc' : 'asc'
                    ])->links() }}
                </div>
            @else
                <p>No reports found.</p>
            @endif
        </div>
    </div>
@endsection

@push ('footer-scripts')
    <script src="{{ asset('js/filter.js?v=1.1') }}"></script>
    <script src="{{ asset('js/datepicker.js?v=1.1') }}"></script>
@endpush