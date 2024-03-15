$(document).ready(function() {
   //declaration for fields with amount and date
    const amountInput = $('#amount');
    const dateInput = $('#date');
    var isRequiredFieldsBlank;

    //get last day of the month
    var dateObject = new Date();
    var month = dateObject.getMonth() + 1;
 //   var lastDayDateFormat = new Date(dateObject.getFullYear(), month, 0);
    var currentDay = dateObject.getDate();

    var maxDate = dateObject.getFullYear() + '-' +
    (month<10 ? '0' : '') + month + '-' + currentDay;

    //restriction for amount and date fields
    amountInput.attr('min','0.01');
    dateInput.attr('min','2023-01-01');
    dateInput.attr('max', maxDate);
    
    //submit button disabled if expense categories or payment method not available 
    if ($('#no-categories').length > 0 || $('#no-payment-option').length > 0) {
        $('#buttonToSubmitForm').prop('disabled', true);
    }

    //restriction for amount input provided as manual, so from keyboard
    amountInput.on('keypress', function (event) {
        var regexInputBeginning = new RegExp("[e\+\-\.]","i");
        var regexNext = new RegExp("[e\+\-]","i");
        var resultOfMatch = $(this).val().match(/^\d+\.?\d?$/) ? true : false;
        var key = String.fromCharCode(!event.key ? event.which : event.charCode);

        if ($(this).val().length == 0 && regexInputBeginning.test(key)) {
            event.preventDefault(); 
        }      

       else if ($(this).val().length > 0 && (regexNext.test(key) || !resultOfMatch || key == ".")) {
           event.preventDefault();   
        }
    });

    //keep two decimal places
    amountInput.change(function () {
        var result = parseFloat($(this).val()).toFixed(2);
        $(this).val(result);
    });   
 

    //no manual input for date
    dateInput.keypress(function(e) {
        e.preventDefault();
    });
    
    //action to be taken after button click
    $('#buttonToSubmitForm').click(function() {
        amountInput.get(0).required = false;
        dateInput.get(0).required = false;
        isRequiredFieldsBlank = false;
   //     $('#logoForPage').focus();
       
        //validation if amount or date fields are empty
        if(amountInput.val() =='' || amountInput.val().length - 1 == 0) {
            $('#expenseRegisterConfirmation > p').html('');
            amountInput.get(0).required = true;
            amountInput.get(0).oninput = function() {this.setCustomValidity('');};
            amountInput.get(0).oninvalid = function() {this.setCustomValidity('Please fill out this field');};
            amountInput.get(0).reportValidity();
            isRequiredFieldsBlank = true;
        }
        else if(dateInput.val() =='' || dateInput.val().length == 0) {
            $('#expenseRegisterConfirmation > p').html('');
            dateInput.get(0).required = true;
            dateInput.get(0).oninput = function() {this.setCustomValidity('');};
            dateInput.get(0).oninvalid = function() {this.setCustomValidity('Please fill out this field');};
            dateInput.get(0).reportValidity();
            isRequiredFieldsBlank = true;
        }
        
        //action to be taken after successful amount and date fields verification - insert data into database or error message
        if (!isRequiredFieldsBlank) {    
            $.ajax({
                type: "POST",
                url: "/ajax/processFirstForm",
                data: $('#firstForm').serialize()+"&ajax="+true,
            }).done(function() {
                $.ajax({
                    type: "POST",
                    url: "/ajax/processSecondForm",
                    data: $('#secondForm').serialize()+"&ajax="+true,
                    success: function(errorMessage) {
                        if(!errorMessage) {
                            $('#expenseRegisterConfirmation > p').html('Expense is registered successfully. Click <a href=\"/expense/display-expense-form\" class=\"font-light-orange link-registration-income-expense\">here</a> to insert next one');
                            $('#buttonToSubmitForm').prop('disabled', true);
                        }
                        else {
                            var json = JSON.parse(errorMessage);
                            $('#expenseRegisterConfirmation > p').html(json);
                            $('#buttonToSubmitForm').blur();
                        }
                    },
                    error: function() {
                        alert("Something went wrong. Error");
                        $('#buttonToSubmitForm').blur();
                    }
                });
                }).fail(function() {
                    alert("Something went wrong. Error");
                    $('#buttonToSubmitForm').blur();
                    });
        }  
    });   
});