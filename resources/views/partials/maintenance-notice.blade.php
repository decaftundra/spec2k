@if (count($notices))
    @foreach ($notices as $notice)
        <div class="alert alert-danger" role="alert">
            <h3>{{ $notice->title }}</h3>
            
            <p>{!! nl2br($notice->contents) !!}</p>
        </div>
    @endforeach
@endif