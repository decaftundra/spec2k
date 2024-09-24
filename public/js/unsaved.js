var unsaved = false;

$("form :input,form textarea,form checkbox,form radio,form select").change(function(){ // triggers change in all input fields including text type
	unsaved = true;
});

$("input[type='text'], textarea").keyup(function () {
    unsaved = true;
});

// prevents warning firing when trying to save changes
$(":input[type='submit']").click(function(){
	unsaved = false;
});

$('.datepicker').datepicker({
    format: 'dd/mm/yyyy',
    weekStart: 1
}).on('changeDate', function(){
    unsaved = true;
});

// fires dialog box
function unloadPage(){ 
    if(unsaved){
        return "You have unsaved changes on this page. Do you want to leave this page and discard your changes or stay on this page?";
    }
}

window.onbeforeunload = unloadPage;