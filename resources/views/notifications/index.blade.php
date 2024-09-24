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
            <h1>To Do</h1>
            
            @if ($latestExtract)
                <p>Latest Extract: started: {{ $latestExtract->started_at->setTimezone($timezone)->format('d/m/y H:i:s') }}, ended: {{ $latestExtract->ended_at->setTimezone($timezone)->format('d/m/y H:i:s') }}.</p>
            @endif
            
            <div class="navbar-form">
                <form action="{{ request()->url() }}" method="GET" class="form-inline">
                    <div class="form-group">
                        <input name="search" type="text" class="form-control input-sm" placeholder="Search" value="{{ old('search') }}">
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
					</div>
                    <button type="submit" class="btn btn-primary btn-sm">Submit <i class="fas fa-chevron-right"></i></button>
					<a href="{{ route('notifications.index', ['reset' => true]) }}" type="button" class="btn btn-info btn-sm"><i class="fas fa-sync-alt"></i> Reset</a>
                </form>  
            </div>
            
            
            
            @if (count($notifications))
                <p class="displaying">
                    Displaying {{ $notifications->firstItem() }} to {{ $notifications->lastItem() }} of {{ number_format($notifications->total()) }} notifications.
                </p>
                
                @include('partials.key')
                
                <div class="table-responsive">
                    <table class="table table-hover" style="white-space:nowrap;">
                        <tr>
                            <th>Action</th>
                            @if (in_array(app()->environment(), ['local', 'dev']))
                                <th>Collins</th>
                            @endif
                            <!--<th>PP</th>-->
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
                                <a title="Order by Serial No." href="{{ request()->fullUrlWithQuery(['orderby' => 'serial', 'page' => 1]) }}">
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
                        
                        @foreach ($notifications as $notification)
                            <?php 
                            if ($notification->status == 'complete_shipped' || $notification->status == 'complete_scrapped') {
                                $rowClass = 'success';
                            } else if ($notification->status == 'subcontracted') {
                                $rowClass = 'warning';
                            } else {
                                $rowClass = '';
                            }
                            ?>
                            
                            <tr class="{{ $rowClass }}">
                                <td>
                                    <a href="{{ route('header.edit', $notification->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-plus"></i> Create
                                    </a>
                                    <button type="button" role="button" title="Delete" class="btn btn-sm btn-danger" onclick="doDelete({{ (int) $notification->id }});">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                                @if (in_array(app()->environment(), ['local', 'dev']))
                                    <td>{{ $notification->is_utas ? tick() : '-' }}</td>
                                @endif
                                <!--<td class="piece-part-count">{{-- $notification->piece_part_count ?: '-' --}}</td>-->
                                <td>{{ $notification->rcsSFI ?: '-' }}</td>
                                <td>{{ $notification->rcsMPN ?: '-' }}</td>
                                <td>{{ $notification->rcsSER ?: '-' }}</td>
                                <td>{{ $notification->hdrROC ?: '-' }}</td>
                                <td>{{ date('d/m/y', strtotime($notification->rcsMRD)) ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </table>
                    
                    {{ $notifications->appends([
                        'search' => request()->search,
                        'status' => request()->status,
                        'pc' => request()->pc,
                        'date_start' => request()->date_start,
                        'date_end' => request()->date_end,
                        'orderby' => request()->orderby,
                        'order' => request()->order == 'asc' ? 'desc' : 'asc'
                    ])->links() }}
                </div>
            @else
                <p>No notifications found.</p>
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
        function doDelete(id)
        {
        	startSpinner();
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