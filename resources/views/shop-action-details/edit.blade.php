@extends('layouts.default')

@section('content')
    <div class="row">
        <div class="col-sm-3 col-md-2">
            @include('partials.report-nav')
        </div>
        <div class="col-sm-9 col-md-10">
            
            <h1>Shop Action Details {!! $mandatory ? '<span class="text-danger">*</span>' : '' !!}</h1>
            
            @include('partials.report-header')
            
            <hr>
            
            <div class="row">
                <form method="POST" action="{{ route('shop-action-details.update', $notificationId) }}">
                    @include('partials.form-body')
                </form>
            </div>
            
        </div>
    </div>
    
@endsection

@push('footer-scripts')
    <script>
        
    
        $( document ).ready(function() {
            
            $('button[type="submit"]').after(
            '&nbsp;<button type="submit" class="btn btn-info" id="action-reset" name="reset">Reset Codes <i class="fas fa-sync-alt"></i></button>'
        );
        
        $('#action-reset').on('click', function(event){
                event.preventDefault();
                
                // Get all shop action codes.
                var sacValues = <?php echo json_encode($SAC); ?>;
                
                // Reset radios and select menus.
                $("#RFI-1").prop("checked", false);
                $("#RFI-2").prop("checked", false);
                $('#SAC').prop('selectedIndex', 0);
                $('#PSC').prop('selectedIndex', 0);
                
                populateSelects(sacValues);
            });
            
            // Get all shop action codes.
            var sacValues = <?php echo json_encode($SAC); ?>;
            
            var codes;
            
            // Get initial values and ajax url.
            var url = '{{ route("shop-action-details.get-action-codes") }}';
            var SAC = '{{ old("SAC", $segment->get_SAS_SAC()) }}';
            var RFI = '{{ old("RFI", $segment->get_SAS_RFI()) }}';
            
            // Don't do anything on initial page load
            
            // leave bad input to validation
            
            // on change of RFI radio button
            $('#RFI-1').on('change', function(){
                
                console.log('rfi 1 changed');
                
                el = this;
                RFI = getRFIValue();
                SAC = getSACValue();
                
                if (SAC == null) {
                    
                    console.log('populate all');
                    populateSelects(sacValues);
                }
                
                getCodes(SAC, RFI, el.name);
            });
            
            $('#RFI-2').on('change', function(){
                
                console.log('rfi 2 changed');
                
                el = this;
                RFI = getRFIValue();
                SAC = getSACValue();
                getCodes(SAC, RFI, el.name);
            });
                
            // on change of SAC drop down
            $('#SAC').on('change', function(){
                
                console.log('sac changed');
                
                el = this;
                RFI = getRFIValue();
                SAC = getSACValue();
                getCodes(SAC, RFI, el.name);
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
            function getCodes(SAC, RFI, elementName)
            {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                
                $.ajax({
                    type: "POST",
                    url: url,
                    data: {
                        SAC: SAC,
                        RFI: RFI
                    },
                    success:function(response) {
                        //console.log(response);
                        //console.log('Ajax call:');
                        //console.log(response.codes);
                        
                        if (!response.codes.length) {
                            console.log('bad combination');
                        }
                        
                        console.log(response);
                        
                        // if element is SAC dropdown
                        if (elementName == 'SAC') {
                            
                            // if no codes returned blank radio button selection.
                            if (!response.codes.length) {
                                $("#RFI-1").prop("checked", false);
                                $("#RFI-2").prop("checked", false);
                            }
                            
                        } else { // element is RFI
                            
                            // filter SAC drop down
                            if (!response.codes.length) {
                                // reset drop down selection
                                $('#SAC').prop('selectedIndex', 0);
                                getCodes(null, RFI, 'RFI');
                            } else {
                                populateSelects(response.codes);
                                setSacSelected(response.data);
                            }
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
                //console.log('Populate selects:');
                //console.log(results.length);
                //console.log(results);
                
                var $sac = $("#SAC");
                
                // Empty select options.
                $sac.html('');
                
                // Initialise empty arrays.
                var uniqueSAC = [];
                
                // Get unique values and add to arrays.
                $.each(results, function (i, e) {
                    var matchingItems = $.grep(uniqueSAC, function (item) {
                       return item.SAC === e.SAC;
                    });
                    if (matchingItems.length === 0 && e.SAC){
                        uniqueSAC.push(e);
                    }
                });
                
                // Populate Shop Action Code dropdown.
                $sac.append($("<option />").val('').text('Please select...'));
                
                $.each(uniqueSAC, function() {
                    $sac.append($("<option />").val(this.SAC).text(sacValues[this.SAC]));
                });
            }
            
            /**
             * Set the selected option on each dropdown.
             *
             * @param (object) data
             * @return void
             */
            function setSacSelected(data)
            {
                //console.log('Set selected:');
                //console.log(data);
                
                $("#SAC").val(data.SAC).attr('selected', true);
            }
            
            function getSACValue()
            {
                return $('#SAC').val();
            }
            
            function getRFIValue()
            {
                if ( $('#RFI-1').is(':checked') ) {
                    return true;
                } else if ( $('#RFI-2').is(':checked') ) {
                    return false;
                } else {
                    return null;
                }
            }
        });
    </script>
@endpush