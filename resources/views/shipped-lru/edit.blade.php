@extends('layouts.default')

@section('content')
    <div class="row">
        <div class="col-sm-3 col-md-2">
            @include('partials.report-nav')
        </div>
        <div class="col-sm-9 col-md-10">
            
            <h1>Shipped LRU {!! $mandatory ? '<span class="text-danger">*</span>' : '' !!}</h1>
            
            @include('partials.report-header')
            
            <hr>
            
            <div class="row">
                <form method="POST" action="{{ route('shipped-lru.update', $notificationId) }}">
                    @include('partials.form-body')
                </form>
            </div>
            
        </div>
    </div>
@endsection