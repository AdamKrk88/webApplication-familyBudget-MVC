$(document).ready(function() {
    // Initialize DataTable
    $('#myDataTable').DataTable({
    paging: true, // Enable pagination
    searching: true, // Enable search
    ordering: true, // Enable sorting
    info: true, // Show information
    lengthChange: false, // Disable the "Show X entries" dropdown
    });
 
    $('.pagination').addClass("font-size-scaled-from-15px");

    $('#myDataTable_filter').addClass("mb-2");

    $(document).on('click', "li", function () {
        $('.pagination').addClass("font-size-scaled-from-15px");
    });

    $('input[type=search]').on('input', function(){
        $('.pagination').addClass("font-size-scaled-from-15px");
    });

    $('.sorting').on('click', function(){
        $('.pagination').addClass("font-size-scaled-from-15px");
    });

});