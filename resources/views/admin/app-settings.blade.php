@extends('layouts.default')

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <h1>Application Settings</h1>
            
            <h3>
                Change Codes
                <span>
        	        <a href="{{ route('change-codes.create') }}" type="button" class="btn btn-sm btn-primary navbar-btn">
            	        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Change Code
            	    </a>
                </span>
            </h3>
            
            @if (count($changeCodes))
                <div class="table-responsive">
                    <table class="table table-condensed">
                        <tr>
                            <th>Name</th>
                            <th colspan="2">Code</th>
                        </tr>
                        
                        @foreach ($changeCodes as $changeCode)
                            <tr>
                                <td>{{ $changeCode->name }}</td>
                                <td><strong>{{ $changeCode->code }}</strong></td>
                                <td align="right">
                                    <a href="{{ route('change-codes.edit', $changeCode->id) }}" class="btn btn-sm btn-warning">
                                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Edit
                                    </a>
                                    <a href="{{ route('change-codes.delete', $changeCode->id) }}" class="btn btn-sm btn-danger">
                                        <span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Delete
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            @else
                <p>No Change Codes could be found.</p>
            @endif
            
            <hr>
            
            <h3>
                Supplier Removal Type Codes
                <span>
        	        <a href="{{ route('supplier-removal-type-codes.create') }}" type="button" class="btn btn-sm btn-primary navbar-btn">
            	        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Supplier Removal Type Code
            	    </a>
                </span>
            </h3>
            
            @if (count($supplierRemovalTypeCodes))
                <div class="table-responsive">
                    <table class="table table-condensed">
                        <tr>
                            <th>Name</th>
                            <th colspan="2">Code</th>
                        </tr>
                        
                        @foreach ($supplierRemovalTypeCodes as $supplierRemovalTypeCode)
                            <tr>
                                <td>{{ $supplierRemovalTypeCode->name }}</td>
                                <td><strong>{{ $supplierRemovalTypeCode->code }}</strong></td>
                                <td align="right">
                                    <a href="{{ route('supplier-removal-type-codes.edit', $supplierRemovalTypeCode->id) }}" class="btn btn-sm btn-warning">
                                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Edit
                                    </a>
                                    <a href="{{ route('supplier-removal-type-codes.delete', $supplierRemovalTypeCode->id) }}" class="btn btn-sm btn-danger">
                                        <span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Delete
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            @else
                <p>No Supplier Removal Type Codes could be found.</p>
            @endif
            
            <hr>
            
            <h3>
                Fault/Failure Found Codes
                <span>
        	        <a href="{{ route('fault-found-codes.create') }}" type="button" class="btn btn-sm btn-primary navbar-btn">
            	        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Fault Found Code
            	    </a>
                </span>
            </h3>
            
            @if (count($faultFoundCodes))
                <div class="table-responsive">
                    <table class="table table-condensed">
                        <tr>
                            <th>Name</th>
                            <th colspan="2">Code</th>
                        </tr>
                        
                        @foreach ($faultFoundCodes as $faultFoundCode)
                            <tr>
                                <td>{{ $faultFoundCode->name }}</td>
                                <td><strong>{{ $faultFoundCode->code }}</strong></td>
                                <td align="right">
                                    <a href="{{ route('fault-found-codes.edit', $faultFoundCode->id) }}" class="btn btn-sm btn-warning">
                                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Edit
                                    </a>
                                    <a href="{{ route('fault-found-codes.delete', $faultFoundCode->id) }}" class="btn btn-sm btn-danger">
                                        <span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Delete
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            @else
                <p>No Fault Found Codes could be found.</p>
            @endif
            
            <hr>
            
            <h3>
                Fault/Failure Induced Codes
                <span>
        	        <a href="{{ route('fault-induced-codes.create') }}" type="button" class="btn btn-sm btn-primary navbar-btn">
            	        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Fault Induced Code
            	    </a>
                </span>
            </h3>
            
            @if (count($faultInducedCodes))
                <div class="table-responsive">
                    <table class="table table-condensed">
                        <tr>
                            <th>Name</th>
                            <th colspan="2">Code</th>
                        </tr>
                        
                        @foreach ($faultInducedCodes as $faultInducedCode)
                            <tr>
                                <td>{{ $faultInducedCode->name }}</td>
                                <td><strong>{{ $faultInducedCode->code }}</strong></td>
                                <td align="right">
                                    <a href="{{ route('fault-induced-codes.edit', $faultInducedCode->id) }}" class="btn btn-sm btn-warning">
                                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Edit
                                    </a>
                                    <a href="{{ route('fault-induced-codes.delete', $faultInducedCode->id) }}" class="btn btn-sm btn-danger">
                                        <span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Delete
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            @else
                <p>No Fault Induced Codes could be found.</p>
            @endif
            
            <hr>
            
            <h3>
                Fault/Failure Confirms Reason For Removal Codes
                <span>
        	        <a href="{{ route('fault-confirms-rfr-codes.create') }}" type="button" class="btn btn-sm btn-primary navbar-btn">
            	        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Fault Confirms Reason For Removal Code
            	    </a>
                </span>
            </h3>
            
            @if (count($faultConfirmsReasonForRemovalCodes))
                <div class="table-responsive">
                    <table class="table table-condensed">
                        <tr>
                            <th>Name</th>
                            <th colspan="2">Code</th>
                        </tr>
                        
                        @foreach ($faultConfirmsReasonForRemovalCodes as $faultConfirmsReasonForRemovalCode)
                            <tr>
                                <td>{{ $faultConfirmsReasonForRemovalCode->name }}</td>
                                <td><strong>{{ $faultConfirmsReasonForRemovalCode->code }}</strong></td>
                                <td align="right">
                                    <a href="{{ route('fault-confirms-rfr-codes.edit', $faultConfirmsReasonForRemovalCode->id) }}" class="btn btn-sm btn-warning">
                                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Edit
                                    </a>
                                    <a href="{{ route('fault-confirms-rfr-codes.delete', $faultConfirmsReasonForRemovalCode->id) }}" class="btn btn-sm btn-danger">
                                        <span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Delete
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            @else
                <p>No Fault Confirms Reason For Removal Codes could be found.</p>
            @endif
            
            <hr>
            
            <h3>
                Fault/Failure Confirms Aircraft Message Codes
                <span>
        	        <a href="{{ route('fault-confirms-am-codes.create') }}" type="button" class="btn btn-sm btn-primary navbar-btn">
            	        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Fault/Failure Confirms Aircraft Message Code
            	    </a>
                </span>
            </h3>
            
            @if (count($faultConfirmsAircraftMessageCodes))
                <div class="table-responsive">
                    <table class="table table-condensed">
                        <tr>
                            <th>Name</th>
                            <th colspan="2">Code</th>
                        </tr>
                        
                        @foreach ($faultConfirmsAircraftMessageCodes as $faultConfirmsAircraftMessageCode)
                            <tr>
                                <td>{{ $faultConfirmsAircraftMessageCode->name }}</td>
                                <td><strong>{{ $faultConfirmsAircraftMessageCode->code }}</strong></td>
                                <td align="right">
                                    <a href="{{ route('fault-confirms-am-codes.edit', $faultConfirmsAircraftMessageCode->id) }}" class="btn btn-sm btn-warning">
                                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Edit
                                    </a>
                                    <a href="{{ route('fault-confirms-am-codes.delete', $faultConfirmsAircraftMessageCode->id) }}" class="btn btn-sm btn-danger">
                                        <span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Delete
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            @else
                <p>No Fault/Failure Confirms Aircraft Message Codes could be found.</p>
            @endif
            
            <hr>
            
            <h3>
                Fault/Failure Confirms Aircraft Part Bite Message Codes
                <span>
        	        <a href="{{ route('fault-confirms-apbm-codes.create') }}" type="button" class="btn btn-sm btn-primary navbar-btn">
            	        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Fault/Failure Confirms Aircraft Part Bite Message Code
            	    </a>
                </span>
            </h3>
            
            @if (count($faultConfirmsAircraftPartBiteMessageCodes))
                <div class="table-responsive">
                    <table class="table table-condensed">
                        <tr>
                            <th>Name</th>
                            <th colspan="2">Code</th>
                        </tr>
                        
                        @foreach ($faultConfirmsAircraftPartBiteMessageCodes as $faultConfirmsAircraftPartBiteMessageCode)
                            <tr>
                                <td>{{ $faultConfirmsAircraftPartBiteMessageCode->name }}</td>
                                <td><strong>{{ $faultConfirmsAircraftPartBiteMessageCode->code }}</strong></td>
                                <td align="right">
                                    <a href="{{ route('fault-confirms-apbm-codes.edit', $faultConfirmsAircraftPartBiteMessageCode->id) }}" class="btn btn-sm btn-warning">
                                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Edit
                                    </a>
                                    <a href="{{ route('fault-confirms-apbm-codes.delete', $faultConfirmsAircraftPartBiteMessageCode->id) }}" class="btn btn-sm btn-danger">
                                        <span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Delete
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            @else
                <p>No Fault/Failure Confirms Aircraft Part Bite Message Codes could be found.</p>
            @endif
            
            <hr>
            
            <h3>
                Hardware/Software Failure Codes
                <span>
        	        <a href="{{ route('hardware-software-failure-codes.create') }}" type="button" class="btn btn-sm btn-primary navbar-btn">
            	        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Hardware/Software Failure Code
            	    </a>
                </span>
            </h3>
            
            @if (count($hardwareSoftwareFailureCodes))
                <div class="table-responsive">
                    <table class="table table-condensed">
                        <tr>
                            <th>Name</th>
                            <th colspan="2">Code</th>
                        </tr>
                        
                        @foreach ($hardwareSoftwareFailureCodes as $hardwareSoftwareFailureCode)
                            <tr>
                                <td>{{ $hardwareSoftwareFailureCode->name }}</td>
                                <td><strong>{{ $hardwareSoftwareFailureCode->code }}</strong></td>
                                <td align="right">
                                    <a href="{{ route('hardware-software-failure-codes.edit', $hardwareSoftwareFailureCode->id) }}" class="btn btn-sm btn-warning">
                                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Edit
                                    </a>
                                    <a href="{{ route('hardware-software-failure-codes.delete', $hardwareSoftwareFailureCode->id) }}" class="btn btn-sm btn-danger">
                                        <span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Delete
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            @else
                <p>No Hardware/Software Failure Codes could be found.</p>
            @endif
            
            <hr>
            
            <h3>
                Location Codes
                <span>
        	        <a href="{{ route('location-codes.create') }}" type="button" class="btn btn-sm btn-primary navbar-btn">
            	        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Location Code
            	    </a>
                </span>
            </h3>
            
            @if (count($locationCodes))
                <div class="table-responsive">
                    <table class="table table-condensed">
                        <tr>
                            <th>Name</th>
                            <th colspan="2">Code</th>
                        </tr>
                        
                        @foreach ($locationCodes as $locationCode)
                            <tr>
                                <td>{{ $locationCode->name }}</td>
                                <td><strong>{{ $locationCode->code }}</strong></td>
                                <td align="right">
                                    <a href="{{ route('location-codes.edit', $locationCode->id) }}" class="btn btn-sm btn-warning">
                                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Edit
                                    </a>
                                    <a href="{{ route('location-codes.delete', $locationCode->id) }}" class="btn btn-sm btn-danger">
                                        <span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Delete
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            @else
                <p>No Location Codes could be found.</p>
            @endif
            
            <h3>
                Final Indicator Codes
                <span>
        	        <a href="{{ route('final-indicator-codes.create') }}" type="button" class="btn btn-sm btn-primary navbar-btn">
            	        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Final Indicator Code
            	    </a>
                </span>
            </h3>
            
            <hr>
            
            @if (count($finalIndicatorCodes))
                <div class="table-responsive">
                    <table class="table table-condensed">
                        <tr>
                            <th>Name</th>
                            <th colspan="2">Code</th>
                        </tr>
                        
                        @foreach ($finalIndicatorCodes as $finalIndicatorCode)
                            <tr>
                                <td>{{ $finalIndicatorCode->name }}</td>
                                <td><strong>{{ $finalIndicatorCode->code }}</strong></td>
                                <td align="right">
                                    <a href="{{ route('final-indicator-codes.edit', $finalIndicatorCode->id) }}" class="btn btn-sm btn-warning">
                                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Edit
                                    </a>
                                    <a href="{{ route('final-indicator-codes.delete', $finalIndicatorCode->id) }}" class="btn btn-sm btn-danger">
                                        <span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Delete
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            @else
                <p>No Final Indicator Codes could be found.</p>
            @endif
            
            <hr>
            
            <h3>
                Shop Action Codes
                <span>
        	        <a href="{{ route('shop-action-codes.create') }}" type="button" class="btn btn-sm btn-primary navbar-btn">
            	        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Shop Action Code
            	    </a>
                </span>
            </h3>
            
            @if (count($shopActionCodes))
                <div class="table-responsive">
                    <table class="table table-condensed">
                        <tr>
                            <th>Name</th>
                            <th colspan="2">Code</th>
                        </tr>
                        
                        @foreach ($shopActionCodes as $shopActionCode)
                            <tr>
                                <td>{{ $shopActionCode->name }}</td>
                                <td><strong>{{ $shopActionCode->code }}</strong></td>
                                <td align="right">
                                    <a href="{{ route('shop-action-codes.edit', $shopActionCode->id) }}" class="btn btn-sm btn-warning">
                                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Edit
                                    </a>
                                    <a href="{{ route('shop-action-codes.delete', $shopActionCode->id) }}" class="btn btn-sm btn-danger">
                                        <span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Delete
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            @else
                <p>No Shop Action Codes could be found.</p>
            @endif
            
            <hr>
            
            <h3>
                Time/Cycle Reference Codes
                <span>
        	        <a href="{{ route('time-cycle-reference-codes.create') }}" type="button" class="btn btn-sm btn-primary navbar-btn">
            	        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Time/Cycle Reference Code
            	    </a>
                </span>
            </h3>
            
            @if (count($timeCycleReferenceCodes))
                <div class="table-responsive">
                    <table class="table table-condensed">
                        <tr>
                            <th>Name</th>
                            <th colspan="2">Code</th>
                        </tr>
                        
                        @foreach ($timeCycleReferenceCodes as $timeCycleReferenceCode)
                            <tr>
                                <td>{{ $timeCycleReferenceCode->name }}</td>
                                <td><strong>{{ $timeCycleReferenceCode->code }}</strong></td>
                                <td align="right">
                                    <a href="{{ route('time-cycle-reference-codes.edit', $timeCycleReferenceCode->id) }}" class="btn btn-sm btn-warning">
                                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Edit
                                    </a>
                                    <a href="{{ route('time-cycle-reference-codes.delete', $timeCycleReferenceCode->id) }}" class="btn btn-sm btn-danger">
                                        <span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Delete
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            @else
                <p>No Time/Cycle Reference Codes could be found.</p>
            @endif
            
            <hr>
        </div>
    </div>
@endsection