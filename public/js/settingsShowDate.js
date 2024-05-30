$(document).ready(function() {

    $.fn.ajaxDateGetter = function(selectedExpenseOrIncomeValue, isChangeEvent) {
        $.ajax({
            type: "POST",
            url: "/ajax/getDateFromDatabaseForExpenseItem",
            data: "&ajax="+true+"&expenseIncomeNumber="+selectedExpenseOrIncomeValue,
            success: function(dateInformation) {
                var date = $.parseJSON(dateInformation);
                var isDateEmpty = $.isEmptyObject(date);

                if(!isDateEmpty) {
                    if ($('#data-to-be-changed').prop('disabled')) {
                        $('#data-to-be-changed').prop('disabled', false);
                    }

                    if (isChangeEvent === true) {
                       $('#data-to-be-changed').val(date["date_of_expense"]);
                    }

                    $('#data-to-be-changed').attr('max', date["date_of_expense_first"]);

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

    $.fn.ajaxDateGetter(selectedExpenseOrIncomeValue, false);

    if (isNumeric && isPositiveInteger) {
        if ($('#data-to-be-changed').prop('disabled')) {
            $('#data-to-be-changed').prop('disabled', false);
        }
    }

    $('#expense-income-number').on('change', function(e) {
        selectedExpenseOrIncomeValue = $(this).val();
        $.fn.ajaxDateGetter(selectedExpenseOrIncomeValue, true);
        
    });
});