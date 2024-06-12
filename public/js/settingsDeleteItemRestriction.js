$(document).ready(function()
{
    //submit button active if both expense number and category provided
    
    let expenseIncomeNumber = $('#expense-income-number').val();
    let expenseIncomeNumberIsNumeric = $.isNumeric(expenseIncomeNumber);
    let expenseIncomeNumberIsPositiveInteger = /^[1-9]\d*$/.test(expenseIncomeNumber);
    let isButtonDisabled = $('#data-change-button').is(":disabled");

    if (expenseIncomeNumberIsNumeric && expenseIncomeNumberIsPositiveInteger && isButtonDisabled)
    {
        $('#data-change-button').prop('disabled', false);
    }


    $("select").on('input', function(e) {
        expenseIncomeNumber = $('#expense-income-number').val();
        expenseIncomeNumberIsNumeric = $.isNumeric(expenseIncomeNumber);
        expenseIncomeNumberIsPositiveInteger = /^[1-9]\d*$/.test(expenseIncomeNumber);
        isButtonDisabled = $('#data-change-button').is(":disabled");
        
        if (expenseIncomeNumberIsNumeric && expenseIncomeNumberIsPositiveInteger && isButtonDisabled) {
            $('#data-change-button').prop('disabled', false);
        }

        if ((!expenseIncomeNumberIsNumeric || !expenseIncomeNumberIsPositiveInteger ) && !isButtonDisabled) {
            $('#data-change-button').prop('disabled', true);
        }

     });
   
});