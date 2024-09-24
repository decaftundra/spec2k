@extends('layouts.default')

@section('content')
    <div class="row">
        <div class="col-xs-12">
	        <h1>Excluded Part Numbers</h1>
	        
	        @can ('view-all-part-lists')
    	        <div class="alert alert-danger" role="alert">
        	        <i class="fas fa-exclamation-triangle"></i> Warning. Any changes made to this data will affect the application globally.
    	        </div>
	        @endcan
                
            @if(count($locations))
            
                <p class="displaying">Displaying {{ $locations->firstItem() }} to {{ $locations->lastItem() }} of {{ $locations->total() }} excluded part number lists.</p>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <th>Name</th>
                        <th>ERP Name</th>
                        <th>Plant Code</th>
                        <th colspan="2">Action</th>
                        
                        @foreach($locations as $location)
                            <tr>
                                <td valign="middle"><span class="bold">{{ $location->name }}</span></td>
                                <td valign="middle"><span class="bold">{{ $location->sap_location_name }}</span></td>
                                <td valign="middle"><span class="bold">{{ $location->plant_code }}</span></td>
                                
                                @if ($location->part_list)
                                    @can('edit', $location->part_list)
                                        <td>
                                            <a class="btn btn-sm btn-warning" href="{{ route('part-list.edit', $location->part_list->id) }}">
                                                Edit <i class="fas fa-pencil-alt"></i>
                                            </a>
                                        </td>
                                    @endcan
                                    @can('delete', $location->part_list)
                                        <td>
                                            <a class="btn btn-sm btn-danger" href="{{ route('part-list.delete', $location->part_list->id) }}">
                                                Delete <i class="far fa-trash-alt"></i>
                                            </a>
                                        </td>
                                    @endcan
                                @else
                                    @can('create', App\PartList::class)
                                        <td>
                                            <a class="btn btn-sm btn-primary" href="{{ route('part-list.create', $location->id) }}">
                                                Create <i class="fas fa-plus"></i>
                                            </a>
                                        </td>
                                    @endcan
                                @endif
                            </tr>
                        @endforeach
                    </table>
                </div>
            
                {{ $locations->links() }}
            @else
                <p>No excluded part number lists to display.</p>
            @endif
        </div>
    </div>
@endsection