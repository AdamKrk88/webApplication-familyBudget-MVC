$(document).ready(function() {

    // Validate password 
    let passwordCheck = false; 
  //  let nameEmailNotZeroLength = false;
    let isSubmitOptionActiveForButton = false;
    displayInformationAfterSubmission();

    $("#password").keyup(function () { 
   
        passwordCheck = validatePassword();

        if (passwordCheck)
        {
            let nameValue = $("#name").val();
            let emailValue = $("#email").val();

            if ($.type(nameValue) === "string" && $.type(emailValue) === "string") 
            {
                nameValue = $.trim(nameValue);
                emailValue = $.trim(emailValue);
                let isNameValidated = validateName(nameValue);
                let isEmailValidated = validateEmail(emailValue);

                if (nameValue.length > 0 && emailValue.length > 0 && isNameValidated && isEmailValidated)
                {
                    clearFirstParagraphAboutPasswordValidation();
                    $("#missing-information").empty();
                    activateRegistrationButton();
                    checkAndUpdateSubmitReadiness(true);
                }
                else if (nameValue.length === 0 || emailValue.length === 0)
                {
                    clearFirstParagraphAboutPasswordValidation();
                    $("#missing-information").text("Name and email cannot be blank. Mandatory fields");
                    deactivateRegistrationButton();
                    checkAndUpdateSubmitReadiness(false);
                }
                else if (!isNameValidated && isEmailValidated)
                {  
                    clearFirstParagraphAboutPasswordValidation();     
                    $("#missing-information").text("Only letters and one space allowed in name");
                    deactivateRegistrationButton();
                    checkAndUpdateSubmitReadiness(false);
                }
                else if (isNameValidated && !isEmailValidated)
                {  
                    clearFirstParagraphAboutPasswordValidation();    
                    $("#missing-information").text("Incorrect email format");
                    deactivateRegistrationButton();
                    checkAndUpdateSubmitReadiness(false);
                }
                else
                {
                    clearFirstParagraphAboutPasswordValidation();    
                    $("#missing-information").text("Only letters and one space allowed in name. Incorrect email format");
                    deactivateRegistrationButton();
                    checkAndUpdateSubmitReadiness(false);
                }
            }
            else
            {
                clearFirstParagraphAboutPasswordValidation(); 
                $("#missing-information").text("Error for name or email input");
                deactivateRegistrationButton();
                checkAndUpdateSubmitReadiness(false);
            }

        }
        
   //     alert(isSubmitOptionActiveForButton);

    }); 



    //Error message display related with change for name and email input in connection with result of password validation
    $("#name,#email").keyup(function () { 

        let nameValue = $("#name").val();
        let emailValue = $("#email").val();
        let passwordValue = $("#password").val();
    //    let isErrorMessagePresent = $.trim($("#missing-information").text()).length > 0 ? true : false;
        let isPasswordErrorMessagePresent = $.trim($("#password-validation-failed").text()).length > 0 ? true : false;
        let isButtonDisabled = $('#registration-button').is(":disabled");



        if ($.type(nameValue) === "string" && $.type(emailValue) === "string" && $.type(passwordValue) === "string") 
        {
            nameValue = $.trim(nameValue);
            emailValue = $.trim(emailValue);
            passwordValue = $.trim(passwordValue);
            let isNameValidated = validateName(nameValue);
            let isEmailValidated = validateEmail(emailValue);

            //activation or deactivation of registration button
            if (passwordCheck === true && nameValue.length > 0 && emailValue.length > 0 && isNameValidated && isEmailValidated) 
            { 
                activateRegistrationButton();         
            } 
            else if (passwordCheck === false || nameValue.length === 0 || emailValue.length === 0 || !isNameValidated || !isEmailValidated)
            {
                deactivateRegistrationButton();
            }


            //Error message management in case password validation is successful
            if (passwordCheck === true && passwordValue.length > 0 && nameValue.length > 0 && emailValue.length > 0 && isNameValidated && isEmailValidated)
            {
                $("#missing-information").empty();
                checkAndUpdateSubmitReadiness(true);
              
            }
            else if (passwordCheck === true && passwordValue.length > 0 && (nameValue.length === 0 || emailValue.length === 0)) 
            {
                $("#missing-information").text("Name and email cannot be blank. Mandatory fields");
                checkAndUpdateSubmitReadiness(false);
            }
            else if (passwordCheck === true && passwordValue.length > 0 && !isNameValidated && isEmailValidated) 
            {
                $("#missing-information").text("Only letters and one space allowed in name");
                checkAndUpdateSubmitReadiness(false);
            }
            else if (passwordCheck === true && passwordValue.length > 0 && isNameValidated && !isEmailValidated) 
            {
                $("#missing-information").text("Incorrect email format");
                checkAndUpdateSubmitReadiness(false);
            }
            else if (passwordCheck === true && passwordValue.length > 0 && !isNameValidated && !isEmailValidated) 
            {
                $("#missing-information").text("Only letters and one space allowed in name. Incorrect email format");
                checkAndUpdateSubmitReadiness(false);
            }
            
//alert(passwordCheck === false && isPasswordErrorMessagePresent);
            //Error message management in case password validation failed
            if (passwordCheck === false && isPasswordErrorMessagePresent) 
            {
                clearFirstParagraphAboutPasswordValidation();
            }
            
            if (passwordCheck === false && passwordValue.length === 0 && (nameValue.length === 0 || emailValue.length === 0)) 
            {
                $("#missing-information").text("All fields are mandatory");
            }
            else if (passwordCheck === false && passwordValue.length > 0 && (nameValue.length === 0 || emailValue.length === 0)) 
            {
                $("#missing-information").text("All fields are mandatory. Password validation failed");
            }
            else if (passwordCheck === false && passwordValue.length === 0 && nameValue.length > 0 && emailValue.length > 0) 
            {
                $("#missing-information").text("Password is mandatory");
            }
            else if (passwordCheck === false && passwordValue.length > 0 && nameValue.length > 0 && emailValue.length > 0 && isNameValidated && isEmailValidated) 
            {
                $("#missing-information").text("Password validation failed");
            }
            else if (passwordCheck === false && passwordValue.length > 0 && nameValue.length > 0 && emailValue.length > 0 && !isNameValidated && isEmailValidated) 
            {
                $("#missing-information").text("Password and name validation failed. Only letters and one space allowed in name");
            }
            else if (passwordCheck === false && passwordValue.length > 0 && nameValue.length > 0 && emailValue.length > 0 && isNameValidated && !isEmailValidated) 
            {
                $("#missing-information").text("Password and email validation failed. Incorrect email format");
            }
            else if (passwordCheck === false && passwordValue.length > 0 && nameValue.length > 0 && emailValue.length > 0 && !isNameValidated && !isEmailValidated) 
            {
                $("#missing-information").text("Password, name and email validation failed");
            }


        }
        else 
        { 
            deactivateRegistrationButton();
            checkAndUpdateSubmitReadiness(false);
        }
   //     alert(isSubmitOptionActiveForButton);
    });

                

/*
    $("#email").keyup(function () { 
        let nameValue = $("#name").val();
        let emailValue = $(this).val();

        if (passwordCheck == true && nameValue.length > 0 && emailValue.length > 0 ) 
        { 
            $('#registration-button').prop('disabled', false);
        } 
   
        
    }); 
*/
    function validateName(name) {
        return /^([a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ]+)* ?[a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ]+$/.test(name);
    }

    function validateEmail(email) {
        return /^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/.test(email);
    }


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

            //errorsCounter.missingCharactersInformation = errorsCounter.missingCharactersInformation + "Length at least 10 characters";
        }


    //    let isButtonDisabled = $('#registration-button').is(":disabled");
   //     $.trim(passwordValue)
        let nameValue = $('#name').val();
        let emailValue = $('#email').val();
        nameValue = $.trim(nameValue);
        emailValue = $.trim(emailValue);

        //no error messages in case of no errors detected. If password validation failed then error messages provided
        if (errorsCounter.errorsTotalNumber > 0) 
        { 
            $("#password-validation-failed").text(passwordErrorMessage); 
            $("#missing-information").text(errorsCounter.missingCharactersInformation);

            deactivateRegistrationButton();
     /*       if (!isButtonDisabled) 
            {
                $('#registration-button').prop('disabled', true);
            }   */

            checkAndUpdateSubmitReadiness(false);

            return false; 
        } 
        else 
        { 
    //        $("#password-validation-failed").empty();
    //        $("#missing-information").empty();

       /*     if (isButtonDisabled && nameValue.length > 0 && emailValue.length > 0) 
            {
                $('#registration-button').prop('disabled', false);
    
            } */

            return true;

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

    function checkAndUpdateSubmitReadiness(readinessStatus)
    {
        if (isSubmitOptionActiveForButton === !readinessStatus)
        {
            isSubmitOptionActiveForButton = readinessStatus;
        }
    }

    function clearFirstParagraphAboutPasswordValidation()
    {
        if ($("#password-validation-failed").text().length > 0)
        {
            $("#password-validation-failed").empty();
        }
    }

    function activateRegistrationButton()
    {
        let isButtonDisabled = $('#registration-button').is(":disabled");
        
        if (isButtonDisabled) 
        {
            $('#registration-button').prop('disabled', false);
        }
    }

    function deactivateRegistrationButton()
    {
        let isButtonDisabled = $('#registration-button').is(":disabled");
        
        if (!isButtonDisabled) 
        {
            $('#registration-button').prop('disabled', true);
        }
    }

    function displayInformationAfterSubmission()
    {
        if ($.trim($("#missing-information").text()).length === 0) 
        {

            let nameValue = $("#name").val();
            let emailValue = $("#email").val();
            let password = $("#password").val();

            if ($.type(nameValue) === "string" && $.type(emailValue) === "string" && $.type(password) === "string") 
            {
                nameValue = $.trim(nameValue);
                emailValue = $.trim(emailValue);
                password = $.trim(password);
                
                let errorMessage = "";
                
                if (nameValue.length === 0)
                {
                    errorMessage = "name";
                }

                if (emailValue.length === 0)
                {
                    errorMessage = errorMessage + " email";
                }

                if (password.length === 0)
                {
                    errorMessage = errorMessage + " password";
                }
                else
                {
                    passwordCheck = true;
                }

                errorMessage = $.trim(errorMessage);
                errorMessage = errorMessage.replace(" ",", ");
                errorMessage = errorMessage[0].toUpperCase() + errorMessage.slice(1) + " validation failed";
                errorMessage = errorMessage.replace(/,(?=[^,]+$)/, ' and');

                $("#missing-information").text(errorMessage);
    
            }
            else
            {
        //        clearFirstParagraphAboutPasswordValidation(); 
                $("#missing-information").text("Error for name or email or password input. It is not a string!");
        //      deactivateRegistrationButton();
        //      checkAndUpdateSubmitReadiness(false);
            }
        }
    }

    // Submit button 
    $("#registration-button").click(function () { 

        if (isSubmitOptionActiveForButton == true) 
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