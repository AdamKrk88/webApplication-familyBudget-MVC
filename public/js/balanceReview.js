	var optionValuePrevious = 1;
	var startDateValue ="0";
	var endDateValue = "0";
	var counterForClickEventRelease = 0;
	var firstOptionClicked = 0;
	var screenWidthPieChartHeight = [];
	var resizingCounter = 0;
	var screenResized = false;
	var pieChart;
	var dataToDisplayList = [];
	


	if (checkIfDataForPieChartExist()) {
		screenWidthPieChartHeight = createPieChart(true);  //insert pieChart and return screen width and pie chart height as array
		var numberOfExpenseCategories = categoryTotalAmountValueForExpense.length;
	
		
		//hide legend if number of categories > 9
		if (numberOfExpenseCategories > 9) {
			pieChart.options['plugins']['legend']['display'] = false;
			pieChart.update();
		}

		//set chart font size to 13 if width >= 1200
		if (window.outerWidth >= 1200) {
			Chart.defaults.font.size = 13;
		}
		
		//set chart font size to 12 if width between 768 and 1200. Use 2/3 height for chart if number of categories > 9 and width < 1000
		if (window.outerWidth >= 768 && window.outerWidth < 1200) {
			Chart.defaults.font.size = 12;
			if (numberOfExpenseCategories > 9 && window.outerWidth < 1000) {
				$('#myChartDiv').css('height',2*screenWidthPieChartHeight[1]/3);
			}
		}

		//set chart font size to 11 if width < 768. Hide legend. Use 1/2 height for chart
		if (window.outerWidth < 768) {
			Chart.defaults.font.size = 11;
			
			if (numberOfExpenseCategories <= 9) {
				pieChart.options['plugins']['legend']['display'] = false;
			}
			
			$('#myChartDiv').css('height',screenWidthPieChartHeight[1]/2);
			pieChart.update();
		}
	}

	//create pie chart object
	function createPieChart(isCallOnload) {
		if($('#myChart').length === 0) {
			$('#myChartDiv').html("<canvas id='myChart'></canvas>");
		}

		pieChart = new Chart($("#myChart"), {
        type: 'pie',
		options: {
			responsive: true,
			maintainAspectRatio: false,
			aspectRatio: 1,
			plugins: {
				legend: {
					display: true
				},
				paddingBelowLegends: false,
				tooltip: {
					enabled: true,
					callbacks: {
						label: (item) => item.parsed + '%'
					}
				}
			}       	
		},
		data: {
            labels: displayCategories(isCallOnload),
            datasets: [
                {
                    backgroundColor: displayBackgroundColor(isCallOnload),	
                    data: displayPercentagesForCategories(isCallOnload),
                }
            ]
        }			
        });

		return provideScreenWidthAndPieChartHeight();
	}

	//check if data for expense exists - check if the is at least one category with amount assigned for expense
	function checkIfDataForPieChartExist() {
	//	var categoryTotalAmountValue = <?= json_encode($categoryTotalAmountValue); ?>;
		if (Array.isArray(categoryTotalAmountValueForExpense) && categoryTotalAmountValueForExpense.length) {
			return true;
		}

		return false;
	}

	//recover pie chart height and settings for legend display
	function recoverPieChartHeight() {
		if (pieChart.data['labels'].length > 9) {
			pieChart.options['plugins']['legend']['display'] = false;
		}
		else if (pieChart.data['labels'].length <= 9 && window.outerWidth >= 768) {
			pieChart.options['plugins']['legend']['display'] = true;
		}
		
		if (window.outerWidth >= 1200) {
			$('#myChartDiv').css('height',screenWidthPieChartHeight[1]);		
		}
			
		if (window.outerWidth >= 768 && window.outerWidth < 1200) {
			if (pieChart.data['labels'].length > 9 && window.outerWidth < 1000) {
				$('#myChartDiv').css('height',2*screenWidthPieChartHeight[1]/3);
			}
			else if (pieChart.data['labels'].length > 9 && window.outerWidth >= 1000) {
				$('#myChartDiv').css('height',screenWidthPieChartHeight[1]);
			}
			else {
				$('#myChartDiv').css('height',screenWidthPieChartHeight[1]);
			}
		}

		if (window.outerWidth < 768) {	
			$('#myChartDiv').css('height',screenWidthPieChartHeight[1]/2);
			pieChart.options['plugins']['legend']['display'] = false;	
		}

		pieChart.update();
	}
	
	//return expense categories if true for isCallInLoad. Otherwise return empty array
	function displayCategories(isCallOnload) {
		var categories = [];

		if (isCallOnload) {
			var numberOfCategories = categoryTotalAmountValueForExpense.length;
			var categoryTotalAmountValue = categoryTotalAmountValueForExpense;

			for (let i = 0; i < numberOfCategories; i++) {
				categories[i] = categoryTotalAmountValue[i][0];
			}

			return categories;
		}
		else {
			return categories;
		}
	}
 
	//return array for colors if argument isCallOnload is true. Otherwise return empty array
	function displayBackgroundColor(isCallOnload) {
		var colors = [];
		
		if (isCallOnload) {
			var numberOfCategories = categoryTotalAmountValueForExpense.length;

			for (let i = 0; i < numberOfCategories; i++) {
				switch(i) {
					case 0:
						colors[i] = "#ffccff";
						break;
					case 1:
						colors[i] = "#bf80ff";
						break;
					case 2:
						colors[i] = "#ff80ff";
						break;
					case 3:
						colors[i] = "#df9fbf";
						break;
					case 4:
						colors[i] = "#ff80bf";
						break;
					case 5:
						colors[i] = "#ff80aa";
						break;
					case 6:
						colors[i] = "#df9f9f";
						break;
					case 7:
						colors[i] = "#ff8080";
						break;
					case 8:
						colors[i] = "#ffbf80";
						break;
					case 9:
						colors[i] = "#ffdf80";
						break;
					case 10:
						colors[i] = "#dfff80";
						break;
					case 11:
						colors[i] = "#80ff80";
						break;
					case 12:
						colors[i] = "#80ffe5";
						break;
					case 13:
						colors[i] = "#80ccff";
						break;
					case 14:
						colors[i] = "#8080ff";
						break;
					case 15:
						colors[i] = "#b3b3cc";
						break;
					case 16:
						colors[i] = "#9fbfdf";
						break;
					case 17:
						colors[i] = "#80bfff";
				}
			}
			
			return colors;
		}
		else {
			return colors;
		}
	}

	//return percentage for expense category in form of one-dimensional array
	function displayPercentagesForCategories(isCallOnload) {
		var percentages = [];
		
		if (isCallOnload) {
			var numberOfCategories = categoryTotalAmountValueForExpense.length;
		//	var totalExpense = totalExpense;
			var categoryTotalAmountValue = categoryTotalAmountValueForExpense;
			var percentagePerCategory = 0;
			
			for (let i = 0; i < numberOfCategories; i++) {
				percentagePerCategory = (parseFloat(categoryTotalAmountValue[i][1]) / totalExpense) * 100;
				percentages[i] = percentagePerCategory.toFixed(2);
			}

			return percentages;
		}
		else {
			return percentages;
		}
	}

	//get specific number of character(s) from the left
	String.prototype.left = function(n) {
    	return this.substring(0, n);
	}
	
	function getCurrentOption() {
		var optionValueCurrent = $(this).val();
		if (optionValueCurrent != 4) {
			optionValuePrevious = optionValueCurrent;
		}
	}

	function getFirstClickOption(that) {
		if (counterForClickEventRelease % 2 == 1) {
			firstOptionClicked = $(that).val();
		}
	}

	function getSelectedOptionFromDropDownList(that) {
		return $(that).children("option:selected").val(); 
	}

	function isElementEmpty(element) {
    	return !$.trim(element.html())
  	}

	function isElementHasDisabledAttributeOn(attribute) {
		if (typeof(attribute) !== 'undefined' && attribute !== false) {
			return true;
		} 
		return false;
	}

	// fileName deleted
	function switchIncomeExpenseSummary(cashFlowType, timePeriodSelectedByUser, isFromDropDownList, isFromChartListSwitcher, isModal, startDateFromModal ='0', endDateFromModal = '0') {
	  	$.ajax({
			async: false,
			url: "/ajax/switchBetweenIncomeExpense",   
			type: 'POST',
			data: {
				'timePeriod': timePeriodSelectedByUser, 
				'isModal': isModal,
				'startDateFromModal': startDateFromModal,
				'endDateFromModal': endDateFromModal,
				'incomeOrExpense': cashFlowType,
				'ajax': true
			},
			success: function(incomeOrExpenseData) {
			
				var json = JSON.parse(incomeOrExpenseData);
				var numberOfIncomeOrExpenseCategories = Object.keys(json).length;
				var checkIfPaddingIsAdded = false;
			
				if (!isFromDropDownList && !isFromChartListSwitcher) {
					if (cashFlowType == "income") {
						$('#presented-table-name').html('Incomes <span class="position-absolute top-50 end-0 translate-middle-y font-size-scaled-from-13px py-1 pe-2" id="date-for-your-choice"></span><a class="position-absolute top-50 start-0 translate-middle-y font-size-scaled-from-15px link-registration-income-expense font-color-black py-1 ps-2" href="" id="chart-list-switcher"><em>Chart / List Switcher</em></a>');
					
						if (startDateFromModal != '0' && endDateFromModal != '0' && (startDateFromModal == endDateFromModal)) {
							$('#date-for-your-choice').html('one day  ' + startDateFromModal);
						}
						else if (startDateFromModal != '0' && endDateFromModal != '0') {
							$('#date-for-your-choice').html('from  ' + startDateFromModal + '  to  ' + endDateFromModal);
						}
						else {
							$('#date-for-your-choice').html('');
						}
						$('#switcher-incomeLink-presentedInformation').html('Presented');
						$('#switcher-expenseLink-presentedInformation').html('Click <a class="font-light-stronger-orange link-registration-income-expense" id="linkToPresentExpenses" href="">here</a>');
					}
					else if (cashFlowType == "expense") {
						$('#presented-table-name').html('Expenses <span class="position-absolute top-50 end-0 translate-middle-y font-size-scaled-from-13px py-1 pe-2" id="date-for-your-choice"></span><a class="position-absolute top-50 start-0 translate-middle-y font-size-scaled-from-15px link-registration-income-expense font-color-black py-1 ps-2" href="" id="chart-list-switcher"><em>Chart / List Switcher</em></a>');
				
						if (startDateFromModal != '0' && endDateFromModal != '0' && (startDateFromModal == endDateFromModal)) {
							$('#date-for-your-choice').html('one day  ' + startDateFromModal);
						}
						else if (startDateFromModal != '0' && endDateFromModal != '0') {
							$('#date-for-your-choice').html('from  ' + startDateFromModal + '  to  ' + endDateFromModal);
						}
						else {
							$('#date-for-your-choice').html('');
						}
						$('#switcher-expenseLink-presentedInformation').html('Presented');
						$('#switcher-incomeLink-presentedInformation').html('Click <a class="font-light-stronger-orange link-registration-income-expense" id="linkToPresentIncomes" href="">here</a>');
					}
				}
				else if (isFromDropDownList && !isModal) {
					$('#date-for-your-choice').html('');
				}

				if (isModal && !isFromChartListSwitcher) {
					if (cashFlowType == "income") {
						$('#presented-table-name').html('Incomes <span class="position-absolute top-50 end-0 translate-middle-y font-size-scaled-from-13px py-1 pe-2" id="date-for-your-choice"></span><a class="position-absolute top-50 start-0 translate-middle-y font-size-scaled-from-15px link-registration-income-expense font-color-black py-1 ps-2" href="" id="chart-list-switcher"><em>Chart / List Switcher</em></a>');
				
						if (startDateFromModal != '0' && endDateFromModal != '0' && (startDateFromModal == endDateFromModal)) {
							$('#date-for-your-choice').html('one day  ' + startDateFromModal);
						}
						else if (startDateFromModal != '0' && endDateFromModal != '0') {
							$('#date-for-your-choice').html('from  ' + startDateFromModal + '  to  ' + endDateFromModal);
						}
						else {
							$('#date-for-your-choice').html('');
						}
					}

					else if (cashFlowType == "expense") {
						$('#presented-table-name').html('Expenses <span class="position-absolute top-50 end-0 translate-middle-y font-size-scaled-from-13px py-1 pe-2" id="date-for-your-choice"></span><a class="position-absolute top-50 start-0 translate-middle-y font-size-scaled-from-15px link-registration-income-expense font-color-black py-1 ps-2" href="" id="chart-list-switcher"><em>Chart / List Switcher</em></a>');
			
						if (startDateFromModal != '0' && endDateFromModal != '0' && (startDateFromModal == endDateFromModal)) {
							$('#date-for-your-choice').html('one day  ' + startDateFromModal);
						}
						else if (startDateFromModal != '0' && endDateFromModal != '0') {
							$('#date-for-your-choice').html('from  ' + startDateFromModal + '  to  ' + endDateFromModal);
						}
						else {
							$('#date-for-your-choice').html('');
						}
					}
				}

				if (Array.isArray(json) && json.length) {
					$('#noDataComment').html('');
					if (pieChart === undefined) {
						screenWidthPieChartHeight = createPieChart(false);
					}

					for (let i = 0; i < 18; i++) {
						if (i < numberOfIncomeOrExpenseCategories) {
							$('#th' + i).html(json[i][0]);
							$('#td' + i).html(json[i][1]); 
							checkIfPaddingIsAdded = $('#th' + i).hasClass("p-0");
						
							if (checkIfPaddingIsAdded) {
								$('#th' + i).removeClass('p-0');
								$('#td' + i).removeClass('p-0');
							}
						}
						else {
							$('#th' + i).html("");
							$('#td' + i).html(""); 
							checkIfPaddingIsAdded = $('#th' + i).hasClass("p-0");
							if (!checkIfPaddingIsAdded) {
								$('#th' + i).addClass('p-0');
								$('#td' + i).addClass('p-0');
							}
						}
					}
				}
				else {
					$('#noDataComment').html('Nothing to show');
					for (let i = 0; i < 18; i++) {
						$('#th' + i).html("");
						$('#td' + i).html(""); 
						checkIfPaddingIsAdded = $('#th' + i).hasClass("p-0");
							if (!checkIfPaddingIsAdded) {
								$('#th' + i).addClass('p-0');
								$('#td' + i).addClass('p-0');
							}
					}
				}
			}
		}).fail(function(incomeOrExpenseData) {
			alert("Something went wrong. Error");
		});
	}

	function getTotalBalance(timePeriodSelectedByUser, isModal, startDateFromModal ='0', endDateFromModal = '0') {
		$.ajax({
			url: "/ajax/calculateTotalBalance",   
			type: 'POST',
			data: {
				'timePeriod': timePeriodSelectedByUser, 
				'isModal': isModal,
				'startDateFromModal': startDateFromModal,
				'endDateFromModal': endDateFromModal,
				'ajax': true
			},
			success: function(balance) {
				var json = JSON.parse(balance);
				$('#total-balance').html(json);
				if (json > 0) {
					$('#balance-comment').html('Congratulations. You are focused on efficiency in financial management');
				}
				else if(json < 0) {
					$('#balance-comment').html('Your balance is below zero. Review your budget');
				}
				else {
					$('#balance-comment').html('Balance is equal to zero');
				}
			}
		}).fail(function(balance) {
			alert("Something went wrong. Error");
		});
	}

	function updatePieChart(pieChart, expenseOrIncome, timePeriodSelectedByUser, isModal, startDateFromModal = "0", endDateFromModal = "0") {
		$.ajax({
			async: false,
			url: "/ajax/updatePieChart",   
			type: 'POST',
			data: {
				'expenseOrIncome': expenseOrIncome,
				'timePeriod': timePeriodSelectedByUser, 
				'isModal': isModal,
				'startDateFromModal': startDateFromModal,
				'endDateFromModal': endDateFromModal,
				'ajax': true
			},
			success: function(dataToUpdatePieChart) {
				var json = JSON.parse(dataToUpdatePieChart);
			
				pieChart.data['labels'] = json['incomeCategories'];
				pieChart.data['datasets'][0]['backgroundColor'] = json['backgroundColorForPieChart'];
				pieChart.data['datasets'][0]['data'] = json['percentagePerCategory'];

				pieChart.update();
				
				if (Array.isArray(json['incomeCategories']) && json['incomeCategories'].length === 0) {
					$('#myChartDiv').css('height', 0);
				}
				else {
					recoverPieChartHeight();
				}		
			}
		}).fail(function(dataToUpdatePieChart) {
			alert("Something went wrong. Error");
		});
	}	

	function cleanList() {
		var typeOfDataReviewed = $('#presented-table-name').text().left(8).trim();
		$('#id-list').html("");
		$('#date-list').html("");
		$('#category-list').html("");
		$('#comment-list').html("");
		$('#amount-list').html("");
		
		if (typeOfDataReviewed == "Expenses") {
			$('#payment-list').html("");

			for (let i = 0; i < dataToDisplayList.length && i < 7; i++) {
				$("#th-id-" + i).addClass('p-0').html("");
				$("#td-date-" + i).addClass('p-0').html("");
				$("#td-category-" + i).addClass('p-0').html("");
				$("#td-payment-" + i).addClass('p-0').html("");
				$("#td-comment-" + i).addClass('p-0').html("");
				$("#td-amount-" + i).addClass('p-0').html("");
			}
		}
		else {
			for (let i = 0; i < dataToDisplayList.length && i < 7; i++) {
				$("#th-id-" + i).addClass('p-0').html("");
				$("#td-date-" + i).addClass('p-0').html("");
				$("#td-category-" + i).addClass('p-0').html("");
				$("#td-comment-" + i).addClass('p-0').html("");
				$("#td-amount-" + i).addClass('p-0').html("");
			}
		}
	}

	function addZeroToDayOrMonthIfNecessaryAndConvertToString(number) {
		if (number < 10) {
			number = "0" + number;
		}
		else {
			number = number.toString();
		}
		return number
	}
	
	function convertDateFromInputToNumber(dateObject) {		
		var dayNumber = dateObject.getDate();
		var day = addZeroToDayOrMonthIfNecessaryAndConvertToString(dayNumber);
		var monthNumber = dateObject.getMonth() + 1;
		var month = addZeroToDayOrMonthIfNecessaryAndConvertToString(monthNumber);
		var year = dateObject.getFullYear().toString();
		var dateAsString = year + month + day;
		return parseInt(dateAsString);
	}
	
	function clearModalBoxToDefault() {
		$("#startDate").removeAttr("style");
		$("#endDate").removeAttr("style");
		$("#errorMessage").html("");	
		$("#endDate").val("");
		$("#startDate").val("");
	}	
	
	function returnCurrentDateAsNumber() {
		var currentDateObject=new Date();
		var currentDateAsInteger = convertDateFromInputToNumber(currentDateObject);
		return currentDateAsInteger;	
	}	
	
	function checkDateInModal() {	
		startDateValue =$("#startDate").val();  
		endDateValue = $("#endDate").val();
		if (startDateValue != "" && endDateValue != "") {
			var startDate = new Date($("#startDate").val());
			var endDate = new Date($("#endDate").val());
			var startDateAsInteger = convertDateFromInputToNumber(startDate);
			var endDateAsInteger = convertDateFromInputToNumber(endDate);
			var currentDateAsInteger = returnCurrentDateAsNumber();
			var backgroundColorForEndDateInput = $("#endDate").css("background-color");
			var backgroundColorForStartDateInput = $("#startDate").css("background-color");
		}
	
		if (startDateValue == "" || endDateValue == "") { 
			$("#errorMessage").addClass("font-red").removeClass("font-green").html("Provide dates");
			return false;
		}
		
		else if (startDateAsInteger > currentDateAsInteger && endDateAsInteger > currentDateAsInteger ) {
			$("#startDate").css("background-color","#ff8080");
			$("#endDate").css("background-color","#ff8080");
			$("#errorMessage").addClass("font-red").removeClass("font-green").html("Both dates greater than current date");
			return false;
		}
		
		else if (startDateAsInteger > currentDateAsInteger) {
			$("#startDate").css("background-color","#ff8080");
			$("#endDate").removeAttr("style");
			$("#errorMessage").addClass("font-red").removeClass("font-green").html("Start date greater than current date");
			return false;
		}
		
		else if (endDateAsInteger > currentDateAsInteger) {
			$("#endDate").css("background-color","#ff8080");
			$("#startDate").removeAttr("style");
			$("#errorMessage").addClass("font-red").removeClass("font-green").html("End date greater than current date");
			return false;
		}
		
		else if (endDateAsInteger < startDateAsInteger) {
			$("#startDate").css("background-color","#ff8080");
			$("#endDate").removeAttr("style");
			$("#errorMessage").addClass("font-red").removeClass("font-green").html("Start date greater than end date");	
			return false;
		}
		
		if (endDateAsInteger >= startDateAsInteger && backgroundColorForEndDateInput == "rgb(255, 255, 255)" && backgroundColorForStartDateInput == "rgb(255, 255, 255)") {
			$("#errorMessage").addClass("font-green").removeClass("font-red").html("Correct");	
		}
		
		else if (endDateAsInteger >= startDateAsInteger && (backgroundColorForEndDateInput == "rgb(255, 128, 128)" || backgroundColorForStartDateInput == "rgb(255, 128, 128)")) {
			$("#startDate").removeAttr("style");
			$("#endDate").removeAttr("style");
			$("#errorMessage").addClass("font-green").removeClass("font-red").html("Now it is correct");	
		}

		return true;
	}

	function capitalizeFirstLetter(string){
        return string.charAt(0).toUpperCase() + string.slice(1);
    }

	function provideNumberOfCategories(arrayOfUserIncomesOrExpenses) {
		var arrayLength = arrayOfUserIncomesOrExpenses.length;
		var categories = [];
		if (Array.isArray(arrayOfUserIncomesOrExpenses) && arrayOfUserIncomesOrExpenses.length) {
			categories[0] = arrayOfUserIncomesOrExpenses[0]['category'];

			for (let i = 1; i < arrayLength; i++) {
				
				if($.inArray(arrayOfUserIncomesOrExpenses[i]['category'], categories) === -1) {
					categories.push(arrayOfUserIncomesOrExpenses[i]['category']);
				}
			}
		}

		return categories.length;
	}

	//provide first page for incomes or expenses list
	function getOnePage(expenseOrIncome, timePeriodSelectedByUser, isModal, startDateFromModal = '0', endDateFromModal = '0') {
		$.ajax({
			async: false,
			url: "/ajax/provideAllItemsForExpenseOrIncome",   
			type: 'POST',
			data: {
				'expenseOrIncome': expenseOrIncome,
				'timePeriod': timePeriodSelectedByUser, 
				'isModal': isModal,
				'startDateFromModal': startDateFromModal,
				'endDateFromModal': endDateFromModal,
				'ajax': true
			}, 
			success: function(dataToUpdateFirstPage) {
				var json = JSON.parse(dataToUpdateFirstPage);
			
				if (Array.isArray(json) && json.length) {
					dataToDisplayList = json;
					var numberOfCategories = provideNumberOfCategories(json);
					var tableHeader = Object.keys(json[0]);
				
					pieChart.data['labels'] = [];
					pieChart.data.datasets[0]['backgroundColor'] = [];
					pieChart.data.datasets[0]['data'] = [];
					pieChart.update();
					$('#myChartDiv').css('height', 0);	
					$("#myChart").remove();

					for (let i = 0; i < numberOfCategories; i++) {
						$('#th' + i).html("");
						$('#td' + i).html(""); 
						$('#th' + i).addClass('p-0');
						$('#td' + i).addClass('p-0');
					}

					if (expenseOrIncome == "Expense") {
						if (!$('#payment-list').length) {
							$('#category-list').after('<th scope="col" class="p-0 fw-bolder" style="width:16%" id="payment-list"></th>');
							$('#category-list').css('width', '16%');
							$('#comment-list').css('width', '30%');
							$('#amount-list').css('width', '13%');
							
							var className = "";
							for (let i = 0; i < 7; i++) {
								className = 'td-payment-' + i;
								$("#td-category-" + i).after('<td id=' + className + ' class="p-0"></td>');
							}
						}
						$('#id-list').html(tableHeader[0].toUpperCase());
						$('#date-list').html(capitalizeFirstLetter(tableHeader[1]));
						$('#category-list').html(capitalizeFirstLetter(tableHeader[2]));
						$('#payment-list').html(capitalizeFirstLetter(tableHeader[3]));
						$('#comment-list').html(capitalizeFirstLetter(tableHeader[4]));
						$('#amount-list').html(capitalizeFirstLetter(tableHeader[5]));

						for (let i = 0; i < json.length && i < 7; i++) {
							$("#th-id-" + i).removeClass('p-0').html(json[i]['id']);
							$("#td-date-" + i).removeClass('p-0').html(json[i]['date']);
							$("#td-category-" + i).removeClass('p-0').html(json[i]['category']);
							$("#td-payment-" + i).removeClass('p-0').html(json[i]['payment']);
							$("#td-comment-" + i).removeClass('p-0').html(json[i]['comment']);
							$("#td-amount-" + i).removeClass('p-0').html(json[i]['amount']);
						}
					}
					else if (expenseOrIncome == "Income") {
						if ($('#payment-list').length) {
							
							for (let i = 0; i < 7; i++) {
								$("#td-payment-" + i).remove();
							}
							
							$('#payment-list').remove();

							$('#category-list').css('width', '19%');
							$('#comment-list').css('width', '40%');
							$('#amount-list').css('width', '16%');
						}

						$('#id-list').html(tableHeader[0].toUpperCase());
						$('#date-list').html(capitalizeFirstLetter(tableHeader[1]));
						$('#category-list').html(capitalizeFirstLetter(tableHeader[2]));
						$('#comment-list').html(capitalizeFirstLetter(tableHeader[3]));
						$('#amount-list').html(capitalizeFirstLetter(tableHeader[4]));

						for (let i = 0; i < json.length && i < 7; i++) {
							$("#th-id-" + i).removeClass('p-0').html(json[i]['id']);
							$("#td-date-" + i).removeClass('p-0').html(json[i]['date']);
							$("#td-category-" + i).removeClass('p-0').html(json[i]['category']);
							$("#td-comment-" + i).removeClass('p-0').html(json[i]['comment']);
							$("#td-amount-" + i).removeClass('p-0').html(json[i]['amount']);
						}	
					}
					
					if (json.length > 7) {
						$('#next-link').html('Next');
					}
				}
			}
		}).fail(function(dataToUpdateFirstPage) {
			alert("Something went wrong. Error");
		});
	}
	
	function updateInformationForNextPageInTheList(itemPositionOnTheList, numberOfItemsToBeUpdated, isExpenseList) {
		if (isExpenseList) {
			for (let i = 0, j = itemPositionOnTheList; i < numberOfItemsToBeUpdated; i++) {
				$("#th-id-" + i).html(dataToDisplayList[j]['id']);
				$("#td-date-" + i).html(dataToDisplayList[j]['date']);
				$("#td-category-" + i).html(dataToDisplayList[j]['category']);
				$("#td-payment-" + i).html(dataToDisplayList[j]['payment']);
				$("#td-comment-" + i).html(dataToDisplayList[j]['comment']);
				$("#td-amount-" + i).html(dataToDisplayList[j]['amount']);
				j++;
			}
		}
		else {
			for (let i = 0, j = itemPositionOnTheList; i < numberOfItemsToBeUpdated; i++) {
				$("#th-id-" + i).html(dataToDisplayList[j]['id']);
				$("#td-date-" + i).html(dataToDisplayList[j]['date']);
				$("#td-category-" + i).html(dataToDisplayList[j]['category']);
				$("#td-comment-" + i).html(dataToDisplayList[j]['comment']);
				$("#td-amount-" + i).html(dataToDisplayList[j]['amount']);
				j++;
			}
		}

		if (numberOfItemsToBeUpdated < 7 && isExpenseList) {
			for (let i = numberOfItemsToBeUpdated; i < 7; i++) {
				$("#th-id-" + i).addClass('p-0').html("");
				$("#td-date-" + i).addClass('p-0').html("");
				$("#td-category-" + i).addClass('p-0').html("");
				$("#td-payment-" + i).addClass('p-0').html("");
				$("#td-comment-" + i).addClass('p-0').html("");
				$("#td-amount-" + i).addClass('p-0').html("");
			}
		}
		else if (numberOfItemsToBeUpdated < 7) {
			for (let i = numberOfItemsToBeUpdated; i < 7; i++) {
				$("#th-id-" + i).addClass('p-0').html("");
				$("#td-date-" + i).addClass('p-0').html("");
				$("#td-category-" + i).addClass('p-0').html("");
				$("#td-comment-" + i).addClass('p-0').html("");
				$("#td-amount-" + i).addClass('p-0').html("");
			}
		}
	}

	function updateInformationForPreviousPageInTheList(itemPositionOnTheList, numberOfItemsToBeRestored, isExpenseList) {
		if (numberOfItemsToBeRestored > 0 && isExpenseList) {
			for (let i = 7 - numberOfItemsToBeRestored; i < 7; i++) {
				$("#th-id-" + i).removeClass('p-0');
				$("#td-date-" + i).removeClass('p-0');
				$("#td-category-" + i).removeClass('p-0');
				$("#td-payment-" + i).removeClass('p-0');
				$("#td-comment-" + i).removeClass('p-0');
				$("#td-amount-" + i).removeClass('p-0');
			}
		}
		else if (numberOfItemsToBeRestored > 0) {
			for (let i = 7 - numberOfItemsToBeRestored; i < 7; i++) {
				$("#th-id-" + i).removeClass('p-0');
				$("#td-date-" + i).removeClass('p-0');
				$("#td-category-" + i).removeClass('p-0');
				$("#td-comment-" + i).removeClass('p-0');
				$("#td-amount-" + i).removeClass('p-0');
			}
		}

		if (isExpenseList) {
			for (let i = 0, j = itemPositionOnTheList; i < 7; i++) {
				$("#th-id-" + i).html(dataToDisplayList[j]['id']);
				$("#td-date-" + i).html(dataToDisplayList[j]['date']);
				$("#td-category-" + i).html(dataToDisplayList[j]['category']);
				$("#td-payment-" + i).html(dataToDisplayList[j]['payment']);
				$("#td-comment-" + i).html(dataToDisplayList[j]['comment']);
				$("#td-amount-" + i).html(dataToDisplayList[j]['amount']);
				j++;
			}
		}
		else {
			for (let i = 0, j = itemPositionOnTheList; i < 7; i++) {
				$("#th-id-" + i).html(dataToDisplayList[j]['id']);
				$("#td-date-" + i).html(dataToDisplayList[j]['date']);
				$("#td-category-" + i).html(dataToDisplayList[j]['category']);
				$("#td-comment-" + i).html(dataToDisplayList[j]['comment']);
				$("#td-amount-" + i).html(dataToDisplayList[j]['amount']);
				j++;
			}
		}
	}

	function resizeFontsForPieChart() {
		
		if (pieChart !== undefined && pieChart !== null) {
			if (window.outerWidth >= 1200) {
				Chart.defaults.font.size = 13;
				if (pieChart.data['labels'].length <= 9) {
					pieChart.options['plugins']['legend']['display'] = true;
				}
				if (Array.isArray(pieChart.data['labels']) && pieChart.data['labels'].length) {	
					$('#myChartDiv').css('height',screenWidthPieChartHeight[1]);
				}
			}
			
			if (window.outerWidth >= 768 && window.outerWidth < 1200) {
				Chart.defaults.font.size = 12;
				if (pieChart.data['labels'].length <= 9) {
					pieChart.options['plugins']['legend']['display'] = true;
				}
				if (pieChart.data['labels'].length > 9 && window.outerWidth < 1000) {
					$('#myChartDiv').css('height',2*screenWidthPieChartHeight[1]/3);
				}
				if (Array.isArray(pieChart.data['labels']) && pieChart.data['labels'].length <= 9) {	
					$('#myChartDiv').css('height',screenWidthPieChartHeight[1]);
				}
				if (pieChart.data['labels'].length > 9 && window.outerWidth >= 1000) {	
					$('#myChartDiv').css('height',screenWidthPieChartHeight[1]);
				}
			}

			if (window.outerWidth < 768) {
				Chart.defaults.font.size = 11;
				pieChart.options['plugins']['legend']['display'] = false;
				if (Array.isArray(pieChart.data['labels']) && pieChart.data['labels'].length) {	
					$('#myChartDiv').css('height',screenWidthPieChartHeight[1]/2);
				}
			}

			pieChart.update();
		}
	}

	//return screen width and pie chart height
	function provideScreenWidthAndPieChartHeight() {
			return [$(window).width(), $('#myChartDiv').height()];
	}

	
	
	$(document).ready(function(){

		$(window).on("resize", function() {
			resizeFontsForPieChart();
		});
	
		$('#periodForBalanceSummary').change(function() { 
			var optionValueNew = getSelectedOptionFromDropDownList(this);
			var typeOfDataReviewed = $('#presented-table-name').text().left(8).trim();
			var isPieChartDisplayed = isElementEmpty($('#id-list'));

			if (!isPieChartDisplayed && optionValueNew != "4") {
				cleanList();
				createPieChart(false);
			}

			if (!isElementEmpty($('#next-link'))) {
				$('#next-link').html('');
			}

			if (!isElementEmpty($('#previous-link'))) {
				$('#previous-link').html('');
			}
		
			startDateValue ="0";
			endDateValue = "0";
		
			if (optionValueNew == "1" && typeOfDataReviewed== "Expenses") {
				switchIncomeExpenseSummary('expense', 'isCurrentMonthDate', true, false, false);
				getTotalBalance('isCurrentMonthDate', false);
				updatePieChart(pieChart, 'Expense', 'isCurrentMonthDate', false);
				
			}
			else if (optionValueNew == "2" && typeOfDataReviewed== "Expenses") {
				switchIncomeExpenseSummary('expense', 'isPreviousMonthDate', true, false, false);
				getTotalBalance('isPreviousMonthDate', false);
				updatePieChart(pieChart, 'Expense', 'isPreviousMonthDate', false);
			}
			else if (optionValueNew == "3" && typeOfDataReviewed== "Expenses") {
				switchIncomeExpenseSummary('expense', 'isCurrentYearDate', true, false, false);
				getTotalBalance('isCurrentYearDate', false);
				updatePieChart(pieChart, 'Expense', 'isCurrentYearDate', false);
			}
			else if (optionValueNew == "1" && typeOfDataReviewed== "Incomes") {
				switchIncomeExpenseSummary('income', 'isCurrentMonthDate', true, false, false);
				getTotalBalance('isCurrentMonthDate', false);
				updatePieChart(pieChart, 'Income', 'isCurrentMonthDate', false);
			}
			else if (optionValueNew == "2" && typeOfDataReviewed== "Incomes") {
				switchIncomeExpenseSummary('income', 'isPreviousMonthDate', true, false, false);
				getTotalBalance('isPreviousMonthDate', false);
				updatePieChart(pieChart, 'Income', 'isPreviousMonthDate', false);
			}
			else if (optionValueNew == "3" && typeOfDataReviewed== "Incomes") {
				switchIncomeExpenseSummary('income', 'isCurrentYearDate', true, false, false);
				getTotalBalance('isCurrentYearDate', false);
				updatePieChart(pieChart, 'Income', 'isCurrentYearDate', false);
			}
		});

		$('#periodForBalanceSummary').click(function() {
			var currentOptionSelected = $(this).val();
			
			counterForClickEventRelease++;
			getFirstClickOption(this);
		
			if (currentOptionSelected == "4" && counterForClickEventRelease > 1) {
				$('#boxToProvidePeriodForBalanceSummary').modal("show");
				const disabledAttributeForButtonsInModal = [$("#closeModalSymbol").attr("disabled"), $("#closeModalButton").attr("disabled"), $("#submitModalButton").attr("disabled"), $("#startDate").attr("disabled"), $("#endDate").attr("disabled")];
		
				for (let i = 0; i < disabledAttributeForButtonsInModal.length; i++ ) {
					if (isElementHasDisabledAttributeOn(disabledAttributeForButtonsInModal[i])) {
						switch (i) {
							case 0:
								$("#closeModalSymbol").removeAttr("disabled");
								break;
							case 1:
								$("#closeModalButton").removeAttr("disabled");
								break;
							case 2:
								$("#submitModalButton").removeAttr("disabled");
								break;
							case 3:
								$("#startDate").removeAttr("disabled");
								break;
							case 4:
								$("#endDate").removeAttr("disabled");
						}
					}
				}

				counterForClickEventRelease = 0;
			}
		});
		
		$("#closeModalSymbol").click(function() {
			$('#periodForBalanceSummary').val(firstOptionClicked);
		});
		
		$("#closeModalButton").click(function() {
			$('#periodForBalanceSummary').val(firstOptionClicked);  
		});

		$("#switcher-expenseLink-presentedInformation").on("click","#linkToPresentExpenses", function(e) {
			e.preventDefault();
			var selectedOptionFromDropDownList = getSelectedOptionFromDropDownList('#periodForBalanceSummary');
			var isPieChartDisplayed = isElementEmpty($('#id-list'));

			if (!isPieChartDisplayed) {
				cleanList();
				createPieChart(false);
			}

			if (!isElementEmpty($('#next-link'))) {
				$('#next-link').html('');
			}

			if (!isElementEmpty($('#previous-link'))) {
				$('#previous-link').html('');
			}

			if (selectedOptionFromDropDownList == "1") {
				switchIncomeExpenseSummary('expense', 'isCurrentMonthDate', false, false, false);
				updatePieChart(pieChart, 'Expense', 'isCurrentMonthDate', false);
			}
			else if (selectedOptionFromDropDownList == "2") {
				switchIncomeExpenseSummary('expense', 'isPreviousMonthDate', false, false, false);
				updatePieChart(pieChart, 'Expense', 'isPreviousMonthDate', false);
			}
			else if (selectedOptionFromDropDownList == "3") {
				switchIncomeExpenseSummary('expense', 'isCurrentYearDate', false, false, false);
				updatePieChart(pieChart, 'Expense', 'isCurrentYearDate', false);
			}
			else if (selectedOptionFromDropDownList == "4") {
				switchIncomeExpenseSummary('expense', 'isDateFromModal', false, false, true, startDateValue, endDateValue);
				updatePieChart(pieChart, 'Expense', 'isDateFromModal', true, startDateValue, endDateValue);
			}
		}); 

		$("#switcher-incomeLink-presentedInformation").on("click", "#linkToPresentIncomes", function(e) {
			e.preventDefault();
			var selectedOptionFromDropDownList = getSelectedOptionFromDropDownList('#periodForBalanceSummary');
			var isPieChartDisplayed = isElementEmpty($('#id-list'));

			if (!isPieChartDisplayed) {
				cleanList();
				createPieChart(false);
			}

			if (!isElementEmpty($('#next-link'))) {
				$('#next-link').html('');
			}

			if (!isElementEmpty($('#previous-link'))) {
				$('#previous-link').html('');
			}

			if (selectedOptionFromDropDownList == "1") {
				switchIncomeExpenseSummary('income', 'isCurrentMonthDate', false, false, false);
				updatePieChart(pieChart, 'Income', 'isCurrentMonthDate', false);
			}
			else if (selectedOptionFromDropDownList == "2") {
				switchIncomeExpenseSummary('income', 'isPreviousMonthDate', false, false, false);
				updatePieChart(pieChart, 'Income', 'isPreviousMonthDate', false);
			}
			else if (selectedOptionFromDropDownList == "3") {
				switchIncomeExpenseSummary('income', 'isCurrentYearDate', false, false, false);
				updatePieChart(pieChart, 'Income', 'isCurrentYearDate', false);
			}
			else if (selectedOptionFromDropDownList == "4") {
				switchIncomeExpenseSummary('income', 'isDateFromModal', false, false, true, startDateValue, endDateValue);
				updatePieChart(pieChart, 'Income', 'isDateFromModal', true, startDateValue, endDateValue);
			}
		});

		$("#presented-table-name").on("click","#chart-list-switcher", function(e) {
			e.preventDefault(); 
			var typeOfDataReviewed = $('#presented-table-name').text().left(8).trim();
			var selectedOptionFromDropDownList = getSelectedOptionFromDropDownList('#periodForBalanceSummary');
			var isPieChartDisplayed = isElementEmpty($('#id-list'));
			
			if (isPieChartDisplayed) {
				if (selectedOptionFromDropDownList == "1" && typeOfDataReviewed== "Expenses") {
					getOnePage('Expense', 'isCurrentMonthDate', false);
				}
				else if (selectedOptionFromDropDownList == "2" && typeOfDataReviewed== "Expenses") {
					getOnePage('Expense', 'isPreviousMonthDate', false);
				}
				else if (selectedOptionFromDropDownList == "3" && typeOfDataReviewed== "Expenses") {
					getOnePage('Expense', 'isCurrentYearDate', false);
				}
				else if (selectedOptionFromDropDownList == "4" && typeOfDataReviewed== "Expenses") {
					getOnePage('Expense', 'isDateFromModal', true, startDateValue, endDateValue);
				}
				else if (selectedOptionFromDropDownList == "1" && typeOfDataReviewed== "Incomes") {
					getOnePage('Income', 'isCurrentMonthDate', false);
				}
				else if (selectedOptionFromDropDownList == "2" && typeOfDataReviewed== "Incomes") {
					getOnePage('Income', 'isPreviousMonthDate', false);
				}
				else if (selectedOptionFromDropDownList == "3" && typeOfDataReviewed== "Incomes") {
					getOnePage('Income', 'isCurrentYearDate', false);
				}
				else if (selectedOptionFromDropDownList == "4" && typeOfDataReviewed== "Incomes") {
					getOnePage('Income', 'isDateFromModal', true, startDateValue, endDateValue);
				}
			}
			else {
				cleanList();
				createPieChart(false);

				if (!isElementEmpty($('#next-link'))) {
					$('#next-link').html('');
				}

				if (!isElementEmpty($('#previous-link'))) {
					$('#previous-link').html('');
				}

				if (selectedOptionFromDropDownList == "1" && typeOfDataReviewed== "Expenses") {
					switchIncomeExpenseSummary('expense', 'isCurrentMonthDate', false, true, false);
					updatePieChart(pieChart, 'Expense', 'isCurrentMonthDate', false);
				}
				else if (selectedOptionFromDropDownList == "2" && typeOfDataReviewed== "Expenses") {
					switchIncomeExpenseSummary('expense', 'isPreviousMonthDate', false, true, false);
					updatePieChart(pieChart, 'Expense', 'isPreviousMonthDate', false);
				}
				else if (selectedOptionFromDropDownList == "3" && typeOfDataReviewed== "Expenses") {
					switchIncomeExpenseSummary('expense', 'isCurrentYearDate', false, true, false);
					updatePieChart(pieChart, 'Expense', 'isCurrentYearDate', false);
				}
				else if (selectedOptionFromDropDownList == "4" && typeOfDataReviewed== "Expenses") {
					switchIncomeExpenseSummary('expense', 'isDateFromModal', false, true, true, startDateValue, endDateValue);
					updatePieChart(pieChart, 'Expense', 'isDateFromModal', true, startDateValue, endDateValue);
				}
				else if (selectedOptionFromDropDownList == "1" && typeOfDataReviewed== "Incomes") {
					switchIncomeExpenseSummary('income', 'isCurrentMonthDate', false, true, false);
					updatePieChart(pieChart, 'Income', 'isCurrentMonthDate', false);
				}
				else if (selectedOptionFromDropDownList == "2" && typeOfDataReviewed== "Incomes") {
					switchIncomeExpenseSummary('income', 'isPreviousMonthDate', false, true, false);
					updatePieChart(pieChart, 'Income', 'isPreviousMonthDate', false);
				}
				else if (selectedOptionFromDropDownList == "3" && typeOfDataReviewed== "Incomes") {
					switchIncomeExpenseSummary('income', 'isCurrentYearDate', false, true, false);
					updatePieChart(pieChart, 'Income', 'isCurrentYearDate', false);
				}
				else if (selectedOptionFromDropDownList == "4" && typeOfDataReviewed== "Incomes") {
					switchIncomeExpenseSummary('income', 'isDateFromModal', false, true, true, startDateValue, endDateValue);
					updatePieChart(pieChart, 'Income', 'isDateFromModal', true, startDateValue, endDateValue);
				}	
			}
		});

		$('#next-link').click(function(e) {
			e.preventDefault();
			var numberOfPages = Math.ceil(dataToDisplayList.length / 7);
			var firstIdOnThePage = $('#th-id-0').text();
			var indexOfFirstIdOnTheCurrentPageInArray = dataToDisplayList.indexOf(dataToDisplayList.find(function(obj) {return obj['id'] === firstIdOnThePage}));
			var indexOfFirstIdOnTheNextPage = indexOfFirstIdOnTheCurrentPageInArray + 7;
			var currentPageNumber = (indexOfFirstIdOnTheCurrentPageInArray / 7) + 1;
			var isExpenseList = ($('#presented-table-name').text().left(8).trim() === "Expenses") ? true : false;
			var numberOfAllItems = dataToDisplayList.length;
			
			if (currentPageNumber + 1 < numberOfPages) {
				updateInformationForNextPageInTheList(indexOfFirstIdOnTheNextPage, 7, isExpenseList);
			}
			else if (currentPageNumber + 1 == numberOfPages) {
				$('#next-link').html('');
				var numberOfItemsOnLastPage =  (numberOfAllItems - (numberOfPages - 1) * 7); 
				updateInformationForNextPageInTheList(indexOfFirstIdOnTheNextPage, numberOfItemsOnLastPage, isExpenseList);
			}

			if (isElementEmpty($('#previous-link'))) {
				$('#previous-link').html('Previous');
			}	
		});

		$('#previous-link').click(function(e) {
			e.preventDefault();
			var numberOfPages = Math.ceil(dataToDisplayList.length / 7);
			var firstIdOnThePage = $('#th-id-0').text();
			var indexOfFirstIdOnTheCurrentPageInArray = dataToDisplayList.indexOf(dataToDisplayList.find(function(obj) {return obj['id'] === firstIdOnThePage}));
			var indexOfFirstIdOnThePreviousdPage = indexOfFirstIdOnTheCurrentPageInArray - 7;
			var currentPageNumber = (indexOfFirstIdOnTheCurrentPageInArray / 7) + 1;
			var isExpenseList = ($('#presented-table-name').text().left(8).trim() === "Expenses") ? true : false;
			var numberOfAllItems = dataToDisplayList.length;

			if (currentPageNumber != numberOfPages) {
				updateInformationForPreviousPageInTheList(indexOfFirstIdOnThePreviousdPage, 0, isExpenseList);
			}
			else if (currentPageNumber == numberOfPages) {
				var numberOfItemsToBeRestored = (numberOfPages * 7) - numberOfAllItems;
				updateInformationForPreviousPageInTheList(indexOfFirstIdOnThePreviousdPage, numberOfItemsToBeRestored, isExpenseList);
				$('#next-link').html('Next');
			}

			if (currentPageNumber - 1 == 1) {
				$('#previous-link').html('');
			}
		});

		$("#submitModalButton").click(function () {
			if (checkDateInModal()) {
				$("#closeModalSymbol").attr("disabled", true);
				$("#closeModalButton").attr("disabled", true);
				$("#submitModalButton").attr("disabled", true);
				$("#startDate").attr("disabled", true);
				$("#endDate").attr("disabled", true);

				var isPieChartDisplayed = isElementEmpty($('#id-list'));
				var typeOfDataReviewed = $('#presented-table-name').text().left(8).trim();
			
				$('#boxToProvidePeriodForBalanceSummary').delay(2000).queue(function() { 
					if (!isPieChartDisplayed) {
						cleanList();
						createPieChart(false);
					}

					if (!isElementEmpty($('#next-link'))) {
						$('#next-link').html('');
					}

					if (!isElementEmpty($('#previous-link'))) {
						$('#previous-link').html('');
					}

					$(this).modal("hide");
	
					if (typeOfDataReviewed == "Expenses") {
						switchIncomeExpenseSummary('expense', 'isDateFromModal', true, false, true, startDateValue, endDateValue); 
						getTotalBalance('isDateFromModal', true, startDateValue, endDateValue);
						updatePieChart(pieChart, 'Expense', 'isDateFromModal', true, startDateValue, endDateValue);
					}
					else if (typeOfDataReviewed == "Incomes") {
						switchIncomeExpenseSummary('income', 'isDateFromModal', true, false, true, startDateValue, endDateValue);
						getTotalBalance('isDateFromModal', true, startDateValue, endDateValue);
						updatePieChart(pieChart, 'Income', 'isDateFromModal', true, startDateValue, endDateValue);
					}
					
					$(this).dequeue(); 
				});
			}
		});

	$("#boxToProvidePeriodForBalanceSummary").on("hidden.bs.modal",clearModalBoxToDefault);

	});

	