@extends('layouts.export')

@section('loader')
    <div class="text-center modal" id="ajax-loader">
        <i class="fas fa-circle-notch fa-spin fa-5x"></i>
        <span class="sr-only">Loading...</span>
    </div>
@endsection

@section('export-form')
    <div class="row">
        <div class="well">
            <form action="{{ request()->url() }}" method="POST">
                
                {{ csrf_field() }}
                
                @if ($errors->any())
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif
                
                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            @if (count($locations))
                                <label for="location">Location <span class="text-danger">*</span></label>
                                <select name="location" class="form-control filter input-sm">
                                    @if (count($locations) > 1)
                                        <option value="all">All Locations</option>
                                    @endif
                                    
                                    @foreach ($locations as $code => $name)
                                        <option {{ old('location', $defaultLocation ?? 'All' ) == $code ? 'selected' : '' }} value="{{ $code }}">
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                        
                        <div class="form-group">
                            <label for="validity">Validity <span class="text-danger">*</span></label>
                            <select name="validity" class="filter form-control input-sm">
                                <option value="all">Valid & Invalid</option>
                                <option value="valid" {{ old('validity', 'valid') == 'valid' ? 'selected' : '' }}>Valid</option>
                                <option value="invalid" {{ old('validity', 'valid') == 'invalid' ? 'selected' : '' }}>Invalid</option>
                            </select>
                        </div>
                    </div>
                
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            @if (count($statuses))
                                <label for="status">Status <span class="text-danger">*</span></label>
                                @foreach ($statuses as $key => $name)
                                    <div class="checkbox">
                                        <label>
                                            <input
                                                type="checkbox"
                                                name="status[]"
                                                value="{{ $key }}"
                                                {{ in_array($key, old('status', ['complete_scrapped', 'complete_shipped'])) ? 'checked' : '' }}
                                            > {{ $name }}
                                        </label>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label for="notification_ids">Notification Ids</label>
                            <textarea style="max-width:100%;min-width:100%;" name="notification_ids" rows="5" class="form-control input-sm" placeholder="Full or partial Notification Ids (no wildcard required)">{{ old('notification_ids') ?? NULL }}</textarea>
                            
                            <p class="help-block">Multiples should be delimited with spaces, commas or new lines.</p>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label for="part_nos">Part Numbers</label>
                            <textarea style="max-width:100%;min-width:100%;" name="part_nos" rows="5" class="form-control input-sm" placeholder="Full or partial Part Numbers (no wildcard required)">{{ old('part_nos') ?? NULL }}</textarea>
                            <p class="help-block">Multiples should be delimited with spaces, commas or new lines.</p>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label for="date_start">Date Start <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <input
                                    autocomplete="off"
                                    id="date_start"
                                    type="text"
                                    class="datepicker form-control filter input-sm"
                                    placeholder="dd/mm/yyyy"
                                    name="date_start"
                                    value="{{ old('date_start') }}"
                                >
                            </div>
                            <p class="help-block">Start date is required for xml export.</p>
                        </div>
                    </div>
                        
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label for="date_end">Date End <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <input
                                    autocomplete="off"
                                    id="date_end"
                                    type="text"
                                    class="datepicker form-control filter input-sm"
                                    placeholder="dd/mm/yyyy"
                                    name="date_end"
                                    value="{{ old('date_end') }}"
                                >
                            </div>
                            <p class="help-block">End date is required for xml export.</p>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-sm">
                                Submit <i class="fas fa-chevron-right"></i>
                            </button>
                            <a href="{{ route('reports.export', ['reset' => true]) }}" type="button" class="btn btn-info btn-sm">
                                <i class="fas fa-sync-alt"></i> Reset
                            </a>
                        </div>
                    </div>
                    
                    @if (old('date_start') && old('date_end'))
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <div class="right-hand-buttons">
                                <button type="submit" name="download-sf" value="download-sf" class="btn btn-info btn-sm {{ !count($allRecords) ? 'disabled' : '' }}">
                                    Shop Findings XML <i class="fas fa-arrow-alt-circle-down"></i>
                                </button>
                                
                                <button type="submit" name ="download-pp" value="download-pp" class="btn btn-info btn-sm {{ !count($allRecords) ? 'disabled' : '' }}">
                                    Piece Parts XML <i class="fas fa-arrow-alt-circle-down"></i>
                                </button>
                                
                                <button type="submit" name="download-zip" value="download-zip" class="btn btn-primary btn-sm {{ !count($allRecords) ? 'disabled' : '' }}">
                                    Zip <i class="fas fa-arrow-alt-circle-down"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </form>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h1>Reports to export</h1>
            
            @if (count($allRecords))
                
                <?php $paginated = $allRecords->paginate(1000); ?>
                
                <p class="displaying">
                    Displaying {{ $paginated->firstItem() }} to {{ $paginated->lastItem() }} of {{ number_format($paginated->total()) }} datasets.
                </p>
                
                @include('partials.key')
                
                <div class="table-responsive">
                    <form action="{{ request()->url() }}" method="POST">
                    
                        {{ csrf_field() }}
                        
                        <input type="hidden" name="location" value="{{ old('location', $defaultLocation ?? 'All') }}">
                        <input type="hidden" name="validity" value="{{ old('validity', 'valid') ?? NULL }}">
                        
                        @if (old('status', ['complete_scrapped', 'complete_shipped']) && count(old('status', ['complete_scrapped', 'complete_shipped'])))
                            @foreach (old('status', ['complete_scrapped', 'complete_shipped']) as $k => $v)
                                <input type="hidden" name="status[{{ $k }}]" value="{{ $v }}">
                            @endforeach
                        @endif
                        
                        <input type="hidden" name="part_nos" value="{{ old('part_nos') ?? NULL }}">
                        <input type="hidden" name="notification_ids" value="{{ old('notification_ids') ?? NULL }}">
                        <input type="hidden" name="date_start" value="{{ old('date_start') }}">
                        <input type="hidden" name="date_end" value="{{ old('date_end') }}">
                        <input type="hidden" name="order" value="{{ old('order') }}">
                        <input type="hidden" name="orderby" value="{{ old('orderby') }}">
                        
                        <table class="table table-hover" style="white-space:nowrap;">
                            <tr>
                                <th>&nbsp;</th>
                                <th>
                                    <button style="padding:0;" type="submit" title="Order by Validity" name="orderby" value="is_valid" class="btn btn-link">
                                        Val <i class="fas fa-sort"></i>
                                    </button>
                                </th>
                                @if (in_array(app()->environment(), ['local', 'dev']))
                                    <th>
                                        <button style="padding:0;" type="submit" title="Order by Collins parts" name="orderby" value="is_utas" class="btn btn-link">
                                            Col <i class="fas fa-sort"></i>
                                        </button>
                                    </th>
                                @endif
                                <!--<th>
                                    <button style="padding:0;" type="submit" title="Order by number of piece parts" name="orderby" value="piece_part_count" class="btn btn-link">
                                        PP <i class="fas fa-sort"></i>
                                    </button>
                                </th>-->
                                <th>
                                    <button style="padding:0;" type="submit" title="Order by user acronym" name="orderby" value="acronym" class="btn btn-link">
                                        User <i class="fas fa-sort"></i>
                                    </button>
                                </th>
                                <th>
                                    <button style="padding:0;" type="submit" title="Order by ID" name="orderby" value="id" class="btn btn-link">
                                        ID <i class="fas fa-sort"></i>
                                    </button>
                                </th>
                                <th>
                                    <button style="padding:0;" type="submit" title="Order by ID" name="orderby" value="RCS_SER" class="btn btn-link">
                                        Rec. SN <i class="fas fa-sort"></i>
                                    </button>
                                </th>
                                <th>
                                    <button style="padding:0;" type="submit" title="Order by ID" name="orderby" value="SUS_SER" class="btn btn-link">
                                        Ship. SN <i class="fas fa-sort"></i>
                                    </button>
                                </th>
                                <th>
                                    <button style="padding:0;" type="submit" title="Order by ID" name="orderby" value="RCS_MPN" class="btn btn-link">
                                        Material <i class="fas fa-sort"></i>
                                    </button>
                                </th>
                                <th>
                                    <button style="padding:0;" type="submit" title="Order by ID" name="orderby" value="status" class="btn btn-link">
                                        Status <i class="fas fa-sort"></i>
                                    </button>
                                </th>
                                <th>
                                    <button style="padding:0;" type="submit" title="Order by ID" name="orderby" value="HDR_ROC" class="btn btn-link">
                                        Rep. Code <i class="fas fa-sort"></i>
                                    </button>
                                </th>
                                <th>
                                    <button style="padding:0;" type="submit" title="Order by ID" name="orderby" value="ship_scrap_date" class="btn btn-link">
                                        Ship/Scrap Date <i class="fas fa-sort"></i>
                                    </button>
                                </th>
                            </tr>
                            
                            @foreach ($paginated as $report)
                                <?php $valid = $report->is_valid; ?>
                                
                                <?php 
                                if ($report->status == 'complete_shipped' || $report->status == 'complete_scrapped') {
                                    $rowClass = 'success';
                                } else if ($report->status == 'subcontracted') {
                                    $rowClass = 'warning';
                                } else {
                                    $rowClass = '';
                                }
                                ?>
                                
                                <tr class="{{ $rowClass }}">
                                    <td>
                                        <a class="btn btn-sm btn-primary" title="Edit" href="{{ route('header.edit', $report->id) }}">
                                            <i class="fas fa-pencil-alt"></i> Edit
                                        </a>
                                    </td>
                                    <td>{{ $valid ? tick() : cross() }}</td>
                                    @if (in_array(app()->environment(), ['local', 'dev']))
                                        <td>{{ $report->is_utas ? tick() : '-' }}</td>
                                    @endif
                                    <!--<td class="piece-part-count">{{-- $report->piece_part_count ?: '-' --}}</td>-->
                                    <td>{{ $report->acronym ?: '-' }}</td>
                                    <td>{{ $report->id }}</td>
                                    <td>
                                        @if ($report->RCSSER)
                                            {{ $report->RCSSER }}
                                        @else
                                            <span class="text-muted">{{ !empty($report->rcsSER) ? $report->rcsSER : '-' }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($report->SUSSER)
                                            {{ $report->SUSSER }}
                                        @else
                                            <span class="text-muted">{{ !empty($report->susSER) ? $report->susSER : '-' }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($report->MPN)
                                            {{ $report->MPN }}
                                        @else
                                            <span class="text-muted">{{ !empty($report->rcsMPN) ? $report->rcsMPN : '-' }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $statuses[$report->status] ?? '-' }}</td>
                                    <td>
                                        @if ($report->ROC)
                                            {{ $report->ROC }}
                                        @else
                                            <span class="text-muted">{{ !empty($report->hdrROC) ? $report->hdrROC : '-' }}</span>
                                        @endif
                                    <td>
                                        @if ($report->shipped_at)
                                            <i class="fas fa-shipping-fast"></i> {{ date('d/m/y', strtotime($report->shipped_at)) }}
                                        @elseif ($report->scrapped_at)
                                            <i class="far fa-trash-alt"></i> {{ date('d/m/y', strtotime($report->scrapped_at)) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </form>
                    
                    {{ $paginated->appends([
                        'status' => request()->status,
                        'location' => request()->location,
                        'validity' => request()->validity,
                        'date_start' => request()->date_start,
                        'date_end' => request()->date_end,
                        'notification_ids' => request()->notification_ids,
                        'part_nos' => request()->part_nos,
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
    <script>
        function date_startSpinner() {
            $('#ajax-loader').show();
        }
        
        function stopSpinner() {
            $('#ajax-loader').hide();
        }
        
        window.onload = function(){
            stopSpinner();
        }
    </script>
    <script src="{{ asset('js/datepicker.js?v=1.2') }}"></script>
@endpush