$(document).ready(function() {

// Validate comment 
    let commentCheck = true; 
    $("#data-to-be-changed-comment").keyup(function () { 
        commentCheck = validateComment(); 
    }); 


    function validateComment() { 
     //   alert(commentCheck);
        let commentValue = $("#data-to-be-changed-comment").val(); 
        let expenseNumber = $('#expense-income-number').val();
        commentValue = $.trim(commentValue);
        let isCommentValid = /^[a-ząćęłńóśźż0-9\040\.\-\/]*$/i.test(commentValue);
        let expenseNumberIsNumeric = $.isNumeric(expenseNumber);
        let expenseNumberIsPositiveInteger = /^[1-9]\d*$/.test(expenseNumber);

        let isButtonDisabled = $('#data-change-button').is(":disabled");

        if (!isCommentValid) 
        { 
            $("#validation-message-jquery").html("Only letters, numbers, space, forward slash, period and dash allowed in the comment"); 

            if (!isButtonDisabled) 
            {
                $('#data-change-button').prop('disabled', true);
            }

            commentCheck = false; 
            return false; 
        } 
        else if (commentValue.length > 50) 
        { 
            $("#validation-message-jquery").html("Length of comment must be between 0 (no comment) and 50"); 

            if (!isButtonDisabled) 
            {
                $('#data-change-button').prop('disabled', true);
            }

            commentCheck = false; 
            return false; 
        } 
        else 
        { 
            $("#validation-message-jquery").empty();

            if (isButtonDisabled && expenseNumberIsNumeric && expenseNumberIsPositiveInteger) 
            {
                $('#data-change-button').prop('disabled', false);
            }
            
            return true;
        } 
    } 

// Submit button 
    $("#data-change-button").click(function () { 
   //     commentCheck = validateComment(); 
        if (commentCheck == true) 
        { 
            return true; 
        } 
        else 
        { 
            $(this).blur();
            return false;
        } 
    }); 
/*    
    $("#comment-modification-form").validate({
        rules: {
            comment: "required"     {
              required: function(element){
                var commentValue = $('#data-to-be-changed-comment').val();
                commentValue = $.trim(commentValue);
                var isCommentValid = /^[a-z0-9\040\.\-\/]+$/i.test(commentValue);
                return !isCommentValid;

              return /^[a-z0-9\040\.\-\/]+$/i.test($('#data-to-be-changed-comment').val());
              }
          }  
        }
      });
*/



/*
    $('#data-change-button').on('click', function(e) {
     
        $("#comment-modification-form").validate({
            rules: {
                comment: {
                  required: function(element){
                    var commentValue = $('#data-to-be-changed-comment').val();
                    commentValue = $.trim(commentValue);
                    var isCommentValid = /^[a-z0-9\040\.\-\/]+$/i.test(commentValue);
                    return !isCommentValid;
                  }
              }
            }
          });

*/

    /* 
        $('#data-to-be-changed-comment').get(0).required = false;

        var commentValue = $('#data-to-be-changed-comment').val();
        commentValue = $.trim(commentValue);
        var isCommentValid = /^[a-z0-9\040\.\-\/]+$/i.test(commentValue);
        
        if (!isCommentValid) {
    */  
    /*    
            $('#data-to-be-changed-comment').get(0).required = true;
            $('#data-to-be-changed-comment').get(0).oninput = function() {this.setCustomValidity('');};
            $('#data-to-be-changed-comment').get(0).oninvalid = function() {this.setCustomValidity('Only letters, numbers, space, forward slash, period and dash allowed in the comment');};
            $('#data-to-be-changed-comment').get(0).reportValidity();
            e.preventDefault();
        

    
    });   */


});    