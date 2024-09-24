@extends('layouts.default')

@section('content')
    <div class="row">
        <div class="col-sm-3 col-md-2">
            @include('partials.report-nav')
        </div>
        <div class="col-sm-9 col-md-10">
            
            <h1>{{ $miscSegmentName }} {!! $mandatory ? '<span class="text-danger">*</span>' : '' !!}</h1>
            
            @include('partials.report-header')
            
            <hr>
            
            <form method="POST" action="{{ route('misc-segment.update', $notificationId) }}">
                @include('partials.form-body')
            </form>
            
        </div>
    </div>
@endsection

@if ($profileName == 'UtasProfile')
    @push('footer-scripts')
        <script>
            $( document ).ready(function() {
                
                var partNo = null;
                
                // Add reset button.
                $('button[type="submit"]').after(
                    '&nbsp;<button type="submit" class="btn btn-info" id="reset" name="reset">Reset <i class="fas fa-sync-alt"></i></button>'
                );
                
                var url = '{{ route("misc-segment.getUtasCodes", $notificationId) }}';
                var url2 = '{{ route("misc-segment.getUtasReasonCodes", $notificationId) }}';
                var url3 = '{{ route("misc-segment.getUtasTypeCode", $notificationId) }}';
                var url4 = '{{ route("misc-segment.getUtasPartNo", $notificationId) }}';
                var plant;
                var reason;
                var subassemblyName;
                var component;
                var feature;
                var description;
                
                // Set the variables.
                if (hasOldRequest()) {
                    plant = '{{ old("Plant") }}';
                    reason = '{{ old("Reason") }}';
                    subassemblyName = '{{ old("SubassemblyName") }}';
                    component = '{{ old("Component") }}';
                    feature = '{{ old("FeatureName") }}';
                    description = '{{ old("FailureDescription") }}';
                } else {
                    plant = '{{ $segment->get_MISC_Plant() }}';
                    reason = '{{ $segment->get_MISC_Reason() }}';
                    subassemblyName = '{{ $segment->get_MISC_SubassemblyName() }}';
                    component = '{{ $segment->get_MISC_Component() }}';
                    feature = '{{ $segment->get_MISC_FeatureName() }}';
                    description = '{{ $segment->get_MISC_FailureDescription() }}';
                }
                
                getCodes(url, plant, partNo, subassemblyName, component, feature, description);
                getReasonCodes(url2, plant, reason);
                getTypeCode();
                getPartNo();
                
                // Filter the codes on dropdown change.
                $('.filter').on('change', function(){
                    plant = $('#Plant').val();
                    reason = $('#Reason').val();
                    subassemblyName = $('#SubassemblyName').val();
                    component = $('#Component').val();
                    feature = $('#FeatureName').val();
                    description = $('#FailureDescription').val();
                    
                    getCodes(url, plant, partNo, subassemblyName, component, feature, description);
                    getReasonCodes(url2, plant, reason);
                    getTypeCode();
                    getPartNo();
                }); // end if on change.
                
                $('#reset').on('click', function(event){
                    event.preventDefault();
                    getCodes(url, null, null, null, null, null, null, null);
                    getReasonCodes(url2, null, null, null);
                    getTypeCode();
                    getPartNo();
                });
                
                /**
                 * Has the page been submitted already.
                 *
                 * @return boolean
                 */
                function hasOldRequest()
                {
                    return '{{ empty(old()) }}' ? false : true;
                }
                
                /**
                 * Get the filtered Utas codes via ajax.
                 *
                 * @param (string) url
                 * @param (string) subassemblyName
                 * @param (string) component
                 * @param (string) feature
                 * @param (string) description
                 * @return void
                 */
                function getCodes(url, plant, partNo, subassemblyName, component, feature, description)
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
                            plant: plant,
                            partNo: partNo,
                            subassemblyName: subassemblyName,
                            component: component,
                            feature: feature,
                            description: description
                        },
                        success:function(response) {
                            console.log(response.codes);
                            
                            populateSelects(response.codes);
                            setSelected(response.data);
                        }
                    });
                }
                
                /**
                 * Get the filtered Utas codes via ajax.
                 *
                 * @param (string) url
                 * @param (string) subassemblyName
                 * @param (string) component
                 * @param (string) feature
                 * @param (string) description
                 * @return void
                 */
                function getReasonCodes(url2, plant, reason)
                {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    
                    $.ajax({
                        type: "POST",
                        url: url2,
                        data: {
                            plant: plant,
                            reason: reason
                        },
                        success:function(response) {
                            //console.log(response.codes);
                            
                            populateReasonSelect(response.codes);
                            setReasonSelected(response.data);
                        }
                    });
                }
                
                /**
                 * Get the Utas type code via ajax.
                 *
                 * @return void
                 */
                function getTypeCode()
                {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    
                    $.ajax({
                        type: "POST",
                        url: url3,
                        success:function(response) {
                            //console.log(response.type);
                            
                            $('#Type').val(response.type);
                        }
                    });
                }
                
                /**
                 * Get the Utas part no via ajax.
                 *
                 * @return void
                 */
                function getPartNo()
                {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    
                    $.ajax({
                        type: "POST",
                        url: url4,
                        success:function(response) {
                            console.log(response.partNo);
                            
                            $('#PartNo').val(response.partNo);
                        }
                    });
                }
                
                /**
                 * Populate the dropdowns with unique filtered values.
                 *
                 * @param (object) results
                 * @return void
                 */
                function populateReasonSelect(results)
                {
                    //console.log(results.length);
                    //console.log(results);
                    
                    var $reason = $("#Reason");
                    
                    // Empty select options.
                    $reason.html('');
                    
                    // Initialise empty arrays.
                    var uniqueREASON = [];
                    
                    // Get unique values and add to arrays.
                    $.each(results, function (i, e) {
                        var matchingItems = $.grep(uniqueREASON, function (item) {
                           return item.REASON === e.REASON;
                        });
                        if (matchingItems.length === 0 && e.REASON){
                            uniqueREASON.push(e);
                        }
                    });
                    
                    // Populate SubAssemblyName dropdown.
                    $reason.append($("<option />").val('').text('Please select...'));
                    
                    $.each(uniqueREASON, function() {
                        $reason.append($("<option />").val(this.REASON).text(this.REASON));
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
                    
                    var $SubassemblyName = $("#SubassemblyName");
                    var $Component = $('#Component');
                    var $FeatureName = $('#FeatureName');
                    var $FailureDescription = $('#FailureDescription');
                    
                    // Empty select options.
                    $SubassemblyName.html('');
                    $Component.html('');
                    $FeatureName.html('');
                    $FailureDescription.html('');
                    
                    // Initialise empty arrays.
                    var uniqueSUB = [];
                    var uniqueCOMP = [];
                    var uniqueFEAT = [];
                    var uniqueDESCR = [];
                    
                    // Get unique values and add to arrays.
                    $.each(results, function (i, e) {
                        var matchingItems = $.grep(uniqueSUB, function (item) {
                           return item.SUB === e.SUB;
                        });
                        if (matchingItems.length === 0 && e.SUB){
                            uniqueSUB.push(e);
                        }
                        
                        var matchingItems = $.grep(uniqueCOMP, function (item) {
                           return item.COMP === e.COMP;
                        });
                        if (matchingItems.length === 0 && e.COMP){
                            uniqueCOMP.push(e);
                        }
                        
                        var matchingItems = $.grep(uniqueFEAT, function (item) {
                           return item.FEAT === e.FEAT;
                        });
                        if (matchingItems.length === 0 && e.FEAT){
                            uniqueFEAT.push(e);
                        }
                        
                        var matchingItems = $.grep(uniqueDESCR, function (item) {
                           return item.DESCR === e.DESCR;
                        });
                        if (matchingItems.length === 0 && e.DESCR){
                            uniqueDESCR.push(e);
                        }
                    });
                    
                    // Populate SubAssemblyName dropdown.
                    $SubassemblyName.append($("<option />").val('').text('Please select...'));
                    
                    $.each(uniqueSUB, function() {
                        $SubassemblyName.append($("<option />").val(this.SUB).text(this.SUB));
                    });
                    
                    // Populate Component dropdown.
                    $Component.append($("<option />").val('').text('Please select...'));
                    
                    $.each(uniqueCOMP, function() {
                        $Component.append($("<option />").val(this.COMP).text(this.COMP));
                    });
                    
                    // Populate FeatureName dropdown.
                    $FeatureName.append($("<option />").val('').text('Please select...'));
                    
                    $.each(uniqueFEAT, function() {
                        $FeatureName.append($("<option />").val(this.FEAT).text(this.FEAT));
                    });
                    
                    // Populate FailureDescription dropdown.
                    $FailureDescription.append($("<option />").val('').text('Please select...'));
                    
                    $.each(uniqueDESCR, function() {
                        $FailureDescription.append($("<option />").val(this.DESCR).text(this.DESCR));
                    });
                }
                
                /**
                 * Set the selected option on each dropdown.
                 *
                 * @param (object) data
                 * @return void
                 */
                function setReasonSelected(data)
                {
                    //console.log(data);
                    
                    $("#Reason").val(data.reason).attr('selected', true);
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
                    
                    $("#SubassemblyName").val(data.subassemblyName).attr('selected', true);
                    $("#Component").val(data.component).attr('selected', true);
                    $("#FeatureName").val(data.feature).attr('selected', true);
                    $("#FailureDescription").val(data.description).attr('selected', true);
                }
            });
        </script>
    @endpush
@endif