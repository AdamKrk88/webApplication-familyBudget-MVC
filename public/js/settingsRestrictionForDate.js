$(document).ready(function() {
   
    //get current day of the month
     var dateObject = new Date();
     var month = dateObject.getMonth() + 1;
     var currentDay = dateObject.getDate();
 
     var maxDate = dateObject.getFullYear() + '-' +
     (month<10 ? '0' : '') + month + '-' + currentDay;
 
     //restriction for amount and date fields
     $('#data-to-be-changed').attr('min','2023-01-01');
     $('#data-to-be-changed').attr('max', maxDate);

    //no manual input for date
    $('#data-to-be-changed').keypress(function(e) {
        e.preventDefault();
    });
});