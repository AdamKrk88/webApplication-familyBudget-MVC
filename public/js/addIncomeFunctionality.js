$(document).ready(function() {
    //declaration for fields with amount and date
    const amountInput = $('#amount');
    const dateInput = $('#date');
    var isRequiredFieldsBlank;

    //get last day of the month
    var dateObject = new Date();
    var month = dateObject.getMonth() + 1;
    var lastDayDateFormat = new Date(dateObject.getFullYear(), month, 0);
    var lastDay = lastDayDateFormat.getDate();

    var maxDate = dateObject.getFullYear() + '-' +
    (month<10 ? '0' : '') + month + '-' + lastDay;
   

    //restriction for amount and date fields
  //  amountInput.attr('min','0.01');
    dateInput.attr('min','2023-01-01');
    dateInput.attr('max', maxDate);
    

    //if no categories available, then submit button inactive
    if ($('#no-categories').length > 0) {
        $('#buttonToSubmitForm').prop('disabled', true);
    }

  /*  //no possibilty to type +, -, e, E in amount field
    amountInput.get(0).oninput = function() {
        this.value = this.value.replace(/[e\+\-]/i, "");
    }; 
*/
    //only numbers allowed in amount field
 /*   amountInput.keypress(function(e) {
        if (e.which < 46 || (e.which > 46 && e.which < 48) || e.which > 57) {
            e.preventDefault();
        }
    });
*/
    //no manual, direct input from keyboard allowed
    dateInput.keypress(function(e) {
        e.preventDefault();
    });

    amountInput.on('keypress', function (event) {
        var regexInputBeginning = new RegExp("[e\+\-\.]","i");
        var regexNext = new RegExp("[e\+\-]","i");
        var resultOfMatch = $(this).val().match(/^\d+\.?\d?$/) ? true : false;
        var key = String.fromCharCode(!event.key ? event.which : event.charCode);
//   alert(resultOfMatch);
 //  alert(key);
        if ($(this).val().length == 0 && regexInputBeginning.test(key)) {
            event.preventDefault(); 
        }      

       else if ($(this).val().length > 0 && (regexNext.test(key) || !resultOfMatch || key == ".")) {
           event.preventDefault();   
        }
    });



    amountInput.change(function () {
      //   alert(this.val().match(/^\d+\.?\d?\d?/g));
        var result = parseFloat($(this).val()).toFixed(2);
        $(this).val(result);
  //   $(this).val(resultOfMatch);
   //  alert(typeof amountInput.val());
//       alert(resultOfMatch);
    });
   
    //action to be taken after button click
    $('#buttonToSubmitForm').click(function() {
        amountInput.get(0).required = false;
        dateInput.get(0).required = false;
        isRequiredFieldsBlank = false;
    //	$('#logoForPage').focus();
        
    //validation if amount or date fields are empty
        if(amountInput.val() =='' || amountInput.val().length - 1 == 0) {
            $('#incomeRegisterConfirmation > p').html('');
            amountInput.get(0).required = true;
            amountInput.get(0).oninput = function() {this.setCustomValidity('');};
            amountInput.get(0).oninvalid = function() {this.setCustomValidity('Please fill out this field');};
            amountInput.get(0).reportValidity();
            isRequiredFieldsBlank = true;
        }
        else if(dateInput.val() =='' || dateInput.val().length == 0) {
            $('#incomeRegisterConfirmation > p').html('');
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
                data: $('#firstForm').serialize(),
            }).done(function() {
                $.ajax({
                    type: "POST",
                    url: "/ajax/processSecondForm",
                    data: $('#secondForm').serialize(),
                    success: function(errorMessage) {
                        if(!errorMessage) {
                            $('#incomeRegisterConfirmation > p').html('Income is registered successfully. Click <a href=\"/income/displayIncomeForm\" class=\"font-light-orange link-registration-income-expense\">here</a> to insert next one');
                            $('#buttonToSubmitForm').prop('disabled', true);
                        }
                        else {
                            var json = JSON.parse(errorMessage);
                            $('#incomeRegisterConfirmation > p').html(json);
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