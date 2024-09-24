@extends('layouts.default')

@section('content')
    <div class="row">
        <div class="col-sm-3 col-md-2">
            @include('partials.report-nav')
        </div>
        <div class="col-sm-9 col-md-10">
            
            <h1>Engine Information {!! $mandatory ? '<span class="text-danger">*</span>' : '' !!}</h1>
            
            @include('partials.report-header')
            
            <hr>
            
            <form method="POST" action="{{ route('engine-information.update', $notificationId) }}">
                @include('partials.form-body')
            </form>
            
        </div>
    </div>



<style>
    /* MGTSUP-373 we need to try and override some of the styling after it builds the client HTML to bypass some Laravel and Bootstrap inconsistencies */
    .ljmFLOATFixClear{float:none !important; clear:both;}
    .ljmFLOAT{float:left !important; height:100px;}
    .ljmFLOAT + .ljmFLOATFixClear{clear:unset; float:right !important; height:100px;}
</style>


    <script type="text/javascript">

        const arrEngineTypes = [];
        const arrEngineModels = [];
        const arrEngineManufacturerCodes = [];

        var szSelectedEngineModel = '';  

        document.addEventListener("DOMContentLoaded", () =>
        {

            // LJM because laravel layout is a nightmare then hardcoding some rules in here:
            //        $("#AET").parent().parent().css('float', 'left');
            //$("#AEM").parent().parent().css('float', 'left');
//            $("#AEM").parent().parent().html('<div><br/></div>' + $("#AEM").parent().parent().html());

//            $("<div>Test2</div>").insertBefore($("#AEM").parent().parent());



            // LJM for some reason there is an issue with Laravel coming out with the selected value so run a pre check.
            // Engine Type
            if (($("#AET").find(":selected").val() == '') && ($("#AETO").val() != '')) {
                // this means that there is an OTHER value but we need to double check if it should be in the SELECTED drop down.
                $("#AET > option").each(function () {
                    if (this.value == $("#AETO").val()) {
                        $("#AET").val($("#AETO").val()).prop('selected', true);
                        $("#AETO").val(''); // blank the OTHER value off
                    }
                });
            }

            // Engine Model
            if (($("#AEM").find(":selected").val() == '') && ($("#AEMO").val() != '')) {
                // this means that there is an OTHER value but we need to double check if it should be in the SELECTED drop down.
                $("#AEM > option").each(function () {
                    if (this.value == $("#AEMO").val()) {
                        $("#AEM").val($("#AEMO").val()).prop('selected', true);
                        $("#AEMO").val(''); // blank the OTHER value off
                    }
                });
            }



            szSelectedEngineModel = $("#AEM").find(":selected").val();

            // populate the lookup table from the drop down.
            var arrSplitEngineDetails = $("#LJMFILTERINFO").val().split("|");

            for (i = 0; i < arrSplitEngineDetails.length; i++)
            {
                arrEngineTypes[i] = arrSplitEngineDetails[i].split(":")[0];
                arrEngineModels[i] = arrSplitEngineDetails[i].split(":")[1];
                arrEngineManufacturerCodes[i] = arrSplitEngineDetails[i].split(":")[2];
            }




            if ($("#AET").val() == "") {
                $(".AETO_container").show();
            } else {
                $(".AETO_container").hide();
            }

            if ($("#AEM").val() == "") {
                $(".AEMO_container").show();
            } else {
                $(".AEMO_container").hide();
            }
            FilterDownModels();




            $("#AET").on("change", function () {
                if ($("#AET").val() == "") {
                    $(".AETO_container").show();
                } else {
                    $(".AETO_container").hide();
                }

                // reset the manufacturer code
                $('#MFR').val('');

                $(".AEMO_container").show();
                FilterDownModels();
            });

            $("#AEM").on("change", function () {
                if ($("#AEM").val() == "") {
                    $(".AEMO_container").show();
                } else {
                    $(".AEMO_container").hide();
                }

                // reset the manufacturer code
                $('#MFR').val('');

                PresetManufacturerCode();
            });


        });


        function FilterDownModels() {
            // LJMMar23 now filter down the models
            $('#AEM')
                .empty()
                .append('<option value="">Empty - Please Select or type in the Other text box.</option>')
            ;


            for (var i = 0; i < arrEngineModels.length; i++)
            {
                if ($("#AET").val() != '')
                {
                    if (arrEngineTypes[i] == $("#AET").val())
                    {
                        if (szSelectedEngineModel == arrEngineModels[i]) {
                            $('#AEM').append('<option value="' + arrEngineModels[i] + '" selected>' + arrEngineModels[i] + '</option>');
                        }
                        else {
                            $('#AEM').append('<option value="' + arrEngineModels[i] + '">' + arrEngineModels[i] + '</option>');
                        }

                    }
                }
            }
        }


        function PresetManufacturerCode() {
            for (var i = 0; i < arrEngineModels.length; i++)
            {
                if ($("#AEM").val() != '')
                {
                    if (arrEngineModels[i] == $("#AEM").val())
                    {
                        $('#MFR').val(arrEngineManufacturerCodes[i]);
                    }
                }
            }
        }



    </script>


@endsection