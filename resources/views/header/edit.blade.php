@extends('layouts.default')

@section('content')
    <div class="row">
        <div class="col-sm-3 col-md-2">
            @include('partials.report-nav')
        </div>
        <div class="col-sm-9 col-md-10">
            
            <h1>Header {!! $mandatory ? '<span class="text-danger">*</span>' : '' !!}</h1>
            
            @include('partials.report-header')
            
            <hr>
            
            <div class="row">
                <form autocomplete="off" method="POST" action="{{ route('header.update', $notificationId) }}">
                    @include('partials.form-body')
                </form>
            </div>
            
        </div>
    </div>
@endsection

@push('footer-scripts')
    <link rel="stylesheet" href="{{ asset('css/jquery-ui.min.css') }}">
    <script src="{{ asset('js/jquery-ui.min.js') }}"></script>
    <script>
        // Reporting Organisation autocomplete.
        var is_admin = '{{ auth()->check() && !auth()->user()->isUser() }}';
        
        if (is_admin){
            // Add reset button.
            $('button[type="submit"]').after(
                '&nbsp;<button type="submit" class="btn btn-info" id="ron-reset" name="reset">Reset Reporting Org. <i class="fas fa-sync-alt"></i></button>'
            );
            
            $('#ron-reset').on('click', function(event){
                event.preventDefault();
                $('.autocomplete').autocomplete('close').val('');
            });
            
            // Add reset button.
            $('#ron-reset').after(
                '&nbsp;<button type="submit" class="btn btn-info" id="cust-reset" name="reset">Reset Company Info <i class="fas fa-sync-alt"></i></button>'
            );
            
            $('#cust-reset').on('click', function(event){
                event.preventDefault();
                $('.cust-autocomplete').autocomplete('close').val('');
            });
        } else {
            // Add reset button.
            $('button[type="submit"]').after(
                '&nbsp;<button type="submit" class="btn btn-info" id="cust-reset" name="reset">Reset Company Info <i class="fas fa-sync-alt"></i></button>'
            );
            
            $('#cust-reset').on('click', function(event){
                event.preventDefault();
                $('.cust-autocomplete').autocomplete('close').val('');
            });
        }
        
        $('.autocomplete').autocomplete({
            source: function (request, response) {
        
                var roc = $('#ROC').val();
                var ron = $('#RON').val();
                var id = this.element[0].id;
                var data = {element: id, term: request.term, roc: roc, ron: ron };
                
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
            
                $.ajax({
                    type: "POST",
                    url: "{{ route('header.get-autocomplete') }}",
                    data: data,
                    success: function (data) {
                        console.log('autocomplete...');
                        console.log(data);
                        response(data);
                    },
                    error: function () {
                        response([]);
                    }
                });
            }
        });
        	
        $('.autocomplete').on( "autocompleteclose", function( event, ui ) {
            
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            var ron = $('#RON').val();
            var roc = $('#ROC').val();
            
            var data = { ron: ron , roc: roc };
        
            $.ajax({
                type: "POST",
                url: "{{ route('header.get-reporting-organisation') }}",
                data: data,
                success: function (data) {
                    console.log('populating inputs');
                    console.log(data);
                    populateInputs(data);
                },
                error: function (data) {
                    //console.log(data);
                }
            });
        });
        
        function populateInputs(data)
        {
            if (data.name) $('#RON').val(data.name);
            if (data.cage_code) $('#ROC').val(data.cage_code);
        }
    
        // Customers autocomplete.
        
        $('.cust-autocomplete').autocomplete({
            source: function (request, response) {
        
                var opr = $('#OPR').val();
                var who = $('#WHO').val();
                var id = this.element[0].id;
                var data = {element: id, term: request.term, opr: opr, who: who };
                
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
            
                $.ajax({
                    type: "POST",
                    url: "{{ route('header.get-customers-autocomplete') }}",
                    data: data,
                    success: function (data) {
                        //console.log(data);
                        response(data);
                    },
                    error: function () {
                        response([]);
                    }
                });
            }
        });
        	
        $('.cust-autocomplete').on( "autocompleteclose", function( event, ui ) {
            
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            var opr = $('#OPR').val();
            var who = $('#WHO').val();
            
            var data = { opr: opr , who: who };
        
            $.ajax({
                type: "POST",
                url: "{{ route('header.get-customer') }}",
                data: data,
                success: function (data) {
                    //console.log(data);
                    populateCustInputs(data);
                },
                error: function (data) {
                    //console.log(data);
                }
            });
        });
        
        function populateCustInputs(data)
        {
            if (data.icao) $('#OPR').val(data.icao);
            if (data.company_name) $('#WHO').val(data.company_name);
        }
    </script>
@endpush