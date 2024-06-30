$(document).ready(function() {

   
    let nameCheck = false; 
    

    $("#username").keyup(function () { 
        clearParagraphWithMessageFromServerNameValidation();
        validateName();

    }); 


    // Validate name
    function validateName() { 

        let nameValue = $("#username").val(); 

        nameValue = $.trim(nameValue);
        nameValueLength = nameValue.length;

        let isNameValidated = /^([a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ]+)* ?[a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ]+$/.test(nameValue);


        //no error messages in case of no errors detected. If name validation failed then error messages provided
        if (isNameValidated) 
        { 
            clearParagraphWithMessageFromClientNameValidation();
            checkAndUpdateChangeNameReadiness(true);
            activateChangeNameButton()
        } 
        else if (nameValueLength === 0)
        {
            clearParagraphWithMessageFromClientNameValidation();
            checkAndUpdateChangeNameReadiness(false);
            deactivateChangeNameButton(); 
        } 
        else 
        { 
            provideErrorMessageFromClientNameValidation();
            checkAndUpdateChangeNameReadiness(false);
            deactivateChangeNameButton(); 
        } 
    }


    function checkAndUpdateChangeNameReadiness(readinessStatus)
    {
        if (nameCheck === !readinessStatus)
        {
            nameCheck = readinessStatus;
        }
    }
    
    function clearParagraphWithMessageFromServerNameValidation()
    {
        if ($("#text-message-paragraph").text().length > 0)
        {
            $("#text-message-paragraph").empty();
        }
    }

    function clearParagraphWithMessageFromClientNameValidation()
    {
        if ($("#name-validation-jquery").text().length > 0)
        {
            $("#name-validation-jquery").empty();
        }
    }

    function provideErrorMessageFromClientNameValidation()
    {
        if ($("#name-validation-jquery").text().length === 0)
        {
            $("#name-validation-jquery").text("Only letters and one space allowed in name");
        }
    }

    function activateChangeNameButton()
    {
        let isButtonDisabled = $('#data-change-button').is(":disabled");
        
        if (isButtonDisabled) 
        {
            $('#data-change-button').prop('disabled', false);
        }
    }

    function deactivateChangeNameButton()
    {
        let isButtonDisabled = $('#data-change-button').is(":disabled");
        
        if (!isButtonDisabled) 
        {
            $('#data-change-button').prop('disabled', true);
        }
    }


    // Submit button 
    $("#data-change-button").click(function () { 

        if (nameCheck === true) 
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