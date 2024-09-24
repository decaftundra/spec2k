<tr>
    <td>{{ ucfirst($activity->name) }} {{ $activity->getSubjectName() }}</td>
    
    @if ($activity->name == 'deleted')
        <td>
            <i class="fas fa-trash-alt"></i> Unavailable
        </td>
        
        <td>
            @if ($activity->notification_id)
                {{ $activity->notification_id }}
            @elseif ($activity->shop_finding_id)
                {{ $activity->shop_finding_id }}
            @else
                -
            @endif
        </td>
    @else
        <td>
            @if ($activity->getSubject())
                <i class="fas fa-external-link-alt"></i>
                <a rel="noopener" target="_blank" href="{{ $activity->getSubject()->getActivityUrl() }}">
                    {{ $activity->getSubject()->getActivityUrlTitle() }}
                </a>
            @else
                <i class="fas fa-trash-alt"></i> Unavailable
            @endif
        </td>
        
        <td>
            @if ($activity->notification_id)
                {{ $activity->notification_id }}
            @elseif ($activity->shop_finding_id)
                {{ $activity->shop_finding_id }}
            @else
                -
            @endif
        </td>
    @endif
    
    <td>{{ $activity->created_at->setTimezone($timezone)->format('d/m/Y H:i:s') }}</td>
</tr>