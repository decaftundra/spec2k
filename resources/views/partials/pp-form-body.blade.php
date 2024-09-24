@foreach ($piecePartDetail['wps_inputs'] as $key => $attributes)
    
    <?php
    $idAndName = $piecePartDetail['id'].'['.$key.']';
    $attributes['key'] = $idAndName;
    ?>
    
    @if ($key != 'PFC')
        
        <?php
        $attributes['input_type'] = 'hidden';
        $input = new \App\Spec2kInput($attributes);
        ?>
        
        <?php $old = old($piecePartDetail['id'].'.'.$key) ?? false; ?>
        
        {!! $input->render($piecePartDetail['WPS_Segment'], $old) !!}
    @else
        
        <?php
        $attributes['input_type'] = 'radio-pp';
        $input = new \App\Spec2kInput($attributes);
        ?>
        
        <!-- CURRENTLY ONLY RADIO INPUTS ARE SUPPORTED -->
        <div class="form-group form-group-sm {{ $errors->has($piecePartDetail['id'].'.'.$key) ? 'has-error' : '' }}">
            {!! $input->render($piecePartDetail['WPS_Segment'], $old) !!}
        </div>
    @endif
@endforeach