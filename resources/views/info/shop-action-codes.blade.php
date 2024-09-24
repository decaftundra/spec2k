@extends('layouts.default')

@section('content')
    <div class="row">
        <div class="col-xs-12">
	        <h1>Shop Action Codes</h1>
            
            <div class="navbar-form">
                <form action="{{ request()->url() }}" method="GET">
					@if (count($sac))
                        <label for="sac">Shop Action Code</label>
                        <select name="sac" class="form-control filter input-sm">
                            
                            @foreach ($sac as $value => $name)
                                <option {{ old('sac') == $value ? 'selected' : '' }} value="{{ $value }}">
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                    
                    @if (count($rfi))
                        <label for="rfi">Repair Final Action Indicator</label>
                        <select name="rfi" class="form-control filter input-sm">
                            @foreach ($rfi as $value => $name)
                                <option {{ old('rfi', NULL) == $value && !is_null(old('rfi')) ? 'selected' : '' }} value="{{ $value }}">
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                    
                    <button type="submit" class="btn btn-primary btn-sm">Submit <i class="fas fa-chevron-right"></i></button>
					<a href="{{ route('info.shop-action-codes', ['reset' => true]) }}" type="button" class="btn btn-info btn-sm"><i class="fas fa-sync-alt"></i> Reset</a>
                </form>
            </div>
                
            @if(count($codes))
            
                <p class="displaying">Displaying {{ $codes->firstItem() }} to {{ $codes->lastItem() }} of {{ $codes->total() }} Shop Action Codes.</p>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <th>
                            <a title="Order by Shop Action Code" href="{{ request()->fullUrlWithQuery(['orderby' => 'sac', 'page' => 1]) }}">
                                Shop Action Code (SAC) <i class="fas fa-sort"></i>
                            </a>
                        </th>
                        <th>
                            <a title="Order by Repair Final Action Indicator" href="{{ request()->fullUrlWithQuery(['orderby' => 'rfi', 'page' => 1]) }}">
                                Repair Final Action Indicator (RFI) <i class="fas fa-sort"></i>
                            </a>
                        </th>
                        
                        @foreach($codes as $code)
                            <tr>
                                <td valign="middle"><span class="bold">{{ $code->SAC }}</span></td>
                                <td valign="middle">
                                    @if (is_null($code->RFI))
                                        -
                                    @else
                                        {{ $code->RFI == 1 ? 'Yes' : 'No' }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            
                {{ $codes->appends([
                    'sac' => request()->sac,
                    'rfi' => request()->rfi,
                    'orderby' => request()->orderby,
                    'order' => request()->order == 'asc' ? 'desc' : 'asc'
                ])->links() }}
            @else
                <p>No Shop Action Codes to display.</p>
            @endif
        </div>
    </div>
@endsection