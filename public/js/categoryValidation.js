$(document).ready(function() {

   
    let categoryCheck = false; 
    

    $("#new-category").keyup(function () { 
        clearParagraphWithMessageFromServerCategoryValidation();
        validateCategory();

    }); 


    // Validate name
    function validateCategory() { 

        let category = $("#new-category").val(); 

        category = $.trim(category);
        categoryLength = category.length;

        let isCategoryValidated = /^([a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ]+)* ?[a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ]+$/.test(category);


        //no error messages in case of no errors detected. If name validation failed then error messages provided
        if (isCategoryValidated && categoryLength < 21) 
        { 
            clearParagraphWithMessageFromClientCategoryValidation();
            checkAndUpdateAddCategoryReadiness(true);
            activateAddCategoryButton()
        } 
        else if (categoryLength === 0)
        {
            clearParagraphWithMessageFromClientCategoryValidation();
            checkAndUpdateAddCategoryReadiness(false);
            deactivateAddCategoryButton(); 
        } 
        else 
        { 
            provideErrorMessageFromClientCategoryValidation();
            checkAndUpdateAddCategoryReadiness(false);
            deactivateAddCategoryButton(); 
        } 
    }


    function checkAndUpdateAddCategoryReadiness(readinessStatus)
    {
        if (categoryCheck === !readinessStatus)
        {
            categoryCheck = readinessStatus;
        }
    }
    
    function clearParagraphWithMessageFromServerCategoryValidation()
    {
        if ($("#text-message-paragraph").text().length > 0)
        {
            $("#text-message-paragraph").empty();
        }
    }

    function clearParagraphWithMessageFromClientCategoryValidation()
    {
        if ($("#category-validation-jquery").text().length > 0)
        {
            $("#category-validation-jquery").empty();
        }
    }

    function provideErrorMessageFromClientCategoryValidation()
    {
        if ($("#category-validation-jquery").text().length === 0)
        {
            $("#category-validation-jquery").text("Only letters and one space allowed. Maximum number of characters is 20");
        }
    }

    function activateAddCategoryButton()
    {
        let isButtonDisabled = $('#data-change-button').is(":disabled");
        
        if (isButtonDisabled) 
        {
            $('#data-change-button').prop('disabled', false);
        }
    }

    function deactivateAddCategoryButton()
    {
        let isButtonDisabled = $('#data-change-button').is(":disabled");
        
        if (!isButtonDisabled) 
        {
            $('#data-change-button').prop('disabled', true);
        }
    }


    // Submit button 
    $("#data-change-button").click(function () { 

        if (categoryCheck === true) 
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