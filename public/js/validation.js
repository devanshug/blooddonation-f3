$(document).ready(function() {
	$('#fillform').submit(function(event) {
		/**************************************** Helper Funcions ****************************************/
		function stringLen(fieldLength, lowerBound, upperBound, announcerString)
		{
			if( !(fieldLength >= lowerBound && fieldLength <= upperBound) ) {
				$('#error').text(announcerString+' length must be '+lowerBound+'<=length(pass)<='+upperBound);
				return false;
			}
			return true;
		}

		function alphabetString(string, announcerString)
		{
			if( !string.match(/^[a-zA-Z]+$/) ) {
				$('#error').text(announcerString+' must match ^[a-zA-Z]+$');
				return false;
			}
			return true;
		}

		function alphabetSpaceString(string, announcerString)
		{
			if( !string.match(/^[a-zA-Z ]+$/) ) {
				$('#error').text(announcerString+' must match ^[a-zA-Z ]+$');
				return false;
			}
			return true;
		}

		function alphaNumeric(string, announcerString)
		{
			if( !string.match(/^[a-zA-Z0-9]+$/) ) {
				$('#error').text(announcerString+' must match ^[a-zA-Z0-9]+$');
				return false;
			}
			return true;
		}

		function userexists(data) {
			if ( data == "Username Exists" ) {
				$('#error').text(data);
				return false;
			}
			return true;
		}

		function equalityMismatch(string1, string2, announcerString) {
			if( string1 != string2 ) {
				$('#error').text(announcerString+' Entered do not match, Please check again');
				return false;
			}
			return true;
		}

		// Email Validation
		// The regex used is from reference http://stackoverflow.com/questions/2507030/email-validation-using-jquery
		function validemail(email) {
			var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
			if( !emailReg.test(email) ) {
				$('#error').text('Email must match ^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$');
				return false;
			}
			return true;
		}

		function validname(name, lowerBound, upperBound, announcerString) {
			if( !stringLen(name.length, lowerBound, upperBound, announcerString) || !alphabetString(name, announcerString)) {
				return false;
			}
			return true;
		}

		function validfullname(name, lowerBound, upperBound, announcerString) {
			if( !stringLen(name.length, lowerBound, upperBound, announcerString) || !alphabetSpaceString(name, announcerString)) {
				return false;
			}
			return true;
		}

		function validdate(day, month, year) {
			if(day<1 || month<1 || year<1)
			{
				$('#error').text("Wrong date");
				return false;
			}
			else if(month==2)
			{
				if((year%100==0 && year%400!=0) || year%4!=0)
				{
					if(day>28) {
						$('#error').text("Wrong date");
						return false;
					}
				}
				else
				{
					if(day>29) {
						$('#error').text("Wrong date");
						return false;
					}
				}
			}
			else if(month<=7 && month>=1)
			{
				if(day>=31 && month%2==0)
				{
					$('#error').text("Wrong date");
					return false;
				}
				else if(day>=32 && month%2==1)
				{
					$('#error').text("Wrong date");
					return false;
				}
			}
			else if(month<=12 && month>=8)	{
				if(day>=31 && month%2==1)
				{
					$('#error').text("Wrong date");
					return false;
				}
				else if(day>=32 && month%2==0)
				{
					$('#error').text("Wrong date");
					return false;
				}
			}
			else
			{
				$('#error').text("Wrong date");
				return false;
			}
			return true;
		}

		function validnumber(number,announcerString) {
			if( !number.match(/^[0-9]+$/)) {
				$('#error').text(announcerString+' must match ^[0-9]+$');
				return false;
			}
			return true;
		}
		/************************************* End of Helper Functions ***********************************/

		
		/****************************************** Validations ******************************************/
		// Username Validation
		if($('#username').length) {
			var username = $('#username').val();
			if(!validname(username, 4, 10, 'Username'))
				return false;
			$.get('exists/'+username, userexists);
		}

		// Password Validation
		if($('#password1').length)
		{
			var password1 = $('#password1').val();
			var password2 = $('#password2').val();
			if(!equalityMismatch(password1, password2, 'Passwords') || !alphaNumeric(password1, 'Password') || !stringLen(password1.length, 5, 10, 'Password')) {
				return false;
			}
		}

		// Email Validation
		if($('#email').length) {
			var email = $('#email').val();
			if(!stringLen(email.length, 5, 50, 'Email') || !validemail(email))
				return false;
		}

		// FirstName Validation
		if($('#firstname').length) {
			var firstname = $('#firstname').val();
			if(!validname(firstname, 2, 10, 'First Name'))
				return false;
		}

		// LastName Validation
		if($('#lastname').length) {
			var lastname = $('#lastname').val();
			if(!validname(lastname, 2, 10, 'Last Name'))
				return false;
		}

		// Name Validation
		if($('#name').length) {
			var name = $('#name').val();
			if(!validfullname(name, 4, 20, 'Name'))
				return false;
		}

		// Date Validation
		if($('#day').length) {
			var day = $('#day').val();
			var month = $('#month').val();
			var year = $('#year').val();
			if(!validdate(day, month, year))
				return false;
		}

		// State validation
		if($('#state').length) {
			if($('#state').val()=='') {
				$('#error').text('Please select your state');
				return false;
			}
		}

		// Mobile Number Validation
		if($('#mobile').length) {
			var mobile = $('#mobile').val();
			if(mobile.length!=10) {
				$('#error').text('Mobile Number should be of 10 digit');
				return false;
			}
			if(!validnumber(mobile, 'Mobile Number'))
				return false;
		}

		// Landline Number Validation
		if($('#landline').length) {
			var landline = $('#landline').val();
			if(!validnumber(landline, 'Landline Number'))
				return false;
		}
		/*************************************** End of Validations **************************************/
	})
});
