@extends('layouts.default')

@section('content')
    <div class="row">
        <div class="col-sm-3 col-md-2">
            @include('partials.report-nav')
        </div>
        <div class="col-sm-9 col-md-10">
            <h1>Piece Parts</h1>
            
            @include('partials.report-header')
            
            <p>{{ count($piecePartDetails) }} piece parts found.<p>
            
            @if ($piecePartDetails && count($piecePartDetails))
                
                @if ($warning)
                    <div class="alert alert-warning" role="alert">
                        <i class="fas fa-exclamation-triangle"></i>
                        {{ $warning }}
                    </div>
                @endif
                
                @if (session()->has('errorsArray'))
                        <div class="alert alert-danger">
                            <ul>
                                <li>Please check individual piece parts indicated below.</li>
                            </ul>
                        </div>
                @endif
                
                <form style="padding-bottom:20px;" class="form-inline" method="POST" action="{{ route('piece-parts.update', $notificationId) }}">
                
                    {{ csrf_field() }}
                    
                    <div class="table-responsive">
                        <table class="table table-hover" style="white-space:nowrap;font-size:12px;">
                            <tr>
                                <th><br>Piece Part Record ID</th>
                                <th><br>Part No.</th>
                                <th><br>Description</th>
                                <th>
                                    (Failure ID)<br>
                                    D&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;N&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Y
                                </th>
                                <th><br>Edit WPS {{ App\PieceParts\WPS_Segment::isMandatory($piecePartDetails->first()['id']) ? asterisk() : '' }}</th>
                                <th><br>Edit NHS {{ App\PieceParts\NHS_Segment::isMandatory($piecePartDetails->first()['id']) ? asterisk() : '' }}</th>
                                <th><br>Edit RPS {{ App\PieceParts\RPS_Segment::isMandatory($piecePartDetails->first()['id']) ? asterisk() : '' }}</th>
                            </tr>
                            
                            @foreach ($piecePartDetails as $ppi => $piecePartDetail)
                                <tr bgcolor="{{ session()->has('errorsArray.'.$ppi) ? '#f2dede' : '' }}">
                                    <td>{{ $piecePartDetail['WPS_Segment']->get_WPS_PPI() }}</td>
                                    <td>{{ $piecePartDetail['WPS_Segment']->get_WPS_MPN() }}</td>
                                    <td>{{ $piecePartDetail['WPS_Segment']->get_WPS_PDT() }}</td>
                                    <td>
                                        @include('partials.pp-form-body')
                                    </td>
                                    <td>
                                        @if (is_null(App\PieceParts\WPS_Segment::isValid($piecePartDetail['id'])))
                                            {{ App\PieceParts\WPS_Segment::isMandatory($piecePartDetail['id']) ? cross() : question() }}
                                        @else (App\PieceParts\WPS_Segment::isValid($piecePartDetail['id']))
                                            {{ App\PieceParts\WPS_Segment::isValid($piecePartDetail['id']) ? tick() : cross() }}
                                        @endif
                                        
                                        <a href="{{ route('worked-piece-part.edit', [$notificationId, $piecePartDetail['id']]) }}"
                                           class="btn btn-xs btn-primary"
                                        >
                                            <i class="fas fa-pencil-alt"></i> WPS
                                        </a>
                                    </td>
                                    <td>
                                        
                                        @if (is_null(App\PieceParts\NHS_Segment::isValid($piecePartDetail['id'])))
                                            {{ App\PieceParts\NHS_Segment::isMandatory($piecePartDetail['id']) ? cross() : question() }}
                                        @else (App\PieceParts\NHS_Segment::isValid($piecePartDetail['id']))
                                            {{ App\PieceParts\NHS_Segment::isValid($piecePartDetail['id']) ? tick() : cross() }}
                                        @endif
                                        
                                        <a href="{{ route('next-higher-assembly.edit', [$notificationId, $piecePartDetail['id']]) }}"
                                           class="btn btn-xs btn-primary"
                                        >
                                            <i class="fas fa-pencil-alt"></i> NHS
                                        </a>
                                    </td>
                                    <td>
                                        
                                        @if (is_null(App\PieceParts\RPS_Segment::isValid($piecePartDetail['id'])))
                                            {{ App\PieceParts\RPS_Segment::isMandatory($piecePartDetail['id']) ? cross() : question() }}
                                        @else (App\PieceParts\RPS_Segment::isValid($piecePartDetail['id']))
                                            {{ App\PieceParts\RPS_Segment::isValid($piecePartDetail['id']) ? tick() : cross() }}
                                        @endif
                                        
                                        <a href="{{ route('replaced-piece-part.edit', [$notificationId, $piecePartDetail['id']]) }}"
                                           class="btn btn-xs btn-primary"
                                        >
                                            <i class="fas fa-pencil-alt"></i> RPS
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            Save All <i class="far fa-save"></i>
                        </button>
                    </div>
                
                </form>
            @else
                <p>No piece parts found.</p>
            @endif
        </div>
    </div>
@endsection