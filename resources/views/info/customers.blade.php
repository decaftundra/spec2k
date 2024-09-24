@extends('layouts.default')

@section('content')
    <div class="row">
        <div class="col-xs-12">
	        <h1>Customers</h1>
            <div class="navbar-form">
                <form action="{{ request()->url() }}" method="GET">
                    <div class="form-group">
                        <input name="search" type="text" class="form-control input-sm" placeholder="Search" value="{{ old('search') }}">
					</div>
                    <button type="submit" class="btn btn-primary btn-sm">Submit <i class="fas fa-chevron-right"></i></button>
					<a href="{{ route('info.customers', ['reset' => true]) }}" type="button" class="btn btn-info btn-sm"><i class="fas fa-sync-alt"></i> Reset</a>
                </form>  
            </div>
                
            @if(count($customers))
            
                <p class="displaying">Displaying {{ $customers->firstItem() }} to {{ $customers->lastItem() }} of {{ $customers->total() }} customers.</p>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <th>
                            <a title="Order by company name" href="{{ request()->fullUrlWithQuery(['orderby' => 'company', 'page' => 1]) }}">
                                Company Name <i class="fas fa-sort"></i>
                            </a>
                        </th>
                        <th>
                            <a title="Order by icao code" href="{{ request()->fullUrlWithQuery(['orderby' => 'code', 'page' => 1]) }}">
                                ICAO Code <i class="fas fa-sort"></i>
                            </a>
                        </th>
                        
                        @foreach($customers as $customer)
                            <tr>
                                <td valign="middle"><span class="bold">{{ $customer->company_name }}</span></td>
                                <td valign="middle">{{ $customer->icao }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            
                {{ $customers->appends([
                    'search' => request()->search,
                    'orderby' => request()->orderby,
                    'order' => request()->order == 'asc' ? 'desc' : 'asc'
                ])->links() }}
            @else
                <p>No customers to display.</p>
            @endif
        </div>
    </div>
@endsection