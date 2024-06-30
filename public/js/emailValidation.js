$(document).ready(function() {

   
    let emailCheck = false; 
    

    $("#email").keyup(function () { 
        clearParagraphWithMessageFromServerEmailValidation();
        validateEmail();

    }); 


    // Validate name
    function validateEmail() { 

        let emailValue = $("#email").val(); 

        emailValue = $.trim(emailValue);
        emailValueLength = emailValue.length;

        let isEmailValidated = /^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/.test(emailValue);


        //no error messages in case of no errors detected. If name validation failed then error messages provided
        if (isEmailValidated) 
        { 
            clearParagraphWithMessageFromClientEmailValidation();
            checkAndUpdateChangeEmailReadiness(true);
            activateChangeEmailButton()
        } 
        else if (emailValueLength === 0)
        {
            clearParagraphWithMessageFromClientEmailValidation();
            checkAndUpdateChangeEmailReadiness(false);
            deactivateChangeEmailButton(); 
        } 
        else 
        { 
            provideErrorMessageFromClientEmailValidation();
            checkAndUpdateChangeEmailReadiness(false);
            deactivateChangeEmailButton(); 
        } 
    }


    function checkAndUpdateChangeEmailReadiness(readinessStatus)
    {
        if (emailCheck === !readinessStatus)
        {
            emailCheck = readinessStatus;
        }
    }
    
    function clearParagraphWithMessageFromServerEmailValidation()
    {
        if ($("#text-message-paragraph").text().length > 0)
        {
            $("#text-message-paragraph").empty();
        }
    }

    function clearParagraphWithMessageFromClientEmailValidation()
    {
        if ($("#email-validation-jquery").text().length > 0)
        {
            $("#email-validation-jquery").empty();
        }
    }

    function provideErrorMessageFromClientEmailValidation()
    {
        if ($("#email-validation-jquery").text().length === 0)
        {
            $("#email-validation-jquery").text("Incorrect email format");
        }
    }

    function activateChangeEmailButton()
    {
        let isButtonDisabled = $('#data-change-button').is(":disabled");
        
        if (isButtonDisabled) 
        {
            $('#data-change-button').prop('disabled', false);
        }
    }

    function deactivateChangeEmailButton()
    {
        let isButtonDisabled = $('#data-change-button').is(":disabled");
        
        if (!isButtonDisabled) 
        {
            $('#data-change-button').prop('disabled', true);
        }
    }


    // Submit button 
    $("#data-change-button").click(function () { 

        if (emailCheck === true) 
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