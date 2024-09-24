@extends('layouts.default')

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <h1>{{ $user->fullname }} <small>Activities</small></h1>
            
            @if (count($activities))
                <p class="displaying">
                    Displaying {{ $activities->firstItem() }} to {{ $activities->lastItem() }} of {{ number_format($activities->total()) }} activities.
                </p>
                
                <div class="table-responsive">
                    <table class="table table-hover" style="white-space:nowrap;">
                        <tr>
                            <th>Action</th>
                            <th>Info</th>
                            <th>Notification ID</th>
                            <th>Date</th>
                        </tr>
                        
                        @foreach ($activities as $activity)
                            @include('partials.user-activity')
                        @endforeach
                    </table>
                    
                    {{ $activities->links() }}
                </div>
            @else
                <p>No activities found.</p>
            @endif
        </div>
    </div>
@endsection