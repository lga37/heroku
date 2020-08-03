//script.js
$(function () {

	toastr.options = {
	    "closeButton": true, // true/false
	    "debug": false, // true/false
	    "newestOnTop": false, // true/false
	    "progressBar": false, // true/false
	    "positionClass": "toast-top-right", // toast-top-right / toast-top-left / toast-bottom-right / toast-bottom-left
	    "preventDuplicates": false, //true/false
	    "onclick": null,
	    "showDuration": "300", // in milliseconds
	    "hideDuration": "1000", // in milliseconds
	    "timeOut": "2000", // in milliseconds
	    "extendedTimeOut": "1000", // in milliseconds
	    "showEasing": "swing",
	    "hideEasing": "linear",
	    "showMethod": "fadeIn",
	    "hideMethod": "fadeOut"
	};


    new WOW().init();

	$('[data-toggle="tooltip"]').tooltip();
	$('[data-toggle="popover"]').popover();
    $('.mdb-select').material_select();


    $('.datepicker').pickadate({


		monthsFull: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
		monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
		weekdaysFull: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
		weekdaysShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
		showMonthsShort: undefined,
		showWeekdaysFull: undefined,

		// Buttons
		today: 'Today',
		clear: 'Clear',
		close: 'Close',

		// Accessibility labels
		labelMonthNext: 'Next month',
		labelMonthPrev: 'Previous month',
		labelMonthSelect: 'Select a month',
		labelYearSelect: 'Select a year',


		format: 'd mmmm, yyyy',
		formatSubmit: 'dd/mm/yyyy',		

		closeOnSelect: true,
		closeOnClear: true,

		editable: true

    });










});



