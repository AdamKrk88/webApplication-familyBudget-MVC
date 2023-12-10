$(document).ready(function() {
    const amountInput = $('#amount');
    const dateInput = $('#date');
    var isRequiredFieldsBlank;

    amountInput.attr('min','0.01');
    dateInput.attr('min','2022-01-01');
    dateInput.attr('max','{{ currentDate }}');

    if ($('#no-categories').length > 0) {
        $('#buttonToSubmitForm').prop('disabled', true);
    }

    amountInput.get(0).oninput = function() {
        this.value = this.value.replace(/[e\+\-]/gi, "");
    }; 

    amountInput.keypress(function(e) {
        if (e.which < 48 || e.which > 57) {
            e.preventDefault();
        }
    });

    dateInput.keypress(function(e) {
        e.preventDefault();
    });
    

    $('#buttonToSubmitForm').click(function() {
        amountInput.get(0).required = false;
        dateInput.get(0).required = false;
        isRequiredFieldsBlank = false;
    //	$('#logoForPage').focus();
        
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
        
        if (!isRequiredFieldsBlank) {
            $.ajax({
                type: "POST",
                url: "/includes/insertIncomePartOne.php",
                data: $('#firstForm').serialize(),
            }).done(function() {
                $.ajax({
                    type: "POST",
                    url: "/includes/insertIncomePartTwo.php",
                    data: $('#secondForm').serialize(),
                    success: function(errorMessage) {
                        if(!errorMessage) {
                            $('#incomeRegisterConfirmation > p').html('Income is registered successfully. Click <a href=\"addincome.php\" class=\"font-light-orange link-registration-income-expense\">here</a> to insert next one');
                            $('#buttonToSubmitForm').prop('disabled', true);
                        }
                        else {
                            var json = JSON.parse(errorMessage);
                            $('#incomeRegisterConfirmation > p').html(json);
                        }
                    }});
                }).fail(function() {
                    alert("Something went wrong. Error");
                    });
        }  
    });   
});