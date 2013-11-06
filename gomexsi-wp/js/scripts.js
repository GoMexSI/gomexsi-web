jQuery(document).ready(function($) {
	
	/* Fancybox */
	$("a[href$='.jpg'],a[href$='.jpeg'],a[href$='.png'],a[href$='.gif']").fancybox();
	
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
	
	
	/* =============================================================================
	   Article Reference Tooltip
	   ========================================================================== */
	
	$('body').delegate('.ref-tag-link', 'click', function(e){
		e.preventDefault();
		e.stopPropagation();
		
		// Remove any existing ref tag boxes.
		$('.ref-tag-box').remove();
		
		var ref_tag = $(this).text();
		log(ref_tag);
		
		var wrapper = $(this).parent('.ref-tag-wrapper');
		$(wrapper).append('<div class="ref-tag-box loading"><div class="container"><ul></ul></div><div class="bridge"></div></div>');
		var box = $(wrapper).find('.ref-tag-box');
		var linkList = $(wrapper).find('ul');
		
		// POST to the WordPress Ajax system.
		$.post(
			
			// URL to the WordPress Ajax system.
			'/wp-admin/admin-ajax.php',
			
			// The object containing the POST data.
			{
				action	: 'rhm_ref_tag',
				ref_tag	: ref_tag
			},
			
			// Success callback function.
			function(data, textStatus, jqXHR){
				var externalUrl = data.url;

				$(box).removeClass('loading');
				
				if(typeof externalUrl !== 'undefined'){
					$(linkList).append('<li><a href="' + externalUrl + '" class="external" target="_blank">Details of Study (FWC)</a></li>');
				} else {
					$(linkList).append('<li>Details of Study Unavailable</li>');
					if($(wrapper).closest('.tablepress').length == 0){
						$(linkList).append('<li>See <em>Data References</em> in the menu for full citation.</li>');
					}
				}
			},
		
			// Expect JSON data.
			'json'
			
		// Failure callback function.
		).fail(function(jqXHR, textStatus, errorThrown){
			log(errorThrown);
		});
	});
	
	$('body').click(function(e){
		// If a name tip box is open, then clicking anywhere else will remove it.
		$('.ref-tag-box').remove();
	});
	
	// Go through tables of data references and wrap the reference tags with the necessary HTML.
	$('.tablepress').each(function(){
		var refTagHeader = $(this).find('th:contains("Reference tag"), th:contains("Reference Tag"), th:contains("Ref tag"), th:contains("Ref Tag")');
		var colClass = $(refTagHeader).attr('class');
		var colClassMatch = colClass.match(/column-\d+/);
		if(typeof colClassMatch[0] != 'undefined'){
			var colClassSelector = 'td.' + colClassMatch[0];
			$(this).find(colClassSelector).each(function(){
				var refTag = $(this).text();
				refTag = refTag.replace(/[,;\.]/g, '');
				refTag = refTag.replace(/\s+/g, ' ');
				$(this).html('<div class="ref-tag-wrapper"><a href="#" class="ref-tag-link">' + refTag + '</a></div>');
			});
		}
	});
	
	
	
	/* =============================================================================
	   Article Reference Tooltip
	   ========================================================================== */
	if($('.stats').length){
		// POST to the WordPress Ajax system.
		$.post(
			
			// URL to the WordPress Ajax system.
			'/wp-admin/admin-ajax.php',
			
			// The object containing the POST data.
			{action : 'rhm_stats_request'},
			
			// Success callback function.
			function(data, textStatus, jqXHR){
				log(data);
				
/*
				$('.stats-visits').each(function(i){
					var start = parseInt($(this).html());
					var end = data.visits;
					countUp($(this), start, end, .02, 1);
				});
				
				$('.stats-predators').each(function(i){
					var start = parseInt($(this).html());
					var end = data.predators;
					countUp($(this), start, end, .02, 1);
				});
				
				$('.stats-prey').each(function(i){
					var start = parseInt($(this).html());
					var end = data.prey;
					countUp($(this), start, end, .02, 1);
				});
*/
				
				$('.stats-studies').each(function(i){
					var start = parseInt($(this).html());
					var end = data.studies;
					countUp($(this), start, end, .02, 1);
				});
				
				$('.stats-interactors').each(function(i){
					var start = parseInt($(this).html());
					var end = data.interactors;
					countUp($(this), start, end, .02, 1);
				});
				
				$('.stats-interactions').each(function(i){
					var start = parseInt($(this).html());
					var end = data.interactions;
					countUp($(this), start, end, .02, 1);
				});
			},
		
			// Expect JSON data.
			'json'
			
		// Failure callback function.
		).fail(function(jqXHR, textStatus, errorThrown){
			log(errorThrown);
		});

	}
});


function countUp(jQObject, currentValue, endValue, factor, delay){
	var t = setTimeout(function(){
		if(currentValue < endValue){
			var delta = Math.ceil((endValue - currentValue) * factor);
			currentValue = currentValue + delta;
			$(jQObject).html($.format.number(currentValue, '#,###'));
			countUp(jQObject, currentValue, endValue, factor, delay);
		}
	}, delay);
}

