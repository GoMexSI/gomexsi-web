/* =============================================================================
   Custom Scripts
   ========================================================================== */

jQuery(document).ready(function($) {
	$('a#login-link, a.login-link').click(function(e){
		e.preventDefault();
		$('#registrationform').hide(250);
		$('#loginform').toggle(250);
		$('#loginform input#user_login').focus();
	});

	$('#registration-link a, a.registration-link').click(function(e){
		e.preventDefault();
		$('#loginform').hide(250);
		$('#registrationform').toggle(250);
		$('#registrationform input#user_login').focus();
	});
	
	$('form#registrationform').submit(function(e){
		e.preventDefault();
		$.post(
			Ajax.url,
			{
				action: 'rhm_ajax_register',
				user_login: $('form#registrationform input#user_login').val(),
				user_email: $('form#registrationform input#user_email').val()
			},
			function(result) {
				$('form#registrationform #reg-notes').remove();
				$('form#registrationform').prepend('<p id="reg-notes"></p>');
				
				$('form#registrationform #reg-notes').html(result);

				if(result.indexOf('Success') !== -1){
					$('form#registrationform #reg-notes').addClass('success').show();
					$('form#registrationform p.registration-username label').html('Your Username');
					$('form#registrationform input#user_login').prop('disabled', true).addClass('success');
					$('form#registrationform input#user_email').prop('disabled', true).addClass('success');
					$('form#registrationform #reg_passmail').remove();
					$('form#registrationform input#register').remove();
				} else if(result.indexOf('ERROR') !== -1){
					$('form#registrationform #reg-notes').addClass('error').show();
				}
			}
		);
	});
	
	$('form#loginform').submit(function(e){
		e.preventDefault();

		var remember = false;
		if($('form#loginform input#rememberme').is(':checked')){
			remember = true;
		}
		
		$.post(
			Ajax.url,
			{
				action: 'rhm_ajax_login',
				log: $('form#loginform input#user_login').val(),
				pwd: $('form#loginform input#user_pass').val(),
				rememberme: remember
			},
			function(result) {
				$('form#loginform #log-notes').remove();
				$('form#loginform').prepend('<p id="log-notes"></p>');
				
				$('form#loginform #log-notes').html(result);

				if(result.indexOf('Success') !== -1){
					$('form#loginform #log-notes').addClass('success').show();
					$('form#loginform input#user_login').prop('disabled', true).addClass('success');
					$('form#loginform input#user_pass').prop('disabled', true).addClass('success');
					$('form#loginform input#rememberme').prop('disabled', true);
					$('form#loginform input#wp-submit').remove();
					location.reload(true);
				} else if(result.indexOf('ERROR') !== -1){
					$('form#loginform #log-notes').addClass('error').show();
				}
			}
		);
	});
	
	if($('body.page-template-data-query-php').length){
		// Data query form submit action.
		$('form#test-form').submit(function(e){
		
			// Prevent actual form submission.  We'll do this with AJAX.
			e.preventDefault();
			
			$.post(
				Ajax.url,
				{
					action: 'rhm_data_query',
					species : $('input#species').val()
				},
				function(data, textStatus, jqXHR){
					$('#status').html(textStatus);
					$('#results').html(data);
				}
			).fail(function(jqXHR, textStatus, errorThrown){
				$('#status').html(textStatus);
				$('#results').html('');
			});
		});
		
		// Clear results.
		$('a#clear').click(function(e){
			e.preventDefault();
			$('#results').html('');
		});
	}
});
