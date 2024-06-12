$(document).ready(function()
{
    $("#data-change-button").click(function()
    {
        if ($("#text-message-paragraph").length) 
        {
            $("#text-message-paragraph").text("");
        }
    });
});