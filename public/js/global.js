// Convert first letter of string to uppercase.
function jsUcfirst(string){
    return string.charAt(0).toUpperCase() + string.slice(1);
}



function setWordCounts()
{
	$('input[type=text], textarea').each(function(){
		var text_max = $(this).prop('maxlength');
		var id = $(this).prop('id');
	    var text_length = $(this).val().length;
	    var text_remaining = text_max - text_length;
	    $('#wordcount-feedback-' + id).html('<small>' + text_remaining + ' chars remaining</small>');
	});
}

$(document).ready(function() {
    
    setWordCounts();
    
    $('input[type=text], textarea').keyup(function() {
		var text_max = $(this).prop('maxlength');
		var id = $(this).prop('id');
	    var text_length = $(this).val().length;
        var text_remaining = text_max - text_length;
        
        if (text_remaining == 0) {
            $('#wordcount-feedback-' + id).css({'color':'#a94442'});
        }
        
        $('#wordcount-feedback-' + id).html('<small>' + text_remaining + ' chars remaining</small>');
    });
    
    // Alerts.
    if (!$.isEmptyObject(sweetalert)) {
        swal({
          title: jsUcfirst(sweetalert.status)+'!',
          text: sweetalert.message,
          type: sweetalert.status,
          confirmButtonText: sweetalert.confirm,
          showConfirmButton: sweetalert.confirm ? true : false,
          timer: sweetalert.confirm ? null : 1500,
          closeOnConfirm: sweetalert.confirm ? true : false
        });
    }
}); 