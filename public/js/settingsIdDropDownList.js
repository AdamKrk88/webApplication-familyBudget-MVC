//have drop down list for expense/income numbers with limited size and scroll bar 

$(document).ready(function()
{

$("#expense-income-number").on('mousedown', function(e) {
	var el = $(e.currentTarget);

    var sizeAttribute = el.attr('size');
	
    if(typeof sizeAttribute !== 'undefined' && sizeAttribute !== false && el.attr('size') == "1"){
    	e.preventDefault();    
    }
});


$("#expense-income-number").on('click', function(e) {
   
    $(this).focus();

    var currentNumberOfItems = $("#expense-income-number option").length;
 	var el = $(e.currentTarget); 

    if (el.attr('size') == "1" && currentNumberOfItems < 5) {
        el.addClass('select-open');
        el.attr('size', currentNumberOfItems.toString());
    }
    else if (el.attr('size') == "1" && currentNumberOfItems >= 5) {
        el.addClass('select-open');
        el.attr('size', "5");
    }
    else {
        el.removeClass('select-open');
        el.attr('size', "1");
    }
});


$("#data-to-be-changed").on('click', function(e) {

    var expenseIdSize = $("#expense-income-number").attr('size');

    if (expenseIdSize > 1)
    {
        $("#expense-income-number").attr('size',"1");
    }

}); 


$("body").on('click', function(e) {



    var arrayOfElementsAllowedToBeClicked = ["DIV","FORM","H2","SPAN","A","BUTTON","HEADER","FOOTER","BODY","IMG"];
    var tagNameClicked = e.target.tagName;

    var isIncludedInArray = $.inArray(tagNameClicked, arrayOfElementsAllowedToBeClicked);
    var expenseIdSize = $("#expense-income-number").attr('size');


    if (isIncludedInArray > -1 && expenseIdSize > 1)
    {
        $("#expense-income-number").attr('size',"1");
    }

}); 


});