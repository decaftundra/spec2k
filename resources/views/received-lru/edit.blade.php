@extends('layouts.default')

@section('content')
    <div class="row">
        <div class="col-sm-3 col-md-2">
            @include('partials.report-nav')
        </div>
        <div class="col-sm-9 col-md-10">
            
            <h1>Received LRU {!! $mandatory ? '<span class="text-danger">*</span>' : '' !!}</h1>
            
            @include('partials.report-header')
            
            <hr>
            
            <div class="row">
                <form method="POST" action="{{ route('received-lru.update', $notificationId) }}">
                    @include('partials.form-body')
                </form>
            </div>
            
        </div>
    </div>
@endsection

@push('footer-scripts')
    <script>
        $( document ).ready(function() {
            
            var rrcValues = <?php echo json_encode($rrc); ?>;
            var ffcValues = <?php echo json_encode($ffc); ?>;
            var ffiValues = <?php echo json_encode($ffi); ?>;
            var fhsValues = <?php echo json_encode($fhs); ?>;
            var fcrValues = <?php echo json_encode($fcr); ?>;
            var facValues = <?php echo json_encode($fac); ?>;
            var fbcValues = <?php echo json_encode($fbc); ?>;
            
            // Add reset button.
            $('button[type="submit"]').after(
                '&nbsp;<button type="submit" class="btn btn-info" id="reset" name="reset">Reset Failure Codes <i class="fas fa-sync-alt"></i></button>'
            );
            
            var url = '{{ route("recieved-lru.get-rcs-failure-codes") }}';
            var utas = <?php echo $utas; ?>;
            var rrc = '{{ old("RRC", $segment->get_RCS_RRC()) }}';
            var ffc = '{{ old("FFC", $segment->get_RCS_FFC()) }}';
            var ffi = '{{ old("FFI", $segment->get_RCS_FFI()) }}';
            var fhs = '{{ old("FHS", $segment->get_RCS_FHS()) }}';
            var fcr = '{{ old("FCR", $segment->get_RCS_FCR()) }}';
            var fac = '{{ old("FAC", $segment->get_RCS_FAC()) }}';
            var fbc = '{{ old("FBC", $segment->get_RCS_FBC()) }}';
            
            getCodes(utas, rrc, ffc, ffi, fhs, fcr, fac, fbc, 1);
            
            // Filter the codes on dropdown change.
            $('.filter').on('change', function(){
                
                rrc = $('#RRC').val();
                ffc = $('#FFC').val();
                ffi = $('#FFI').val();
                fhs = $('#FHS').val();
                fcr = $('#FCR').val();
                fac = $('#FAC').val();
                fbc = $('#FBC').val();
                
                getCodes(utas, rrc, ffc, ffi, fhs, fcr, fac, fbc);
            }); // end if on change.
            
            $('#reset').on('click', function(event){
                event.preventDefault();
                getCodes(utas, null, null, null, null, null, null, null);
            });
            
            /**
             * Get the filtered RCS Failure codes via ajax.
             *
             * @param (string) url
             * @param (string) subassemblyName
             * @param (string) component
             * @param (string) feature
             * @param (string) description
             * @return void
             */
            function getCodes(utas, rrc, ffc, ffi, fhs, fcr, fac, fbc, pageload)
            {
                // IE doesn't support default function values.
                if (pageload === undefined) {
                    pageload = 0;
                }
                
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                
                $.ajax({
                    type: "POST",
                    url: url,
                    data: {
                        utas: utas,
                        rrc: rrc,
                        ffc: ffc,
                        ffi: ffi,
                        fhs: fhs,
                        fcr: fcr,
                        fac: fac,
                        fbc: fbc
                    },
                    success:function(response) {
                        //console.log(response.codes);
                        //console.log(pageload);
                        
                        if (!response.codes.length && pageload) {
                            alert('The current saved failure codes are invalid! Click OK to reset.');
                            getCodes(utas, null, null, null, null, null, null, null);
                        } else {
                            populateSelects(response.codes);
                            setSelected(response.data);
                            setSelectedIfOnlyOneChoice();
                        }
                    }
                });
            }
            
            /**
             * Populate the dropdowns with unique filtered values.
             *
             * @param (object) results
             * @return void
             */
            function populateSelects(results)
            {
                //console.log(results.length);
                //console.log(results);
                
                var $rrc = $("#RRC");
                var $ffc = $("#FFC");
                var $ffi = $("#FFI");
                var $fhs = $("#FHS");
                var $fcr = $("#FCR");
                var $fac = $("#FAC");
                var $fbc = $("#FBC");
                
                // Empty select options.
                $rrc.html('');
                $ffc.html('');
                $ffi.html('');
                $fhs.html('');
                $fcr.html('');
                $fac.html('');
                $fbc.html('');
                
                // Initialise empty arrays.
                var uniqueRRC = [];
                var uniqueFFC = [];
                var uniqueFFI = [];
                var uniqueFHS = [];
                var uniqueFCR = [];
                var uniqueFAC = [];
                var uniqueFBC = [];
                
                // Get unique values and add to arrays.
                $.each(results, function (i, e) {
                    var matchingItems = $.grep(uniqueRRC, function (item) {
                       return item.RRC === e.RRC;
                    });
                    if (matchingItems.length === 0 && e.RRC){
                        uniqueRRC.push(e);
                    }
                    
                    var matchingItems = $.grep(uniqueFFC, function (item) {
                       return item.FFC === e.FFC;
                    });
                    if (matchingItems.length === 0 && e.FFC){
                        uniqueFFC.push(e);
                    }
                    
                    var matchingItems = $.grep(uniqueFFI, function (item) {
                       return item.FFI === e.FFI;
                    });
                    if (matchingItems.length === 0 && e.FFI){
                        uniqueFFI.push(e);
                    }
                    
                    var matchingItems = $.grep(uniqueFHS, function (item) {
                       return item.FHS === e.FHS;
                    });
                    if (matchingItems.length === 0 && e.FHS){
                        uniqueFHS.push(e);
                    }
                    
                    var matchingItems = $.grep(uniqueFCR, function (item) {
                       return item.FCR === e.FCR;
                    });
                    if (matchingItems.length === 0 && e.FCR){
                        uniqueFCR.push(e);
                    }
                    
                    var matchingItems = $.grep(uniqueFAC, function (item) {
                       return item.FAC === e.FAC;
                    });
                    if (matchingItems.length === 0 && e.FAC){
                        uniqueFAC.push(e);
                    }
                    
                    var matchingItems = $.grep(uniqueFBC, function (item) {
                       return item.FBC === e.FBC;
                    });
                    if (matchingItems.length === 0 && e.FBC){
                        uniqueFBC.push(e);
                    }
                });
                
                // Populate SubAssemblyName dropdown.
                $rrc.append($("<option />").val('').text('Please select...'));
                
                $.each(uniqueRRC, function() {
                    $rrc.append($("<option />").val(this.RRC).text(rrcValues[this.RRC]));
                });
                                    
                // Populate SubAssemblyName dropdown.
                $ffc.append($("<option />").val('').text('Please select...'));
                
                $.each(uniqueFFC, function() {
                    $ffc.append($("<option />").val(this.FFC).text(ffcValues[this.FFC]));
                });
                                    
                // Populate SubAssemblyName dropdown.
                $ffi.append($("<option />").val('').text('Please select...'));
                
                $.each(uniqueFFI, function() {
                    $ffi.append($("<option />").val(this.FFI).text(ffiValues[this.FFI]));
                });
                
                // Populate SubAssemblyName dropdown.
                $fhs.append($("<option />").val('').text('Please select...'));
                
                $.each(uniqueFHS, function() {
                    $fhs.append($("<option />").val(this.FHS).text(fhsValues[this.FHS]));
                });
                
                // Populate SubAssemblyName dropdown.
                $fcr.append($("<option />").val('').text('Please select...'));
                
                $.each(uniqueFCR, function() {
                    $fcr.append($("<option />").val(this.FCR).text(fcrValues[this.FCR]));
                });
                
                // Populate SubAssemblyName dropdown.
                $fac.append($("<option />").val('').text('Please select...'));
                
                $.each(uniqueFAC, function() {
                    $fac.append($("<option />").val(this.FAC).text(facValues[this.FAC]));
                });
                
                // Populate SubAssemblyName dropdown.
                $fbc.append($("<option />").val('').text('Please select...'));
                
                $.each(uniqueFBC, function() {
                    $fbc.append($("<option />").val(this.FBC).text(fbcValues[this.FBC]));
                });
            }
            
            /**
             * Set the selected option on each dropdown.
             *
             * @param (object) data
             * @return void
             */
            function setSelected(data)
            {
                //console.log(data);
                
                $("#RRC").val(data.rrc).attr('selected', true);
                $("#FFC").val(data.ffc).attr('selected', true);
                $("#FFI").val(data.ffi).attr('selected', true);
                $("#FHS").val(data.fhs).attr('selected', true);
                $("#FCR").val(data.fcr).attr('selected', true);
                $("#FAC").val(data.fac).attr('selected', true);
                $("#FBC").val(data.fbc).attr('selected', true);
            }
            
            /**
             * If there's only one possible choice in the options list, select it.
             *
             * @return void
             */
            function setSelectedIfOnlyOneChoice()
            {
                if ($('#RRC').children('option').length == 2) {
                    $('#RRC').prop('selectedIndex', 1);
                }
                
                if ($('#FFC').children('option').length == 2) {
                    $('#FFC').prop('selectedIndex', 1);
                }
                
                if ($('#FFI').children('option').length == 2) {
                    $('#FFI').prop('selectedIndex', 1);
                }
                
                if ($('#FHS').children('option').length == 2) {
                    $('#FHS').prop('selectedIndex', 1);
                }
                
                if ($('#FCR').children('option').length == 2) {
                    $('#FCR').prop('selectedIndex', 1);
                }
                
                if ($('#FAC').children('option').length == 2) {
                    $('#FAC').prop('selectedIndex', 1);
                }
                
                if ($('#FBC').children('option').length == 2) {
                    $('#FBC').prop('selectedIndex', 1);
                }
            }
        });
    </script>
@endpush