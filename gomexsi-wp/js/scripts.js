jQuery(document).ready(function($) {

/* =============================================================================
   Data Query
   ========================================================================== */
	
	// Start by checking to see if we're on the data query page template.
	if($('body.page-template-data-query-php').length){
		
		// Data query form submit action.
		$('form#data-query').submit(function(e){
		
			// Prevent actual form submission.  We'll do this with AJAX.
			e.preventDefault();
			
			// Clear the status container.
			$('#status').html('');
			
			// The query object that we'll submit via POST.
			var queryObj = {};
			
			// The "action" tells WordPress what to run.  Defined in functions.php.
			queryObj.action = 'rhm_data_query';
			
			// Loop through the form elements that have the class "query-var".
			$('form#data-query .query-var').each(function(){
				// Use the name attribute as the key and the value as the value.
				queryObj[$(this).attr('name')] = $(this).val();
			});
			
			// POST to the WordPress Ajax system.
			$.post(
				
				// URL to the WordPress Ajax system.
				'/wp-admin/admin-ajax.php',
				
				// The object containing the POST data.
				queryObj,
				
				// Success callback function.
				function(data, textStatus, jqXHR){
					
					// Show status on page.
					$('#status').html(textStatus);
					
					// Show results on page.
					$('#results').html(data);
				}
			
			// Failure callback function.
			).fail(function(jqXHR, textStatus, errorThrown){
				
				// Show status on page.
				$('#status').html(textStatus);
				
				// Clear results area.
				$('#results').html('');
			});
		});
		
		// "Clear Results" link action.
		$('a#clear').click(function(e){
			e.preventDefault();
			$('#results').html('');
		});
	}


/* =============================================================================
   Login and Registration
   ========================================================================== */
	
	// Show the login form when link is clicked.
	$('a#login-link, a.login-link').click(function(e){
		e.preventDefault();
		$('#registrationform').hide(250);
		$('#loginform').toggle(250);
		$('#loginform input#user_login').focus();
	});
	
	// Show the registration form when link is clicked.
	$('#registration-link a, a.registration-link').click(function(e){
		e.preventDefault();
		$('#loginform').hide(250);
		$('#registrationform').toggle(250);
		$('#registrationform input#user_login').focus();
	});
	
	// Handle registration form submission.
	$('form#registrationform').submit(function(e){
		
		// Prevent actual form submission.  We'll do this with AJAX.
		e.preventDefault();
		
		// POST to the WordPress Ajax system.
		$.post(
			
			// URL to the WordPress Ajax system.
			'/wp-admin/admin-ajax.php',
			{
				// Tell WordPress which action to run. Defined in functions.php.
				action: 'rhm_ajax_register',
				
				// Get values from form.
				user_login: $('form#registrationform input#user_login').val(),
				user_email: $('form#registrationform input#user_email').val()
			},
			
			// Callback function.
			function(result) {
				
				// Prep the "notes" area.
				$('form#registrationform #reg-notes').remove();
				$('form#registrationform').prepend('<p id="reg-notes"></p>');
				
				// Load any message returned by the authentication system.
				$('form#registrationform #reg-notes').html(result);
				
				// If registration was successful, show the success message and disable the form.
				if(result.indexOf('Success') !== -1){
					$('form#registrationform #reg-notes').addClass('success').show();
					$('form#registrationform p.registration-username label').html('Your Username');
					$('form#registrationform input#user_login').prop('disabled', true).addClass('success');
					$('form#registrationform input#user_email').prop('disabled', true).addClass('success');
					$('form#registrationform #reg_passmail').remove();
					$('form#registrationform input#register').remove();
				}
				// If registration was unsuccessful, show the error message.
				else if(result.indexOf('ERROR') !== -1){
					$('form#registrationform #reg-notes').addClass('error').show();
				}
			}
		);
	});
	
	// Handle login form submission.
	$('form#loginform').submit(function(e){
		
		// Prevent actual form submission.  We'll do this with AJAX.
		e.preventDefault();
		
		// Get boolean value of "remember me" checkbox.
		var remember = false;
		if($('form#loginform input#rememberme').is(':checked')){
			remember = true;
		}
		
		// POST to the WordPress Ajax system.
		$.post(
			
			// URL to the WordPress Ajax system.
			'/wp-admin/admin-ajax.php',
			{
				// Tell WordPress which action to run. Defined in functions.php.
				action: 'rhm_ajax_login',
				
				// Get values from form.
				log: $('form#loginform input#user_login').val(),
				pwd: $('form#loginform input#user_pass').val(),
				rememberme: remember
			},
			
			// Callback function.
			function(result) {
				
				// Prep the "notes" area.
				$('form#loginform #log-notes').remove();
				$('form#loginform').prepend('<p id="log-notes"></p>');
				
				// Load any message returned by the authentication system.
				$('form#loginform #log-notes').html(result);
				
				// If login was successful, show the success message, disable the form, and reload the page.
				if(result.indexOf('Success') !== -1){
					$('form#loginform #log-notes').addClass('success').show();
					$('form#loginform input#user_login').prop('disabled', true).addClass('success');
					$('form#loginform input#user_pass').prop('disabled', true).addClass('success');
					$('form#loginform input#rememberme').prop('disabled', true);
					$('form#loginform input#wp-submit').remove();
					location.reload(true);
				}
				// If login was unsuccessful, show the error message.
				else if(result.indexOf('ERROR') !== -1){
					$('form#loginform #log-notes').addClass('error').show();
				}
			}
		);
	});
	
});
