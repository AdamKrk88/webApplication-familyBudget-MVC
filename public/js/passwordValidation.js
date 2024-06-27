$(document).ready(function() {

    // Validate password 
    let passwordCheck = false; 
    

    $("#password").keyup(function () { 
        clearParagraphAboutServerPasswordValidation();
        validatePassword();

    }); 



    function validatePassword() { 

        let passwordValue = $("#password").val(); 

        passwordValue = $.trim(passwordValue);

        let passwordErrorMessage = "Password validation failed. What is missing?";

        let errorsCounter = {errorsTotalNumber: 0, errorMessageContactCounter: 0, missingCharactersInformation: "At least "};
        let isUppercase = /[A-ZĄĆĘŁŃÓŚŹŻ]+/.test(passwordValue);
        let isLowercase = /[a-ząćęłńóśźż]+/.test(passwordValue);
        let isNumber    = /[0-9]+/.test(passwordValue);
        let isSpecialChars = /[^\w]+/.test(passwordValue);
        let isPasswordLengthCorrect = passwordValue.length > 9 ? true : false;
        let isPasswordLengthErrorPresent = false;

        let validationResult = {uppercase: isUppercase, lowercase: isLowercase, number: isNumber, special: isSpecialChars, length: isPasswordLengthCorrect};

        for (let key in validationResult) {
            if (validationResult[key] === false) {
                errorsCounter.errorsTotalNumber = errorsCounter.errorsTotalNumber + 1;
            }
        }

        if (validationResult.length === false) {
            isPasswordLengthErrorPresent = true;
        }

        if (!isUppercase) {
            errorMessageBuilder("one uppercase letter", false, false, isPasswordLengthErrorPresent, errorsCounter);
        }

        if (!isLowercase) {
            errorMessageBuilder("one lowercase letter", false, false, isPasswordLengthErrorPresent, errorsCounter);
        }

        if (!isNumber) {
            errorMessageBuilder("one number", false, false, isPasswordLengthErrorPresent, errorsCounter);
        }

        if (!isSpecialChars) { 
            errorMessageBuilder("one special character", true, false, isPasswordLengthErrorPresent, errorsCounter);
        }

        if (!isPasswordLengthCorrect) { 
            errorMessageBuilder("Length at least 10 characters", false, true, isPasswordLengthErrorPresent, errorsCounter);

        }

        /*
        let nameValue = $('#name').val();
        let emailValue = $('#email').val();
        nameValue = $.trim(nameValue);
        emailValue = $.trim(emailValue);
        */

        //no error messages in case of no errors detected. If password validation failed then error messages provided
        if (errorsCounter.errorsTotalNumber > 0) 
        { 
        //    $("#password-validation-failed").text(passwordErrorMessage); 
        
            provideFirstParagraphAboutPasswordValidation(passwordErrorMessage);
            $("#validation-password-jquery").text(errorsCounter.missingCharactersInformation);

            deactivateChangePasswordButton();
            checkAndUpdateChangePasswordReadiness(false);

         //   return false; 
        } 
        else 
        { 
            clearFirstParagraphAboutPasswordValidation();
            $("#validation-password-jquery").empty();
            checkAndUpdateChangePasswordReadiness(true);
            activateChangePasswordButton();


    //        $("#password-validation-failed").empty();
    //        $("#validation-password-jquery").empty();

       /*     if (isButtonDisabled && nameValue.length > 0 && emailValue.length > 0) 
            {
                $('#registration-button').prop('disabled', false);
    
            } */

    //        return true;

            /*
            else
            {
                return false;
            }
            */
            
        } 
    }


    //builder for error message indicating what is missing in the password
    function errorMessageBuilder(message, lastMessageFromRegex, lengthMessage, isPasswordLengthErrorPresent, errorsCounter) { 
        errorsCounter.errorMessageContactCounter = errorsCounter.errorMessageContactCounter + 1; 

        if (errorsCounter.errorMessageContactCounter === errorsCounter.errorsTotalNumber && !lengthMessage) {
            errorsCounter.missingCharactersInformation = errorsCounter.missingCharactersInformation + message;
        }
        else if (lastMessageFromRegex) {
            errorsCounter.missingCharactersInformation = errorsCounter.missingCharactersInformation + message + ". ";
        }
        else if ((errorsCounter.errorsTotalNumber === 3 && errorsCounter.errorMessageContactCounter === 2 && isPasswordLengthErrorPresent) || (errorsCounter.errorsTotalNumber === 2 && errorsCounter.errorMessageContactCounter === 1 && isPasswordLengthErrorPresent)) {
            errorsCounter.missingCharactersInformation = errorsCounter.missingCharactersInformation + message + ". ";
        }
        else if (errorsCounter.errorMessageContactCounter === errorsCounter.errorsTotalNumber && lengthMessage && errorsCounter.errorMessageContactCounter === 1) {
            errorsCounter.missingCharactersInformation = message;
        }
        else if (errorsCounter.errorMessageContactCounter === errorsCounter.errorsTotalNumber && lengthMessage && errorsCounter.errorMessageContactCounter > 1) {
            errorsCounter.missingCharactersInformation = errorsCounter.missingCharactersInformation + message;
        }
        else {
            errorsCounter.missingCharactersInformation = errorsCounter.missingCharactersInformation + message + ", ";
        }
    }

    function checkAndUpdateChangePasswordReadiness(readinessStatus)
    {
        if (passwordCheck === !readinessStatus)
        {
            passwordCheck = readinessStatus;
        }
    }

    function provideFirstParagraphAboutPasswordValidation(passwordErrorMessage)
    {
        if ($.trim($("#password-validation-failed").text()).length === 0)
        {
            $("#password-validation-failed").text(passwordErrorMessage);
        }
    }  
   
   
    function clearFirstParagraphAboutPasswordValidation()
    {
        if ($("#password-validation-failed").text().length > 0)
        {
            $("#password-validation-failed").empty();
        }
    }

    function clearParagraphAboutServerPasswordValidation()
    {
        if ($("#text-message-paragraph").text().length > 0)
        {
            $("#text-message-paragraph").empty();
        }
    }

    function activateChangePasswordButton()
    {
        let isButtonDisabled = $('#data-change-button').is(":disabled");
        
        if (isButtonDisabled) 
        {
            $('#data-change-button').prop('disabled', false);
        }
    }

    function deactivateChangePasswordButton()
    {
        let isButtonDisabled = $('#data-change-button').is(":disabled");
        
        if (!isButtonDisabled) 
        {
            $('#data-change-button').prop('disabled', true);
        }
    }


    // Submit button 
    $("#data-change-button").click(function () { 

        if (passwordCheck == true) 
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