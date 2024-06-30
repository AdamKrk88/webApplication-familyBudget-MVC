$(document).ready(function() {

   
    let paymentCheck = false; 
    

    $("#new-payment").keyup(function () { 
        clearParagraphWithMessageFromServerPaymentValidation();
        validatePayment();

    }); 


    // Validate name
    function validatePayment() { 

        let payment = $("#new-payment").val(); 

        payment = $.trim(payment);
        paymentLength = payment.length;

        let isPaymentValidated = /^([a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ]+)* ?[a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ]+$/.test(payment);


        //no error messages in case of no errors detected. If name validation failed then error messages provided
        if (isPaymentValidated && paymentLength < 21) 
        { 
            clearParagraphWithMessageFromClientPaymentValidation();
            checkAndUpdateAddPaymentReadiness(true);
            activateAddPaymentButton()
        } 
        else if (paymentLength === 0)
        {
            clearParagraphWithMessageFromClientPaymentValidation();
            checkAndUpdateAddPaymentReadiness(false);
            deactivateAddPaymentButton(); 
        } 
        else 
        { 
            provideErrorMessageFromClientPaymentValidation();
            checkAndUpdateAddPaymentReadiness(false);
            deactivateAddPaymentButton(); 
        } 
    }


    function checkAndUpdateAddPaymentReadiness(readinessStatus)
    {
        if (paymentCheck === !readinessStatus)
        {
            paymentCheck = readinessStatus;
        }
    }
    
    function clearParagraphWithMessageFromServerPaymentValidation()
    {
        if ($("#text-message-paragraph").text().length > 0)
        {
            $("#text-message-paragraph").empty();
        }
    }

    function clearParagraphWithMessageFromClientPaymentValidation()
    {
        if ($("#payment-validation-jquery").text().length > 0)
        {
            $("#payment-validation-jquery").empty();
        }
    }

    function provideErrorMessageFromClientPaymentValidation()
    {
        if ($("#payment-validation-jquery").text().length === 0)
        {
            $("#payment-validation-jquery").text("Only letters and one space allowed. Maximum number of characters is 20");
        }
    }

    function activateAddPaymentButton()
    {
        let isButtonDisabled = $('#data-change-button').is(":disabled");
        
        if (isButtonDisabled) 
        {
            $('#data-change-button').prop('disabled', false);
        }
    }

    function deactivateAddPaymentButton()
    {
        let isButtonDisabled = $('#data-change-button').is(":disabled");
        
        if (!isButtonDisabled) 
        {
            $('#data-change-button').prop('disabled', true);
        }
    }


    // Submit button 
    $("#data-change-button").click(function () { 

        if (paymentCheck === true) 
        { 
            return true; 
        } 
        else 
        { 
            $(this).blur();
            return false;
        } 
    }); 

});