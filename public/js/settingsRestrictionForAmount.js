$(document).ready(function() {
    
    //restriction for amount
    $('#data-to-be-changed').attr('min','0.01');
    $('#data-to-be-changed').attr('max','1000000000');

    //keep two decimal places
    $('#data-to-be-changed').change(function () {
        var result = parseFloat($(this).val()).toFixed(2);
        $(this).val(result);
    });  

    //set up minimum and maximal expense/income amount for single item
    $('#data-to-be-changed').on('input', function (event) {
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

    //restriction for amount input provided as manual, so from keyboard
    $('#data-to-be-changed').on('keypress', function (event) {
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

});