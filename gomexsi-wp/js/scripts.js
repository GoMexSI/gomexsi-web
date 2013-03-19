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
					// The function to process the data.
					processData(data);
					
					// Show status on page.
					$('#status').html(textStatus);
					
					// Show results on page.
					$('#raw-results').html(data);
				}
			
			// Failure callback function.
			).fail(function(jqXHR, textStatus, errorThrown){
				
				// Show status on page.
				$('#status').html(textStatus);
				
				// Clear results area.
				$('#results').html('');
				$('#raw-results').html('');
			});
		});
		
		// "Clear Results" link action.
		$('a#clear').click(function(e){
			e.preventDefault();
			$('#status').html('');
			$('#results').html('');
			$('#raw-results').html('');
		});
		
		// Convert ugly property names into pretty names.
		function niceName(name){
			// Table of names.
			var niceName = {
				prey: 'Prey',
				scientificName: 'Scientific Name',
				subjectInstances: 'Subject Instances'
			}
			
			if(typeof niceName[name] != 'undefined'){
				// If the name is in the table, return the nice name.
				return niceName[name];
			} else {
				// Otherwise, just return the original name.
				return name;
			}
		}
		
		// Process returned data.
		function processData(data){
			// Find the end of the array so we can trim any extra characters.
			var trimLimit = data.lastIndexOf(']') + 1;
			
			// Trim any extra characters.
			var results = data.substring(0, trimLimit);
			
			// Convert to JSON.
			if(results){
				results = JSON && JSON.parse(results) || $.parseJSON(results);
			}
			
			log(results);
			
			// Clear the results container.
			$('#results').html('');
			
			// Loop through the results.
			$.each(results, function(i, subject){
				if(!subject.scientificName){
					// Log an error if there is no scientificName property.
					log('Error: no scientific name.');
				} else {
					// Create a unique base ID for this subject.
					var baseID = subject.scientificName.replace(' ', '-').toLowerCase();
					
					// Output the scientific name.
					var sciNameID = baseID + '-sci-name';
					$('#results').append('<p id="' + sciNameID + '">Scientific Name: ' + subject.scientificName + '</p>');
					
					if(!subject.subjectInstances){
						// Log an error if there are no instances.
						log('Error: no subject instances.');
					} else {
						// Output instance title.
						var instancesTitleID = baseID + '-instances-title';
						$('#results').append('<p id="' + instancesTitleID + '">Subject Instances:</p>');
						
						// Make a list for instances.
						var instancesListID = baseID + '-instances-list';
						$('#results').append('<ul id="' + instancesListID + '"/>');
						var instancesList = $('ul#' + instancesListID);
						
						$.each(subject.subjectInstances, function(j, instance){
							// Make list item for each instance.
							var singleInstanceID = baseID + '-instance-' + (j + 1);
							$(instancesList).append('<li id="' + singleInstanceID + '">Instance ' + (j + 1) + '</li>');
							var singleInstance = $(instancesList).children('li#' + singleInstanceID);
							
							// Make sublist of instance properties.
							var singleInstanceListID = singleInstanceID + '-list';
							$(singleInstance).append('<ul id="' + singleInstanceListID + '" />');
							var singleInstanceList = $('ul#' + singleInstanceListID);
							
							$.each(instance, function(propName, propValue){
								// Make list item for each instance property.
								var instancePropID = singleInstanceID + '-' + propName;
								$(singleInstanceList).append('<li id="' + instancePropID + '">' + niceName(propName) + '</li>');
								var instanceProp = $('li#' + instancePropID);
								
								if(typeof propValue == 'object'){
									// If this property is an object (an array is an object), then make a list for its values.
									var propListID = instancePropID + '-list';
									$(singleInstanceList).append('<ul id="' + propListID + '" />');
									var propList = $('ul#' + propListID);
									
									$.each(propValue, function(k, item){
										// Make list items for each value.
										$(propList).append('<li>' + item + '</li>');
									});
								} else {
									// If this property is not an object (or an array), then just add the value to the property list item.
									$(instanceProp).append(': ' + propValue)
								}
							});
						});
					}
				}
			});
		}
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
