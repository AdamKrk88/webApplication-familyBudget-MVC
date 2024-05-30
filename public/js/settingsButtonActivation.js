$(document).ready(function()
{
    //submit button active if both expense number and category provided
    
    $("select, input").on('input', function(e) {
        var isButtonDisabled = $('#data-change-button').is(":disabled");
        
        if ($('#expense-income-number').val() != "--" && $('#data-to-be-changed').val() != "--" && $('#data-to-be-changed').val() != "" && isButtonDisabled) {
            $('#data-change-button').prop('disabled', false);
        }

        if (($('#expense-income-number').val() == "--" || $('#data-to-be-changed').val() == "--" || $('#data-to-be-changed').val() == "") && !isButtonDisabled) {
            $('#data-change-button').prop('disabled', true);
        }

     });
/*
    $("input").on('input', function() {
        var isButtonDisabled = $('#data-change-button').is(":disabled");
        
        if ($('#expense-income-number').val() != "--" && $('#data-to-be-changed').val() != "--" && $('#data-to-be-changed').val() != "" && isButtonDisabled) {
            $('#data-change-button').prop('disabled', false);
        }

        if (($('#expense-income-number').val() == "--" || $('#data-to-be-changed').val() == "--" || $('#data-to-be-changed').val() == "") && !isButtonDisabled) {
            $('#data-change-button').prop('disabled', true);
        }
     });
*/     
});