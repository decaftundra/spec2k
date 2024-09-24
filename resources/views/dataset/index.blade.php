@extends('layouts.default')

@section('loader')
    <div class="text-center modal" id="ajax-loader">
        <i class="fas fa-circle-notch fa-spin fa-5x"></i>
        <span class="sr-only">Loading...</span>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <h1>In Progress</h1>
            
            <div class="navbar-form">
                <form action="{{ request()->url() }}" method="GET">
                    <div class="form-group">
                        <input name="search" type="text" class="form-control input-sm" placeholder="Search" value="{{ old('search') }}">
					</div>
					@if (count($statuses))
                        <select name="status" class="form-control filter input-sm">
                            <option value="">All Statuses</option>
                            
                            @foreach ($statuses as $key => $name)
                                <option {{ old('status', 'All') == $key ? 'selected' : '' }} value="{{ $key }}">
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    @endif
					<div class="form-group">
    					<select name="filter" class="filter form-control input-sm">
        					<option value="">Select filter...</option>
        					<option value="valid" {{ old('filter') == 'valid' ? 'selected' : '' }}>Valid</option>
        					<option value="invalid" {{ old('filter') == 'invalid' ? 'selected' : '' }}>Invalid</option>
    					</select>
					</div>
					@if (count($reportingOrganisations))
                        <select name="pc" class="form-control filter input-sm" style="max-width:150px;">
                            
                            @if (count($reportingOrganisations) > 1)
                                <option value="All">All Locations</option>
                            @endif
                            
                            @foreach ($reportingOrganisations as $plantCode => $name)
                                <option {{ old('pc', array_key_exists(auth()->user()->defaultLocation(), $reportingOrganisations) ? auth()->user()->defaultLocation() : 'All' ) == $plantCode ? 'selected' : '' }} value="{{ $plantCode }}">
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                    <label for="date_start">From</label>
                    <div class="input-group">
                        <div class="input-group-addon"><i class="fas fa-calendar-alt"></i></div>
                        <input
                            style="max-width:100px;"
                            autocomplete="off"
                            id="date_start"
                            type="text"
                            class="datepicker form-control filter input-sm"
                            placeholder="dd/mm/yyyy"
                            name="date_start"
                            value="{{ old('date_start') }}"
                        >
                    </div>
                    <label for="date_end">To</label>
                    <div class="input-group">
                        <div class="input-group-addon"><i class="fas fa-calendar-alt"></i></div>
                        <input
                            style="max-width:100px;"
                            autocomplete="off"
                            id="date_end"
                            type="text"
                            class="datepicker form-control filter input-sm"
                            placeholder="dd/mm/yyyy"
                            name="date_end"
                            value="{{ old('date_end') }}"
                        >
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">Submit <i class="fas fa-chevron-right"></i></button>
					<a href="{{ route('datasets.index', ['reset' => true]) }}" type="button" class="btn btn-info btn-sm"><i class="fas fa-sync-alt"></i> Reset</a>
                </form>  
            </div>
            
            @if (count($datasets))
                <p class="displaying">
                    Displaying {{ $datasets->firstItem() }} to {{ $datasets->lastItem() }} of {{ number_format($datasets->total()) }} datasets.
                </p>
                
                @include('partials.key')
                
                <div class="table-responsive">
                    <table class="table table-hover" style="white-space:nowrap;">
                        <tr>
                            <th>Action</th>
                            <th>Valid</th>
                            @if (in_array(app()->environment(), ['local', 'dev']))
                                <th>Collins</th>
                            @endif
                            <!--<th>PP</th>-->
                            <th>
                                <a title="Order by ID" href="{{ request()->fullUrlWithQuery(['orderby' => 'user', 'page' => 1]) }}">
                                    User <i class="fas fa-sort"></i>
                                </a>
                            </th>
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
                                    Rep. Code <i class="fas fa-sort"></i>
                                </a>
                            </th>
                            <th>
                                <a title="Order by Receipt Date" href="{{ request()->fullUrlWithQuery(['orderby' => 'date', 'page' => 1]) }}">
                                    Rec. Date <i class="fas fa-sort"></i>
                                </a>
                            </th>
                        </tr>
                        
                        @foreach ($datasets as $dataset)
                            <?php $valid = $dataset->is_valid; ?>
                            
                            <?php 
                                
                                if ($dataset->status == 'complete_shipped' || $dataset->status == 'complete_scrapped') {
                                    $rowClass = 'success';
                                } else if ($dataset->status == 'subcontracted') {
                                    $rowClass = 'warning';
                                } else {
                                    $rowClass = '';
                                }
                            ?>
                            
                            <tr class="{{ $rowClass }}">
                                <td>
                                    <a href="{{ route('header.edit', $dataset->id) }}" title="Edit" class="btn btn-sm btn-primary">
                                        <i class="fas fa-pencil-alt"></i> Edit
                                    </a>
                                    
                                    <button type="button" role="button" title="Put on standby" class="btn btn-sm btn-warning" onclick="putOnStandby({{ (int) $dataset->id }});">
                                        <i class="fas fa-pause"></i>
                                    </button>
                                    
                                    <button type="button" role="button" title="Delete" class="btn btn-sm btn-danger" onclick="doDelete({{ (int) $dataset->id }});">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                
                                </td>
                                <td>{{ $valid ? tick() : cross() }}</td>
                                @if (in_array(app()->environment(), ['local', 'dev']))
                                    <td>{{ $dataset->is_utas ? tick() : '-' }}</td>
                                @endif
                                <!--<td class="piece-part-count">{{-- $dataset->piece_part_count ?: '-' --}}</td>-->
                                <td>{{ $dataset->acronym }}</td>
                                <td>{{ $dataset->id }}</td>
                                <td>{{ $dataset->RCS_MPN ?? '-' }}</td>
                                <td>{{ $dataset->RCS_SER ?? '-' }}</td>
                                <td>{{ $dataset->HDR_ROC ?? '-' }}</td>
                                <td>{{ date('d/m/y', strtotime($dataset->RCS_MRD)) ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </table>
                    
                    {{ $datasets->appends([
                        'search' => request()->search,
                        'status' => request()->status,
                        'filter' => request()->filter,
                        'pc' => request()->pc,
                        'date_start' => request()->date_start,
                        'date_end' => request()->date_end,
                        'orderby' => request()->orderby,
                        'order' => request()->order == 'asc' ? 'desc' : 'asc'
                    ])->links() }}
                </div>
            @else
                <p>No datasets found.</p>
            @endif
        </div>
    </div>
@endsection

@push ('footer-scripts')
    <script>
        function startSpinner() {
            $('#ajax-loader').show();
        }
        
        function stopSpinner() {
            $('#ajax-loader').hide();
        }
        
        window.onload = function(){
            stopSpinner();
        }
	</script>
    <script src="{{ asset('js/filter.js?v=1.1') }}"></script>
    <script src="{{ asset('js/datepicker.js?v=1.2') }}"></script>
    
    <script>
        function putOnStandby(id)
        {
        	console.log('putting on standby');
        	
        	Number.prototype.pad = function(size) {
              var s = String(this);
              while (s.length < (size || 2)) {s = "0" + s;}
              return s;
            }
            
            id = id.pad(12); // zerofill id to 12 characters.
        	
        	$.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            var url = "{{ route('status.put-on-standby') }}";
        	
        	$.ajax({
                type: "POST",
                url : url,
                data:{id:id},
                success : function(data){
                    showStatusChangeAlert('on_standby');
                    setTimeout(function(){
                        location.reload(true);
                    }, 2000);
                },
                error: function(xhr, ajaxOptions, thrownError){
                    showErrorAlert();
                    console.log(xhr);
                    console.log(ajaxOptions);
                    console.log(thrownError);
                }
            });
        }
        
        function doDelete(id)
        {
        	console.log('deleting');
        	
        	Number.prototype.pad = function(size) {
              var s = String(this);
              while (s.length < (size || 2)) {s = "0" + s;}
              return s;
            }
            
            id = id.pad(12); // zerofill id to 12 characters.
        	
        	$.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            var url = "{{ route('status.delete') }}";
        	
        	$.ajax({
                type: "POST",
                url : url,
                data:{id:id},
                success : function(data){
                    showStatusChangeAlert('deleted');
                    setTimeout(function(){
                        location.reload(true);
                    }, 2000);
                },
                error: function(xhr, ajaxOptions, thrownError){
                    showErrorAlert();
                    console.log(xhr);
                    console.log(ajaxOptions);
                    console.log(thrownError);
                }
            });
        }
        
        function showStatusChangeAlert(status)
        {
            var message;
            
            if (status == 'on_standby') {
                message = 'put on standby';
            } else if (status == 'deleted') {
                message = 'deleted';
            } else if (status == 'restored') {
                message = 'restored';
            } else if ('removed_on_standby') {
                message = 'removed from on standby';
            }
            
            swal({
              title: 'Success!',
              text: 'Record ' + message + ' successfully!',
              type: 'success',
              showConfirmButton: false,
              timer: 2000
            });
        }
        
        function showErrorAlert()
        {
            swal({
              title: 'Error!',
              text: 'Oops! Something went wrong, please try again later.',
              type: 'error',
              showConfirmButton: false,
              timer: 2000
            });
        }
    </script>
@endpush