@extends('layouts.default')

@section('content')
    <div class="row">
        <div class="col-xs-12">
	        <h1>Received LRU Failure Codes</h1>
	        
            @if ((old('rrc') == 'U') && (old('fhs') == 'HW') && (old('ffc') == 'FT') && (old('fcr') == 'CR'))
                <div class="alert alert-warning" role="alert">
                    <i class="fas fa-exclamation-triangle"></i>
                    {{ $warnings['Failed'] }}
                </div>
            @elseif (in_array(old('rrc'), ['O', 'M', 'S']))
                <div class="alert alert-warning" role="alert">
                    <i class="fas fa-exclamation-triangle"></i>
                    {{ $warnings['NotFailed'] }}
                </div>
            @endif
            
            <div class="navbar-form">
                <form action="{{ request()->url() }}" method="GET">
					@if (count($rrc))
                        <label for="rrc">RRC</label>
                        <select name="rrc" class="form-control filter input-sm">
                            
                            @foreach ($rrc as $value => $name)
                                <option {{ old('rrc') == $value ? 'selected' : '' }} value="{{ $value }}">
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                    
                    @if (count($ffc))
                        <label for="ffc">FFC</label>
                        <select name="ffc" class="form-control filter input-sm">
                            @foreach ($ffc as $value => $name)
                                <option {{ old('ffc') == $value ? 'selected' : '' }} value="{{ $value }}">
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                    
                    @if (count($ffi))
                        <label for="ffi">FFI</label>
                        <select name="ffi" class="form-control filter input-sm">
                            @foreach ($ffi as $value => $name)
                                <option {{ old('ffi') == $value ? 'selected' : '' }} value="{{ $value }}">
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                    
                    @if (count($fhs))
                        <label for="fhs">FHS</label>
                        <select name="fhs" class="form-control filter input-sm">
                            @foreach ($fhs as $value => $name)
                                <option {{ old('fhs') == $value ? 'selected' : '' }} value="{{ $value }}">
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                    
                    <br>
                    
                    @if (count($fcr))
                        <label for="fcr">FCR</label>
                        <select name="fcr" class="form-control filter input-sm">
                            @foreach ($fcr as $value => $name)
                                <option {{ old('fcr') == $value ? 'selected' : '' }} value="{{ $value }}">
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                    
                    @if (count($fac))
                        <label for="fac">FAC</label>
                        <select name="fac" class="form-control filter input-sm">
                            @foreach ($fac as $value => $name)
                                <option {{ old('fac') == $value ? 'selected' : '' }} value="{{ $value }}">
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                    
                    @if (count($fbc))
                        <label for="fbc">FBC</label>
                        <select name="fbc" class="form-control filter input-sm">
                            @foreach ($fbc as $value => $name)
                                <option {{ old('fbc') == $value ? 'selected' : '' }} value="{{ $value }}">
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                    
                    <button type="submit" class="btn btn-primary btn-sm">Submit <i class="fas fa-chevron-right"></i></button>
					<a href="{{ route('info.rcs-failure-codes', ['reset' => true]) }}" type="button" class="btn btn-info btn-sm"><i class="fas fa-sync-alt"></i> Reset</a>
                </form>
            </div>
            
            <a href="{{ route('info.rcs-failure-codes', ['rrc' => 'U', 'fhs' => 'HW', 'ffc' => 'FT', 'fcr' => 'CR', 'ffi' => 'NI']) }}" type="button" class="btn btn-warning btn-sm"><i class="fas fa-exclamation-triangle"></i> Show failed Piece Part warning</a>
	        
	        
	        <a href="{{ route('info.rcs-failure-codes', ['rrc' => substr(str_shuffle('OMS'), 0, 1)]) }}" type="button" class="btn btn-warning btn-sm"><i class="fas fa-exclamation-triangle"></i> Show non-failed Piece Part warning</a>
	        
	        <hr>
                
            @if(count($codes))
            
                <p class="displaying">Displaying {{ $codes->firstItem() }} to {{ $codes->lastItem() }} of {{ $codes->total() }} Received LRU Failure Codes.</p>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <th>
                            <a title="Order by Supplier Removal Type Code" href="{{ request()->fullUrlWithQuery(['orderby' => 'rrc', 'page' => 1]) }}">
                                Supplier Removal Type Code (RRC) <i class="fas fa-sort"></i>
                            </a>
                        </th>
                        <th>
                            <a title="Order by Failure/Fault Found Code" href="{{ request()->fullUrlWithQuery(['orderby' => 'ffc', 'page' => 1]) }}">
                                Failure/Fault Found Code (FFC) <i class="fas fa-sort"></i>
                            </a>
                        </th>
                        <th>
                            <a title="Order by Failure/Fault Induced Code" href="{{ request()->fullUrlWithQuery(['orderby' => 'ffi', 'page' => 1]) }}">
                                Failure/Fault Induced Code (FFI) <i class="fas fa-sort"></i>
                            </a>
                        </th>
                        <th>
                            <a title="Order by Hardware/Software Failure Code" href="{{ request()->fullUrlWithQuery(['orderby' => 'fhs', 'page' => 1]) }}">
                                Hardware/Software Failure Code (FHS) <i class="fas fa-sort"></i>
                            </a>
                        </th>
                        <th>
                            <a title="Order by Failure/Fault Confirm Reason Code" href="{{ request()->fullUrlWithQuery(['orderby' => 'fcr', 'page' => 1]) }}">
                                Failure/Fault Confirm Reason Code (FCR) <i class="fas fa-sort"></i>
                            </a>
                        </th>
                        <th>
                            <a title="Order by Failure/Fault Confirm Aircraft Message Code" href="{{ request()->fullUrlWithQuery(['orderby' => 'fac', 'page' => 1]) }}">
                                Failure/Fault Confirm Aircraft Message Code (FAC) <i class="fas fa-sort"></i>
                            </a>
                        </th>
                        <th>
                            <a title="Order by Failure/Fault Confirm Bite Message Code" href="{{ request()->fullUrlWithQuery(['orderby' => 'fbc', 'page' => 1]) }}">
                                Failure/Fault Confirm Bite Message Code (FBC) <i class="fas fa-sort"></i>
                            </a>
                        </th>
                        
                        @foreach($codes as $code)
                            <tr>
                                <td valign="middle"><span class="bold">{{ $code->RRC }}</span></td>
                                <td valign="middle">{{ $code->FFC }}</td>
                                <td valign="middle">{{ $code->FFI }}</td>
                                <td valign="middle">{{ $code->FHS }}</td>
                                <td valign="middle">{{ $code->FCR }}</td>
                                <td valign="middle">{{ $code->FAC }}</td>
                                <td valign="middle">{{ $code->FBC }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            
                {{ $codes->appends([
                    'rrc' => request()->rrc,
                    'ffc' => request()->ffc,
                    'ffi' => request()->ffi,
                    'fhs' => request()->fhs,
                    'fcr' => request()->fcr,
                    'fac' => request()->fac,
                    'fbc' => request()->fbc,
                    'orderby' => request()->orderby,
                    'order' => request()->order == 'asc' ? 'desc' : 'asc'
                ])->links() }}
            @else
                <p>No Received LRU Failure Codes to display.</p>
            @endif
        </div>
    </div>
@endsection