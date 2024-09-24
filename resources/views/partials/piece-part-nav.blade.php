<ul class="nav nav-tabs nav-justified">
    <li role="presentation" class="{{ set_active('*worked-piece-part*') ? 'active' : '' }}">
        <a href="{{ route('worked-piece-part.edit', [$notificationId, $piecePartDetailId]) }}">
            Worked Piece Part {{ App\PieceParts\WPS_Segment::isMandatory($piecePartDetailId) ? asterisk() : '' }}
        </a>
    </li>
    <li role="presentation" class="{{ !$piecePartDetailId ? 'disabled' : '' }} {{ set_active('*next-higher-assembly*') ? 'active' : '' }}">
        <a href="{{ route('next-higher-assembly.edit', [$notificationId, $piecePartDetailId]) }}">
            Next Higher Assembly {{ App\PieceParts\NHS_Segment::isMandatory($piecePartDetailId) ? asterisk() : '' }}
        </a>
    </li>
    <li role="presentation" class="{{ !$piecePartDetailId ? 'disabled' : '' }} {{ set_active('*replaced-piece-part*') ? 'active' : '' }}">
        <a href="{{ route('replaced-piece-part.edit', [$notificationId, $piecePartDetailId]) }}">
            Replaced Piece Part {{ App\PieceParts\RPS_Segment::isMandatory($piecePartDetailId) ? asterisk() : '' }}
        </a>
    </li>
</ul>