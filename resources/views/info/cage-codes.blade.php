@extends('layouts.default')

@section('content')
    <div class="row">
        <div class="col-xs-12">
	        <h1>Manufacturer Cage Codes</h1>
            <div class="navbar-form">
                <form action="{{ request()->url() }}" method="GET">
                    <div class="form-group">
                        <input name="search" type="text" class="form-control input-sm" placeholder="Search" value="{{ old('search') }}">
					</div>
                    <button type="submit" class="btn btn-primary btn-sm">Submit <i class="fas fa-chevron-right"></i></button>
					<a href="{{ route('info.cage-codes', ['reset' => true]) }}" type="button" class="btn btn-info btn-sm"><i class="fas fa-sync-alt"></i> Reset</a>
                </form>  
            </div>
                
            @if(count($cageCodes))
            
                <p class="displaying">Displaying {{ $cageCodes->firstItem() }} to {{ $cageCodes->lastItem() }} of {{ $cageCodes->total() }} cage codes.</p>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <th>
                            <a title="Order by name" href="{{ request()->fullUrlWithQuery(['orderby' => 'code', 'page' => 1]) }}">
                                Cage Code <i class="fas fa-sort"></i>
                            </a>
                        </th>
                        <th>
                            <a title="Order by info" href="{{ request()->fullUrlWithQuery(['orderby' => 'info', 'page' => 1]) }}">
                                Info <i class="fas fa-sort"></i>
                            </a>
                        </th>
                        
                        @foreach($cageCodes as $cageCode)
                            <tr>
                                <td valign="middle"><span class="bold">{{ $cageCode->cage_code }}</span></td>
                                <td valign="middle"><span class="bold">{{ $cageCode->info }}</span></td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            
                {{ $cageCodes->appends([
                    'search' => request()->search,
                    'orderby' => request()->orderby,
                    'order' => request()->order == 'asc' ? 'desc' : 'asc'
                ])->links() }}
            @else
                <p>No manufacturer cage codes to display.</p>
            @endif
        </div>
    </div>
@endsection