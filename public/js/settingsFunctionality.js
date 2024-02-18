function isElementContainNoCharacters(element) {
    return !element.html().trim();
  }

$(document).ready(function(){
    $("#name-change-button").click(function() {
        if (isElementContainNoCharacters($("#name-change")) && !isElementContainNoCharacters($("#messageForUser"))) {
            $("#messageForUser").html("<a class=\"link-registration-income-expense font-light-orange fst-italic\" href=\"settings.php?customize=User\">" + "<?php if (isset($_SESSION['successMessage'])) {echo "Reload";} ?>" + "</a>");
        }
    });
    
    $("#email-change-button").click(function() {
        if (isElementContainNoCharacters($("#email-change")) && !isElementContainNoCharacters($("#messageForUser"))) {
            $("#messageForUser").html("<a class=\"link-registration-income-expense font-light-orange fst-italic\" href=\"settings.php?customize=User\">" + "<?php if (isset($_SESSION['successMessage'])) {echo "Reload";} ?>" + "</a>");
        }
    });

    $("#password-change-button").click(function() {
        if (isElementContainNoCharacters($("#password-change")) && !isElementContainNoCharacters($("#messageForUser"))) {
            $("#messageForUser").html("<a class=\"link-registration-income-expense font-light-orange fst-italic\" href=\"settings.php?customize=User\">" + "<?php if (isset($_SESSION['successMessage'])) {echo "Reload";} ?>" + "</a>");
        }
    });

    $("#add-category-expense-button").click(function() {
        if (isElementContainNoCharacters($("#add-category-expense")) && !isElementContainNoCharacters($("#messageForExpense"))) {
        $("#messageForExpense").html("<a class=\"link-registration-income-expense font-light-orange fst-italic\" href=\"settings.php?customize=Expense\">" + "<?php if (isset($_SESSION['successMessage'])) {echo "Reload";} ?>" + "</a>");
        }
    });

    $("#add-payment-expense-button").click(function() {
        if (isElementContainNoCharacters($("#add-payment-expense")) && !isElementContainNoCharacters($("#messageForExpense"))) {
        $("#messageForExpense").html("<a class=\"link-registration-income-expense font-light-orange fst-italic\" href=\"settings.php?customize=Expense\">" + "<?php if (isset($_SESSION['successMessage'])) {echo "Reload";} ?>" + "</a>");
        }
    });

    $("#remove-expense-button").click(function() {
        if (isElementContainNoCharacters($("#remove-expense")) && !isElementContainNoCharacters($("#messageForExpense"))) {
        $("#messageForExpense").html("<a class=\"link-registration-income-expense font-light-orange fst-italic\" href=\"settings.php?customize=Expense\">" + "<?php if (isset($_SESSION['successMessage'])) {echo "Reload";} ?>" + "</a>");
        }
    });

    $("#edit-comment-expense-button").click(function() {
        if (isElementContainNoCharacters($("#edit-expense-id-comment")) && !isElementContainNoCharacters($("#messageForExpense"))) {
        $("#messageForExpense").html("<a class=\"link-registration-income-expense font-light-orange fst-italic\" href=\"settings.php?customize=Expense\">" + "<?php if (isset($_SESSION['successMessage'])) {echo "Reload";} ?>" + "</a>");
        }
    });

    $("#edit-category-expense-button").click(function() {
        if (isElementContainNoCharacters($("#edit-expense-id-category")) && !isElementContainNoCharacters($("#messageForExpense"))) {
        $("#messageForExpense").html("<a class=\"link-registration-income-expense font-light-orange fst-italic\" href=\"settings.php?customize=Expense\">" + "<?php if (isset($_SESSION['successMessage'])) {echo "Reload";} ?>" + "</a>");
        }
    });

    $("#add-category-income-button").click(function() {
        if (isElementContainNoCharacters($("#add-category-income")) && !isElementContainNoCharacters($("#messageForIncome"))) {
        $("#messageForIncome").html("<a class=\"link-registration-income-expense font-light-orange fst-italic\" href=\"settings.php?customize=Income\">" + "<?php if (isset($_SESSION['successMessage'])) {echo "Reload";} ?>" + "</a>");
        }
    });

    $("#remove-income-button").click(function() {
        if (isElementContainNoCharacters($("#remove-income")) && !isElementContainNoCharacters($("#messageForIncome"))) {
        $("#messageForIncome").html("<a class=\"link-registration-income-expense font-light-orange fst-italic\" href=\"settings.php?customize=Income\">" + "<?php if (isset($_SESSION['successMessage'])) {echo "Reload";} ?>" + "</a>");
        }
    });

    $("#edit-comment-income-button").click(function() {
        if (isElementContainNoCharacters($("#edit-income-id-comment")) && !isElementContainNoCharacters($("#messageForIncome"))) {
        $("#messageForIncome").html("<a class=\"link-registration-income-expense font-light-orange fst-italic\" href=\"settings.php?customize=Income\">" + "<?php if (isset($_SESSION['successMessage'])) {echo "Reload";} ?>" + "</a>");
        }
    });

    $("#edit-category-income-button").click(function() {
        if (isElementContainNoCharacters($("#edit-income-id-category")) && !isElementContainNoCharacters($("#messageForIncome"))) {
        $("#messageForIncome").html("<a class=\"link-registration-income-expense font-light-orange fst-italic\" href=\"settings.php?customize=Income\">" + "<?php if (isset($_SESSION['successMessage'])) {echo "Reload";} ?>" + "</a>");
        }
    });
});