@extends('layouts.default')

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <h1>Issues</h1>
            
            <div class="navbar-form">
                <form action="{{ request()->url() }}" method="GET">
                    <div class="form-group">
                        <select name="status" class="form-control input-sm">
                            <option value="all" {{ old('status', 'open') == 'all' ? 'selected' : '' }}>All Statuses</option>
                            <option value="open" {{ old('status', 'open') == 'open' ? 'selected' : '' }}>Open Issues</option>
                        </select>
                        <input name="search" type="text" class="form-control input-sm" placeholder="Search" value="{{ old('search') }}">
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">Submit <i class="fas fa-chevron-right"></i></button>
					<a href="{{ route('issue-tracker.index', ['reset' => true]) }}" type="button" class="btn btn-info btn-sm"><i class="fas fa-sync-alt"></i> Reset</a>
					&nbsp;<a class="btn btn-sm btn-danger" href="{{ route('issue-tracker.create') }}">Raise Issue <i class="fas fa-plus"></i></a>
                </form>
            </div>
            
            @if (count($issues))
                <p class="displaying">
                    Displaying {{ $issues->firstItem() }} to {{ $issues->lastItem() }} of {{ number_format($issues->total()) }} issues.
                </p>
                
                <div class="table-responsive">
                    <table class="table table-hover" style="white-space:nowrap;">
                        <tr>
                            <th>Title</th>
                            <th>Posted by</th>
                            <th>Type</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Date</th>
                            
                            @can ('edit', $issues->first())
                                <th>Edit</th>
                            @endcan
                        </tr>
                        
                        @foreach ($issues as $issue)
                            <tr>
                                <td><a href="{{ route('issue-tracker.show', $issue->id) }}">{{ ucfirst($issue->title) }}</a></td>
                                <td>{{ $issue->getPostedBy() }}</td>
                                <td>{!! $kindIcons[$issue->kind] ?? '-' !!}</td>
                                <td>{!! $priorityIcons[$issue->priority] !!}</td>
                                <td>{!! $statuses[$issue->status] ?? '-' !!}</td>
                                <td>{{ date('d/m/Y', strtotime($issue->created_at)) }}</td>
                                
                                @can ('edit', $issue)
                                    <td>
                                        <a class="btn btn-warning btn-sm" href="{{ route('issue-tracker.edit', $issue->id) }}">
                                            <i class="fas fa-pencil-alt"></i> Edit
                                        </a>
                                    </td>
                                @endcan
                            </tr>
                        @endforeach
                    </table>
                    
                    {{ $issues->appends([
                        'search' => request()->search,
                        'status' => request()->status,
                        'order' => request()->order == 'asc' ? 'desc' : 'asc'
                    ])->links() }}
                </div>
            @else
                <p>No issues found.</p>
            @endif
        </div>
    </div>
@endsection