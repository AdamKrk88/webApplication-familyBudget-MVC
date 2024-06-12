$('#delete-confirmation-modal').on('shown.bs.modal', function(e){
    $('#btn-delete').one('focus', function(e){
        $(this).blur();
    });
});

$('#btn-delete-confirmation').on('click', function() {
    $.ajax({
        type: "POST",
        url: "/ajax/delete-account",
        data: "&ajax="+true,
        success: function(deletionResult) {
            var isTrueSet = (deletionResult === 'true');
          //  alert(jQuery.type(deletionResult));
          //  alert(deletionResult);
            if(isTrueSet)
            {
                window.location.href = "/login/redirect-after-deletion"; 
            }
            else
            {
                $('#delete-confirmation-modal').modal('toggle');
                $('#error-message-modal').modal('show'); 
            }
    
        },
        error: function() {
            $('#delete-confirmation-modal').modal('toggle');
            $('#error-message-modal').modal('show');
        }
        
    });
});