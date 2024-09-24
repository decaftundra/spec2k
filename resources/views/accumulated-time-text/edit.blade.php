@extends('layouts.default')

@section('content')
    <div class="row">
        <div class="col-sm-3 col-md-2">
            @include('partials.report-nav')
        </div>
        <div class="col-sm-9 col-md-10">
            
            <h1>Accumulated Time Text {!! $mandatory ? '<span class="text-danger">*</span>' : '' !!}</h1>
            
            @include('partials.report-header')
            
            <hr>
            
            <form method="POST" action="{{ route('accumulated-time-text.update', $notificationId) }}">
                @include('partials.form-body')
            </form>
        </div>
    </div>
@endsection