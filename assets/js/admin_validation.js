function validatePhone() {
	var a = document.getElementById('contact-no').value;
	//var filter = /^[0-9-+]+$/;
	//var filter = /((\+*)((0[ -]+)*|(91 )*)(\d{12}+|\d{10}+))|\d{5}([- ]*)\d{6}/;
	var filter = /((\+*)((0[ -]+)*|(91 )*)(\d{12}|\d{10}))+|\d{5}([- ]*)\d{6}/;
	if (a.length > 14 || a.length < 10) {
		return false;
	}

	if (filter.test(a)) {
		return true;
	} else {
		return false;
	}
}

/* check for valid contact number and add span for status */
jQuery(document).ready(function ($) {
	$('#contact-no').bind("ready blur", function (e) {
		if (validatePhone()) {
			$('#spnPhoneStatus').html('Valid');
			$('#spnPhoneStatus').css('color', 'green');
		} else {
			$('#spnPhoneStatus').html('Invalid');
			$('#spnPhoneStatus').css('color', 'red');
		}
	});
});

