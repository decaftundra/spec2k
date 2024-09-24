@extends('layouts.default')

@section('content')
    <div class="row">
        <div class="col-xs-12">
	        <h1>Fribourg Parts List</h1>
            <div class="navbar-form">
                <form action="{{ request()->url() }}" method="GET">
                    <div class="form-group">
                        <input name="search" type="text" class="form-control input-sm" placeholder="Search" value="{{ old('search') }}">
					</div>
                    <button type="submit" class="btn btn-primary btn-sm">Submit <i class="fas fa-chevron-right"></i></button>
					<a href="{{ route('info.fribourg-parts', ['reset' => true]) }}" type="button" class="btn btn-info btn-sm"><i class="fas fa-sync-alt"></i> Reset</a>
                </form>  
            </div>
                
            @if(count($parts))
            
                <p class="displaying">Displaying {{ $parts->firstItem() }} to {{ $parts->lastItem() }} of {{ $parts->total() }} parts.</p>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <th>
                            <a title="Order by part number" href="{{ request()->fullUrlWithQuery(['page' => 1]) }}">
                                Part Number <i class="fas fa-sort"></i>
                            </a>
                        </th>
                        
                        @foreach($parts as $part)
                            <tr>
                                <td valign="middle"><span class="bold">{{ $part }}</span></td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            
                {{ $parts->appends([
                    'search' => request()->search,
                    'order' => request()->order == 'asc' ? 'desc' : 'asc'
                ])->links() }}
            @else
                <p>No parts to display.</p>
            @endif
        </div>
    </div>
@endsection