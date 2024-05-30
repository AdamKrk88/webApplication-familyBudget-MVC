$(document).ready(function()
{
    //button activation if initial data after page load are correct
    var isButtonDisabled = $('#data-change-button').is(":disabled");
    var commentValue = $("#data-to-be-changed-comment").val(); 
    var expenseNumber = $('#expense-income-number').val();
    commentValue = $.trim(commentValue);
    var isCommentValid = /^[a-z0-9\040\.\-\/]*$/i.test(commentValue);
    var commentLength = commentValue.length;
    var expenseNumberIsNumeric = $.isNumeric(expenseNumber);
    var expenseNumberIsPositiveInteger = /^[1-9]\d*$/.test(expenseNumber);

    if (expenseNumberIsNumeric && expenseNumberIsPositiveInteger && isCommentValid && commentLength <= 50 && isButtonDisabled) {
        $('#data-change-button').prop('disabled', false);
    }


    //submit button active if expense number provided and comment have the valid format  
    $("select").on('input', function(e) {
        isButtonDisabled = $('#data-change-button').is(":disabled");
        commentValue = $("#data-to-be-changed-comment").val(); 
        expenseNumber = $('#expense-income-number').val();
        commentValue = $.trim(commentValue);
        isCommentValid = /^[a-z0-9\040\.\-\/]*$/i.test(commentValue);
        commentLength = commentValue.length;
        expenseNumberIsNumeric = $.isNumeric(expenseNumber);
        expenseNumberIsPositiveInteger = /^[1-9]\d*$/.test(expenseNumber);
        
        if (expenseNumberIsNumeric && expenseNumberIsPositiveInteger && isCommentValid && commentLength <= 50 && isButtonDisabled) {
            $('#data-change-button').prop('disabled', false);
        }

        if ((!expenseNumberIsNumeric || !expenseNumberIsPositiveInteger || !isCommentValid || commentLength > 50) && !isButtonDisabled) {
            $('#data-change-button').prop('disabled', true);
        }

     }); 
});