$(document).ready(function() {
    $('.datepicker').datepicker({
        format: 'dd/mm/yyyy',
        weekStart: 1
    }).on('changeDate', function(){
        /* $('form').submit(); */
    });
});
