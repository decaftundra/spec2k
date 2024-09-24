{{ csrf_field() }}

<input type="hidden" name="rcsSFI" id="rcsSFI" value="{{ $notificationId }}">
<input type="hidden" name="plant_code" id="plant_code" value="{{ $plantCode }}">

@if (session()->has('partial_save'))
    <div class="col-sm-12">
        <div class="alert alert-warning partial-save-alert">
            <ul class="list-unstyled">
                <li>{{ strtoupper(session()->get('partial_save')) }}</li>
            </ul>
        </div>
    </div>
@endif

@if ($errors->any())
    <div class="col-sm-12">
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<?php $sourceData = []; ?>

@foreach ($formInputs as $key => $input)
    <?php $sourceData[$key] = $input->get_value($segment); ?>

    @if ($input->is_hidden())
        {!! $input->render($segment, array_key_exists($key, old()) ? old($key) : false) !!}
    @else
        <?php $display = $showAllFields ? 'block' : 'none'; ?>

        <div style="display:{{ $input->get_display() ?: $display }};"
            class="{{ $input->get_display() ?: 'none-essential' }} {{ $input->get_input_width() }}">
            <div class="form-group {{ $errors->has($key) ? 'has-error' : '' }}">
                {!! $input->render_label() !!}
                {!! $input->render($segment, array_key_exists($key, old()) ? old($key) : false) !!}
            </div>
        </div>
    @endif
@endforeach

<!-- Add input that contains all source data to compare with cached partially saved data in middleware -->
<input type="hidden" name="source_data" value="{{ json_encode($sourceData) }}">

<div class="col-sm-12">
    <div class="form-group">
        <button type="submit" class="btn btn-primary">
            Save <i class="far fa-save"></i>
        </button>
        @if ($deleteRoute && is_a($segment, 'App\Segment') && auth()->user()->can('delete', $segment))
            <button id="delete" type="button" class="btn btn-danger">
                Delete <i class="far fa-trash-alt"></i>
            </button>
        @endif
    </div>
</div>

@push('footer-scripts')
    <script>
        $(document).ready(function() {
            $('#delete').on('click', function() {
                swal({
                        title: "Are you sure?",
                        text: "You cannot undo this action.",
                        type: "error",
                        showCancelButton: true,
                        confirmButtonColor: "#c62020",
                        confirmButtonText: "Delete",
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true,
                    },
                    function() {
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });

                        $.ajax({
                            type: "POST",
                            url: "{{ $deleteRoute }}",
                            success: function(result, status, xhr) {
                                console.log(result);
                                console.log(status);
                                console.log(xhr);

                                swal({
                                        title: "Success",
                                        text: "Segment deleted successfully!",
                                        type: "success",
                                        timer: 1500,
                                        showConfirmButton: false
                                    },
                                    function() {
                                        location.reload(true);
                                    });
                            },
                            error: function(xhr, status, error) {
                                console.log(xhr);
                                console.log(status);
                                console.log(error);

                                swal({
                                    title: "Error",
                                    text: "Segment could not be deleted.",
                                    type: "error",
                                });
                            }
                        });
                    });
            });
        });
    </script>
@endpush
