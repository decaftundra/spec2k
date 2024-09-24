@extends('layouts.default')

@section('content')
    <div class="row">
        <div class="col-sm-3 col-md-2">
            @include('partials.report-nav')
        </div>
        <div class="col-sm-9 col-md-10">
            
            <h1>Replaced Piece Part {!! $mandatory ? '<span class="text-danger">*</span>' : '' !!} <small>{{ $piecePartDetailId }}</small></h1>
            
            @include('partials.piece-part-nav')
            
            @include('partials.report-header')
            
            <hr>
            
            <div class="row">
                <form method="POST" action="{{ route('replaced-piece-part.update', [$notificationId, $piecePartDetailId]) }}">
                    @include('partials.form-body')
                </form>
            </div>
            
        </div>
    </div>
@endsection