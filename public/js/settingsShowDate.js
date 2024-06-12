$(document).ready(function() {

    $.fn.ajaxDateGetter = function(selectedExpenseOrIncomeValue, isChangeEvent, urlExpenseOrIncome, isIncomeOrExpenseDate) {
        $.ajax({
            type: "POST",
            url: urlExpenseOrIncome,
            data: "&ajax="+true+"&expenseIncomeNumber="+selectedExpenseOrIncomeValue,
            success: function(dateInformation) {
                var date = $.parseJSON(dateInformation);
                var isDateEmpty = $.isEmptyObject(date);
//alert(date["date_of_" + isIncomeOrExpenseDate]);
                if(!isDateEmpty) {
                    if ($('#data-to-be-changed').prop('disabled')) {
                        $('#data-to-be-changed').prop('disabled', false);
                    }

                    if (isChangeEvent === true) {
                       $('#data-to-be-changed').val(date["date_of_" + isIncomeOrExpenseDate]);
                    }

                    $('#data-to-be-changed').attr('max', date["date_of_" + isIncomeOrExpenseDate + "_current"]);

                    if ($('#data-change-button').prop('disabled')) {
                        $('#data-change-button').prop('disabled', false);
                    }
                }
                else {
                    $('#data-to-be-changed').val("");
                    $('#data-to-be-changed').prop('disabled', true);
                    if (!$('#data-change-button').prop('disabled')) {
                        $('#data-change-button').prop('disabled', true);
                    }
                }
            },
            error: function() {
                $('#data-change-button').prop('disabled', true);
                $('#data-to-be-changed').prop('disabled', true);
                alert("Something went wrong. Error"); 
            }  
        });
    }
    
    var selectedExpenseOrIncomeValue =  $('#expense-income-number').val();
    var isNumeric = $.isNumeric(selectedExpenseOrIncomeValue);
    var isPositiveInteger = /^[1-9]\d*$/.test(selectedExpenseOrIncomeValue);

    if (isNumeric && isPositiveInteger) {
        if ($('#data-to-be-changed').prop('disabled')) {
            $('#data-to-be-changed').prop('disabled', false);
        }
    }

    var isIncomeOrExpenseNumber = $('#expense-income-number-label').text();
    var urlExpenseOrIncome;
    var isIncomeOrExpenseDate;

    if (isIncomeOrExpenseNumber === "Expense number")
    {
        urlExpenseOrIncome = "/ajax/getDateFromDatabaseForExpenseItem";
        isIncomeOrExpenseDate = "expense";
        $.fn.ajaxDateGetter(selectedExpenseOrIncomeValue, false, urlExpenseOrIncome, isIncomeOrExpenseDate);
    }
    else if (isIncomeOrExpenseNumber === "Income number") 
    {
        urlExpenseOrIncome = "/ajax/getDateFromDatabaseForIncomeItem";
        isIncomeOrExpenseDate = "income";
        $.fn.ajaxDateGetter(selectedExpenseOrIncomeValue, false, urlExpenseOrIncome, isIncomeOrExpenseDate);
    }
    else
    {
        urlExpenseOrIncome = "";
        isIncomeOrExpenseDate = "";
        $('#data-to-be-changed').val("");
        $('#data-to-be-changed').prop('disabled', true);
        $('#data-change-button').prop('disabled', true);
    }

 /*
    var selectedExpenseOrIncomeValue =  $('#expense-income-number').val();
    var isNumeric = $.isNumeric(selectedExpenseOrIncomeValue);
    var isPositiveInteger = /^[1-9]\d*$/.test(selectedExpenseOrIncomeValue);
*/
 //   $.fn.ajaxDateGetter(selectedExpenseOrIncomeValue, false);

 /*   
    if (isNumeric && isPositiveInteger) {
        if ($('#data-to-be-changed').prop('disabled')) {
            $('#data-to-be-changed').prop('disabled', false);
        }
    }
*/

    $('#expense-income-number').on('change', function(e) {
        selectedExpenseOrIncomeValue = $(this).val();

        if (isIncomeOrExpenseNumber === "Expense number" || isIncomeOrExpenseNumber === "Income number")
        {
            $.fn.ajaxDateGetter(selectedExpenseOrIncomeValue, true, urlExpenseOrIncome, isIncomeOrExpenseDate);  
        }    
   //     $.fn.ajaxDateGetter(selectedExpenseOrIncomeValue, true);
    });
});