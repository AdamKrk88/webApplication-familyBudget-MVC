$(document).ready(function()
{
    //submit button disabled if categories not available 
    if ($('#no-categories').length > 0 || $('#no-payments').length > 0 || $('#no-modifications').length > 0) {
        $('#data-change-button').prop('disabled', true);
    }
});