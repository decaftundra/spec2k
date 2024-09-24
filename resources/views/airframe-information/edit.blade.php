@extends('layouts.default')

@section('content')
    <div class="row">
        <div class="col-sm-3 col-md-2">
            @include('partials.report-nav')
        </div>
        <div class="col-sm-9 col-md-10">
            
            <h1>Airframe Information {!! $mandatory ? '<span class="text-danger">*</span>' : '' !!}</h1>
            
            @include('partials.report-header')
            
            <hr>
            
            <div class="alert alert-warning" role="alert">
                <p><i class="fas fa-exclamation-triangle"></i> If changing or manually entering this information please check the corresponding Engine Information segment data is relevant.</p>
            </div>
            
            <form method="POST" action="{{ route('airframe-information.update', $notificationId) }}">
                @include('partials.form-body')
            </form>
        </div>
    </div>
@endsection

@push('footer-scripts')
    <link rel="stylesheet" href="{{ asset('css/jquery-ui.min.css') }}">
    <script src="{{ asset('js/jquery-ui.min.js') }}"></script>
    <script>
        // On page load populate values if a unique record is found.
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            var reg = $('#REG').val();
            var ain = $('#AIN').val();
            var mfn = $('#MFN').val();
            var mfr = $('#MFR').val();
            var amc = $('#AMC').val();
            var ase = $('#ASE').val();
            
            var data = { reg: reg , ain: ain, mfn: mfn, mfr: mfr, amc: amc, ase: ase };
            
            //console.log(data);
        
            $.ajax({
                type: "POST",
                url: "{{ route('airframe-information.get-aircraft-detail') }}",
                data: data,
                success: function (data) {
                    //console.log(data);
                    populateInputs(data);
                },
                error: function (data) {
                    //console.log(data);
                }
            });
        });
        
        // Add reset button.
        $('button[type="submit"]').after(
            '&nbsp;<button type="submit" class="btn btn-info" id="reset" name="reset">Reset Airframe Info <i class="fas fa-sync-alt"></i></button>'
        );
        
        $('#reset').on('click', function(event){
            event.preventDefault();
            $('.autocomplete').autocomplete('close').val('');
        });
        
        $( ".autocomplete" ).autocomplete({
            source: function (request, response) {
        
                var reg = $('#REG').val();
                var ain = $('#AIN').val();
                var mfn = $('#MFN').val();
                var mfr = $('#MFR').val();
                var amc = $('#AMC').val();
                var ase = $('#ASE').val();
                var id = this.element[0].id;
                
                var data = {
                    element: id,
                    term: request.term,
                    reg: reg,
                    ain: ain,
                    mfn: mfn,
                    mfr: mfr,
                    amc: amc,
                    ase: ase
                };
                
                //console.log(data);
                
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                
                //console.log(data);
            
                $.ajax({
                    type: "POST",
                    url: "{{ route('airframe-information.get-autocomplete') }}",
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
        	
        $( ".autocomplete" ).on( "autocompleteclose", function( event, ui ) {
            
            //console.log('doing close stuff');
            
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            var reg = $('#REG').val();
            var ain = $('#AIN').val();
            var mfn = $('#MFN').val();
            var mfr = $('#MFR').val();
            var amc = $('#AMC').val();
            var ase = $('#ASE').val();
            
            var data = { reg: reg , ain: ain, mfn: mfn, mfr: mfr, amc: amc, ase: ase };
            
            //console.log(data);
        
            $.ajax({
                type: "POST",
                url: "{{ route('airframe-information.get-aircraft-detail') }}",
                data: data,
                success: function (data) {
                    //console.log('success');
                    //console.log(data);
                    populateInputs(data);
                },
                error: function (data) {
                    //console.log(data);
                }
            });
        });
        
        function populateInputs(data)
        {
            //console.log(data);
            
            if (data.aircraft_fully_qualified_registration_no) $('#REG').val(data.aircraft_fully_qualified_registration_no);
            if (data.aircraft_identification_no) $('#AIN').val(data.aircraft_identification_no);
            if (data.manufacturer_name) $('#MFN').val(data.manufacturer_name);
            if (data.manufacturer_code) $('#MFR').val(data.manufacturer_code);
            if (data.aircraft_model_identifier) $('#AMC').val(data.aircraft_model_identifier);
            if (data.aircraft_series_identifier) $('#ASE').val(data.aircraft_series_identifier);
        }
    </script>
@endpush