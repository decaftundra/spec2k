<!--Possibly use stacked nav pills here instead of list group???-->

<div style="margin-top:20px;" class="list-group">
    <?php $isMandatory = App\HDR_Segment::isMandatory($notificationId); ?>
    <a style="display:{{ !$isMandatory && !$showAllSegments ? 'none' : 'block' }};" href="{{ route('header.edit', $notificationId) }}" class="list-group-item {{ set_active('*header') ? 'active' : '' }} {{ $isMandatory ? 'mandatory' : 'optional' }}">
        
        @if (is_null(App\HDR_Segment::isValid($notificationId)))
            {{ App\HDR_Segment::isMandatory($notificationId) ? cross() : question() }}
        @else (App\HDR_Segment::isValid($notificationId))
            {{ App\HDR_Segment::isValid($notificationId) ? tick() : cross() }}
        @endif
        
        Header {{ $isMandatory ? asterisk() : '' }}
    </a>
    <?php $isMandatory = App\ShopFindings\AID_Segment::isMandatory($notificationId); ?>
    <!-- preferred segment so display always block -->
    <a style="display:{{ !$isMandatory && !$showAllSegments ? 'block' : 'block'  }};" href="{{ route('airframe-information.edit', $notificationId) }}" class="list-group-item {{ set_active('*airframe-information') ? 'active' : '' }} {{ $isMandatory ? 'mandatory' : 'preferred' }}">
        
        @if (is_null(App\ShopFindings\AID_Segment::isValid($notificationId)))
            {{ App\ShopFindings\AID_Segment::isMandatory($notificationId) ? cross() : question() }}
        @else (App\ShopFindings\AID_Segment::isValid($notificationId))
            {{ App\ShopFindings\AID_Segment::isValid($notificationId) ? tick() : cross() }}
        @endif
        
        Airframe Info {{ $isMandatory ? asterisk() : '' }}
    </a>
    <?php $isMandatory = App\ShopFindings\EID_Segment::isMandatory($notificationId); ?>
    <!-- preferred segment so display always block -->
    <a style="display:{{ !$isMandatory && !$showAllSegments ? 'block' : 'block' }};" href="{{ route('engine-information.edit', $notificationId) }}" class="list-group-item {{ set_active('*engine-information') ? 'active' : '' }} {{ $isMandatory ? 'mandatory' : 'preferred' }}">
        
        @if (is_null(App\ShopFindings\EID_Segment::isValid($notificationId)))
            {{ App\ShopFindings\EID_Segment::isMandatory($notificationId) ? cross() : question() }}
        @else (App\ShopFindings\EID_Segment::isValid($notificationId))
            {{ App\ShopFindings\EID_Segment::isValid($notificationId) ? tick() : cross() }}
        @endif
        
        Engine Info {{ $isMandatory ? asterisk() : '' }}
    </a>
    <?php $isMandatory = App\ShopFindings\API_Segment::isMandatory($notificationId); ?>
    <a style="display:{{ !$isMandatory && !$showAllSegments ? 'none' : 'block' }};" href="{{ route('apu-information.edit', $notificationId) }}" class="list-group-item {{ set_active('*apu-information') ? 'active' : '' }} {{ $isMandatory ? 'mandatory' : 'optional' }}">
        
        @if (is_null(App\ShopFindings\API_Segment::isValid($notificationId)))
            {{ App\ShopFindings\API_Segment::isMandatory($notificationId) ? cross() : question() }}
        @else (App\ShopFindings\API_Segment::isValid($notificationId))
            {{ App\ShopFindings\API_Segment::isValid($notificationId) ? tick() : cross() }}
        @endif
        
        APU Info {{ $isMandatory ? asterisk() : '' }}
    </a>
    <?php $isMandatory = App\ShopFindings\RCS_Segment::isMandatory($notificationId); ?>
    <a style="display:{{ !$isMandatory && !$showAllSegments ? 'none' : 'block' }};" href="{{ route('received-lru.edit', $notificationId) }}" class="list-group-item {{ set_active('*received-lru') ? 'active' : '' }} {{ $isMandatory ? 'mandatory' : 'optional' }}">
        
        @if (is_null(App\ShopFindings\RCS_Segment::isValid($notificationId)))
            {{ App\ShopFindings\RCS_Segment::isMandatory($notificationId) ? cross() : question() }}
        @else (App\ShopFindings\RCS_Segment::isValid($notificationId))
            {{ App\ShopFindings\RCS_Segment::isValid($notificationId) ? tick() : cross() }}
        @endif
        
        Received LRU {{ $isMandatory ? asterisk() : '' }}
    </a>
    <?php $isMandatory = App\ShopFindings\SAS_Segment::isMandatory($notificationId); ?>
    <a style="display:{{ !$isMandatory && !$showAllSegments ? 'none' : 'block' }};" href="{{ route('shop-action-details.edit', $notificationId) }}" class="list-group-item {{ set_active('*shop-action-details') ? 'active' : '' }} {{ $isMandatory ? 'mandatory' : 'optional' }}">
        
        @if (is_null(App\ShopFindings\SAS_Segment::isValid($notificationId)))
            {{ App\ShopFindings\SAS_Segment::isMandatory($notificationId) ? cross() : question() }}
        @else (App\ShopFindings\SAS_Segment::isValid($notificationId))
            {{ App\ShopFindings\SAS_Segment::isValid($notificationId) ? tick() : cross() }}
        @endif
        
        Shop Action {{ $isMandatory ? asterisk() : '' }}
    </a>
    <?php $isMandatory = App\ShopFindings\SUS_Segment::isMandatory($notificationId); ?>
    <a style="display:{{ !$isMandatory && !$showAllSegments ? 'block' : 'block' }};" href="{{ route('shipped-lru.edit', $notificationId) }}" class="list-group-item {{ set_active('*shipped-lru') ? 'active' : '' }} {{ $isMandatory ? 'mandatory' : 'preferred' }}">
        
        @if (is_null(App\ShopFindings\SUS_Segment::isValid($notificationId)))
            {!! App\ShopFindings\SUS_Segment::isMandatory($notificationId) ? cross() : question() !!}
        @else (App\ShopFindings\SUS_Segment::isValid($notificationId))
            {!! App\ShopFindings\SUS_Segment::isValid($notificationId) ? tick() : cross() !!}
        @endif
        
        Shipped LRU {{ $isMandatory ? asterisk() : '' }}
    </a>
    <?php $isMandatory = App\ShopFindings\RLS_Segment::isMandatory($notificationId); ?>
    <a style="display:'block';" href="{{ route('removed-lru.edit', $notificationId) }}" class="list-group-item {{ set_active('*removed-lru') ? 'active' : '' }} preferred"><!-- LJMJun23 MGTSUP-518 -->        
        @if (is_null(App\ShopFindings\RLS_Segment::isValid($notificationId)))
            {{ App\ShopFindings\RLS_Segment::isMandatory($notificationId) ? cross() : question() }}
        @else (App\ShopFindings\RLS_Segment::isValid($notificationId))
            {{ App\ShopFindings\RLS_Segment::isValid($notificationId) ? tick() : cross() }}
        @endif

        Removed LRU {{ $isMandatory ? asterisk() : '' }}
    </a>
    <?php $isMandatory = App\ShopFindings\LNK_Segment::isMandatory($notificationId); ?>
    <a style="display:{{ !$isMandatory && !$showAllSegments ? 'none' : 'block' }};" href="{{ route('linking-field.edit', $notificationId) }}" class="list-group-item {{ set_active('*linking-field') ? 'active' : '' }} {{ $isMandatory ? 'mandatory' : 'optional' }}">
        
        @if (is_null(App\ShopFindings\LNK_Segment::isValid($notificationId)))
            {{ App\ShopFindings\LNK_Segment::isMandatory($notificationId) ? cross() : question() }}
        @else (App\ShopFindings\LNK_Segment::isValid($notificationId))
            {{ App\ShopFindings\LNK_Segment::isValid($notificationId) ? tick() : cross() }}
        @endif
        
        Linking Fields {{ $isMandatory ? asterisk() : '' }}
    </a>
    <?php $isMandatory = App\ShopFindings\ATT_Segment::isMandatory($notificationId); ?>
    <!-- preferred segment so display always block -->
    <a style="display:{{ !$isMandatory && !$showAllSegments ? 'block' : 'block' }};" href="{{ route('accumulated-time-text.edit', $notificationId) }}" class="list-group-item {{ set_active('*accumulated-time-text') ? 'active' : '' }} {{ $isMandatory ? 'mandatory' : 'preferred' }}">
        
        @if (is_null(App\ShopFindings\ATT_Segment::isValid($notificationId)))
            {{ App\ShopFindings\ATT_Segment::isMandatory($notificationId) ? cross() : question() }}
        @else (App\ShopFindings\ATT_Segment::isValid($notificationId))
            {{ App\ShopFindings\ATT_Segment::isValid($notificationId) ? tick() : cross() }}
        @endif
        
        Accum. Time {{ $isMandatory ? asterisk() : '' }}
    </a>
    <?php $isMandatory = App\ShopFindings\SPT_Segment::isMandatory($notificationId); ?>
    <a style="display:{{ !$isMandatory && !$showAllSegments ? 'none' : 'block' }};" href="{{ route('shop-processing-time.edit', $notificationId) }}" class="list-group-item {{ set_active('*shop-processing-time') ? 'active' : '' }} {{ $isMandatory ? 'mandatory' : 'optional' }}">
        
        @if (is_null(App\ShopFindings\SPT_Segment::isValid($notificationId)))
            {{ App\ShopFindings\SPT_Segment::isMandatory($notificationId) ? cross() : question() }}
        @else (App\ShopFindings\SPT_Segment::isValid($notificationId))
            {{ App\ShopFindings\SPT_Segment::isValid($notificationId) ? tick() : cross() }}
        @endif
        
        Shop Proc. {{ $isMandatory ? asterisk() : '' }}
    </a>
    
    @if (App\ShopFindings\Misc_Segment::hasMiscSegment($notificationId))
        <?php $isMandatory = App\ShopFindings\Misc_Segment::isMandatory($notificationId); ?>
        <a style="display:{{ !$isMandatory && !$showAllSegments ? 'none' : 'block' }};" href="{{ route('misc-segment.edit', $notificationId) }}" class="list-group-item {{ set_active('*misc-segment') ? 'active' : '' }} {{ $isMandatory ? 'mandatory' : 'optional' }}">
            
            @if (is_null(App\ShopFindings\Misc_Segment::isValid($notificationId)))
                {{ App\ShopFindings\Misc_Segment::isMandatory($notificationId) ? cross() : question() }}
            @else (App\ShopFindings\Misc_Segment::isValid($notificationId))
                {{ App\ShopFindings\Misc_Segment::isValid($notificationId) ? tick() : cross() }}
            @endif
            
            {{ App\ShopFindings\Misc_Segment::getName($notificationId) }} {{ $isMandatory ? asterisk() : '' }}
        </a>
    @endif
    
    <?php $piecePartsCount = App\PieceParts\PiecePart::countPieceParts($notificationId); ?>
    
    <a href="{{ route('piece-parts.index', $notificationId) }}" class="list-group-item {{ set_active('*piece-parts*') ? 'active' : '' }}">
        {{ App\ShopFindings\ShopFinding::arePiecePartsValid($notificationId) ? tick() : cross() }}
        
        Piece Parts {{ $piecePartsCount ? asterisk() : '' }}
        <span class="badge">{{ $piecePartsCount }}</span>
    </a>
</div>

<div id="segment_toggle" class="checkbox">
    <label>
        <input id="show_all_segments" type="checkbox" value="1" {{ $showAllSegments == 'show' ? 'checked' : '' }}>
        All segments
    </label>
</div>

<div style="display:none;" id="field_toggle" class="checkbox">
    <label>
        <input id="show_all_fields" type="checkbox" value="1" {{ $showAllFields == 'show' ? 'checked' : '' }}>
        All fields
    </label>
</div>

@push('footer-scripts')
    <script>
        if ($('form div.none-essential').length > 0) {
            $('#field_toggle').show(); // Show the checkbox
        }
        
        $('#show_all_fields').on('click', function(){
            
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            if ($(this).is(':checked')) {
                $.ajax({
                  type: "POST",
                  url: "{{ config('app.url') }}" + "/show_all_fields/1"
                });
                
                $('form div.none-essential').show();
                
            } else {
                $.ajax({
                  type: "POST",
                  url: "{{ config('app.url') }}" + "/show_all_fields/0"
                });
                
                $('form div.none-essential').hide();
            }
        });
        
        $('#show_all_segments').on('click', function(){
            
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            if ($(this).is(':checked')) {
                $.ajax({
                  type: "POST",
                  url: "{{ config('app.url') }}" + "/show_all_segments/1"
                });
                
                $('a.optional').show();
                $('a.preferred').show();
                
            } else {
                $.ajax({
                  type: "POST",
                  url: "{{ config('app.url') }}" + "/show_all_segments/0"
                });
                
                $('a.optional').hide();
            }
        });
    </script>
    <script src="{{ asset('js/unsaved.js?v=1.2') }}"></script>
@endpush