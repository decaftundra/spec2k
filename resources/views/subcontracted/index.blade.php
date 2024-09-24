@extends('layouts.default')

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <h1>Subcontracted</h1>
            
            <div class="navbar-form">
                <form action="{{ request()->url() }}" method="GET">
                    <div class="form-group">
                        <input name="search" type="text" class="form-control input-sm" placeholder="Search" value="{{ old('search') }}">
					</div>
					<div class="form-group">
    					<select name="filter" class="filter form-control input-sm">
        					<option value="">Select filter...</option>
        					<option value="valid" {{ old('filter') == 'valid' ? 'selected' : '' }}>Valid</option>
        					<option value="invalid" {{ old('filter') == 'invalid' ? 'selected' : '' }}>Invalid</option>
    					</select>
					</div>
					@if (count($reportingOrganisations))
                        <select name="roc" class="form-control filter input-sm">
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
                            autocomplete="off"
                            id="date_end"
                            type="text"
                            class="datepicker form-control filter input-sm"
                            placeholder="dd/mm/yyyy"
                            name="date_end"
                            value="{{ old('date_end') }}"
                        >
                    </div>
                    <button type="submit" class="btn btn-default btn-sm">Submit <i class="fas fa-chevron-right"></i></button>
					<a href="{{ route('deleted.index', ['reset' => true]) }}" type="button" class="btn btn-default btn-sm"><i class="fas fa-sync-alt"></i> Reset</a>
                </form>  
            </div>
            
            @if (count($datasets))
                <p class="displaying">
                    Displaying {{ $datasets->firstItem() }} to {{ $datasets->lastItem() }} of {{ number_format($datasets->total()) }} datasets.
                </p>
                
                <div class="table-responsive">
                    <table class="table table-hover" style="white-space:nowrap;">
                        <tr>
                            <th>Val.</th>
                            <th>Action</th>
                            <th>UT</th>
                            <th>PP</th>
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
                        
                        @foreach ($datasets as $dataset)
                            <?php $valid = $dataset->is_valid; ?>
                            
                            <tr>
                                <td>{{ $valid ? tick() : cross() }}</td>
                                <td>
                                    <a href="{{ route('header.edit', $dataset->id) }}" title="Edit" class="btn btn-sm btn-primary">
                                        <i class="fas fa-pencil-alt"></i> Edit Dataset
                                    </a>
                                    <button type="button" role="button" title="Put into in progress or to do list" class="btn btn-sm btn-success" onclick="changeStatus({{ (int) $dataset->id }}, 'in_progress');">
                                        <i class="fas fa-play"></i>
                                    </button>
                                </td>
                                <td>{{ $dataset->is_utas ? tick() : '-' }}</td>
                                <td>{{ $dataset->piece_part_count ?: '-' }}</td>
                                <td>{{ $dataset->id }}</td>
                                <td>{{ $dataset->RCS_MPN ?? '-' }}</td>
                                <td>{{ $dataset->RCS_SER ?? '-' }}</td>
                                <td>{{ $dataset->HDR_ROC ?? '-' }}</td>
                                <td>{{ $dataset->HDR_RON ?? '-' }}</td>
                                <td>{{ date('d/m/Y', strtotime($dataset->RCS_MRD)) ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </table>
                    
                    {{ $datasets->appends([
                        'search' => request()->search,
                        'roc' => request()->roc,
                        'filter' => request()->filter,
                        'date_start' => request()->date_start,
                        'date_end' => request()->date_end,
                        'orderby' => request()->orderby,
                        'order' => request()->order == 'asc' ? 'desc' : 'asc'
                    ])->links() }}
                </div>
            @else
                <p>No subcontracted datasets found.</p>
            @endif
        </div>
    </div>
@endsection

@push ('footer-scripts')
    <script src="{{ asset('js/filter.js?v=1.1') }}"></script>
    <script src="{{ asset('js/datepicker.js?v=1.1') }}"></script>
    <script>
        function changeStatus(id, status)
        {
        	console.log('changing status');
        	
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
            
            var url = "{{ route('status.change') }}";
        	
        	$.ajax({
                type: "POST",
                url : url,
                data:{id:id, status:status},
                success : function(data){
                    showStatusChangeAlert(status);
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
            } else {
                message = 'added to in progress or to do';
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