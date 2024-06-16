$(document).ready(function() {
   //declaration for fields with amount and date
    const amountInput = $('#amount');
    const dateInput = $('#date');
    var isRequiredFieldsBlank;

    //get current day of the month
    var dateObject = new Date();
    var month = dateObject.getMonth() + 1;
 //   var lastDayDateFormat = new Date(dateObject.getFullYear(), month, 0);
    var currentDay = dateObject.getDate();

    var maxDate = dateObject.getFullYear() + '-' +
    (month<10 ? '0' : '') + month + '-' + (currentDay<10 ? '0' : '') + currentDay;

    //restriction for amount and date fields
    amountInput.attr('min','0.01');
    amountInput.attr('max','1000000000');
    dateInput.attr('min','2023-01-01');
    dateInput.attr('max', maxDate);

    //set default date as current day
    dateInput.val(maxDate);
    
    //submit button disabled if expense categories or payment method not available 
    if ($('#no-categories').length > 0 || $('#no-payment-option').length > 0) {
        $('#buttonToSubmitForm').prop('disabled', true);
    }

     //set up minimum and maximal expense/income amount for single item
     amountInput.on('input', function (event) {
        var min = parseFloat($(this).attr('min'));
        var max = parseFloat($(this).attr('max'));
        var amountProvided = parseFloat($(this).val());
        
        if (amountProvided > max)
        {
            $(this).val(max);
        }
        else if (amountProvided < min)
        {
            $(this).val(min);
        }       

    });

    var amountBefore;
    //restriction for amount input provided as manual, so from keyboard
    amountInput.on('keypress', function (event) {
        var regexInputBeginning = new RegExp("[e\+\-\.]","i");
        var regexNext = new RegExp("[e\+\-]","i");
        var resultOfMatch = $(this).val().match(/^\d*\.?\d?\d?$/) ? true : false;
        var key = String.fromCharCode(!event.key ? event.which : event.charCode);
        amountBefore = $(this).val();

        if ($(this).val().length == 0 && regexInputBeginning.test(key)) {
            event.preventDefault(); 
        }      

       else if ($(this).val().length > 0 && (regexNext.test(key) || !resultOfMatch || key == ".")) {
           event.preventDefault();   
        }
    });

    //keep two decimal places
    amountInput.change(function () {
        if ($(this).val() !== null && $(this).val().trim() !== "") {
            var result = parseFloat($(this).val()).toFixed(2);
            $(this).val(result);
        }
    });  

    //if there is try to have three decimal places, then amount is rounded to one or two decimal places depending on the condition
    amountInput.on('input', function () {
        var amountAfterRounded = (Math.floor(parseFloat($(this).val())*100)/100).toString();

        if ($(this).val().split(".").length === 2 && $(this).val().split(".")[1].length > 2) {
            if (amountBefore === amountAfterRounded) {
                $(this).val(Math.floor(parseFloat($(this).val())*100)/100); 
            }
            else {
                $(this).val(Math.floor(parseFloat($(this).val())*10)/10);   
            }
        }
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

        //delete message(s) from prevous button click 
        if ($("#expense-message-second").length) 
        {
            $("#expense-message-first").text("");
            $("#expense-message-second").text("");
        }
        else if ($("#expense-message-first").length)
        {
            $("#expense-message-first").text("");
        }

        //validation if amount or date fields are empty
        if(amountInput.val() =='' || amountInput.val().length - 1 == 0) {
            $('#expenseRegisterConfirmation > p').text('');
            amountInput.get(0).required = true;
            amountInput.get(0).oninput = function() {this.setCustomValidity('');};
            amountInput.get(0).oninvalid = function() {this.setCustomValidity('Please fill out this field');};
            amountInput.get(0).reportValidity();
            isRequiredFieldsBlank = true;
        }
        else if(dateInput.val() =='' || dateInput.val().length == 0) {
            $('#expenseRegisterConfirmation > p').text('');
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
                data: $('#firstForm').serialize()+"&ajax="+true+"&currentDate="+maxDate,
            }).done(function() {
                $.ajax({
                    type: "POST",
                    url: "/ajax/processSecondForm",
                    data: $('#secondForm').serialize()+"&ajax="+true,
                    success: function(errorMessage) {
                        var json = JSON.parse(errorMessage);
                        if(json.length === 0) {
                            $('#expense-message-first').text('Expense is registered successfully');
                            amountInput.val('');
                            
                            if ($('#comment').val().length > 0) {
                                $('#comment').val('');
                            }
                   //         $('#buttonToSubmitForm').prop('disabled', true);
                        }
                        else if (json.length === 1) {
                            $('#expense-message-first').text(json[0]);
               //             $('#buttonToSubmitForm').blur();
                        }
                        else if (json.length === 2) {
                            $('#expense-message-first').text("1. " + json[0]);
                            $('#expense-message-second').text("2. " + json[1]);
                  //          $('#buttonToSubmitForm').blur();
                        }
                        else {
                         //   var json = JSON.parse(errorMessage);
                            $('#expense-message-first').text('Internal error in aplication');
                   //         $('#buttonToSubmitForm').blur();
                            $('#buttonToSubmitForm').prop('disabled', true);
                        }

                        $('#buttonToSubmitForm').blur();
                    },
                    error: function() {
                        alert("Something went wrong. Error");
                        $('#buttonToSubmitForm').blur();
                        $('#buttonToSubmitForm').prop('disabled', true);
                    }
                });
                }).fail(function() {
                    alert("Something went wrong. Error");
                    $('#buttonToSubmitForm').blur();
                    $('#buttonToSubmitForm').prop('disabled', true);
                    });
        }  
    });   
});