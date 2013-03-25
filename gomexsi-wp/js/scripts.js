jQuery(document).ready(function($) {

/* =============================================================================
   Data Query
   ========================================================================== */
	
	// Start by checking to see if we're on the data query page template.
	if($('body.page-template-data-query-taxonomic-php').length){
		$('input[name="subjectName"]').focus();
		
		// Conditional switch.
		$('.switch').click(function(e){
			var checked = $(this).prop('checked');
			var switchName = $(this).attr('data-switch');
			if(checked){
				$('.conditional[data-switch="'+switchName+'"]').show().find('.query-var, .query-set').removeClass('null');
			} else {
				$('.conditional[data-switch="'+switchName+'"]').hide().find('.query-var, .query-set').addClass('null');
			}
		});
		
		// Fuzzy search suggestions.
		$('input.taxonomic').focusout(function(e){
			var taxWrap = $(this).parent('.tax-wrapper');
			$(taxWrap).find('ul.tax-suggestions').remove();
		});
		
		$('input.taxonomic').keydown(function(e){
			var key = e.which;
			var taxWrap = $(this).parent('.tax-wrapper');
			var taxSugList = $(taxWrap).find('.tax-suggestions');
			var taxSugItems = $(taxWrap).find('.tax-suggestion');
			var i = $(taxWrap).find('.tax-suggestion.selected').index();
			
			// Down arrow.
			if(key == '40'){
				if(i < (taxSugItems.length - 1)){
					i++;
					var value = $(taxSugItems).removeClass('selected').eq(i).addClass('selected').text();
					$(this).val(value);
				}
			}
			
			// Up arrow.
			if(key == '38'){
				if(i > 0){
					i--;
					var value = $(taxSugItems).removeClass('selected').eq(i).addClass('selected').text();
					$(this).val(value);
				} else {
					i = -1;
					$(taxSugItems).removeClass('selected')
				}
			}
		});
		
		var t;
		
		$('input.taxonomic').keyup(function(e){
			var key = e.which;
			var taxWrap = $(this).parent('.tax-wrapper');
			var taxSugList = $(taxWrap).find('.tax-suggestions');
			var taxSugItems = $(taxWrap).find('.tax-suggestion');
			
			var noTriggerKeys = [16, 17, 18, 20, 37, 38, 39, 40, 91, 93, 27, 110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123, 124, 125, 126, 127, 128, 129, 130];
			
			// Non-arrow, non-modifier keys.
			if($.inArray(key, noTriggerKeys) < 0){
				clearTimeout(t);
				var sugValue = $(this).val();
				t = setTimeout(function(){
					$.post(
						'/wp-admin/admin-ajax.php',
						{
							action: 'rhm_data_query',
							url: 'http://gomexsi.tamucc.edu/gomexsi/requestHandler/RequestHandler.php',
							suggestion: sugValue
						},
						function(data, textStatus, jqXHR){
							log(data);
							if($(taxWrap).find('ul.tax-suggestions').length){
								$(taxWrap).find('ul.tax-suggestions').remove();
							}
							$(taxWrap).append('<ul class="tax-suggestions" />')
							$(taxWrap).find('ul.tax-suggestions').append('<li class="tax-suggestion">test</li>');
						}
					);
				}, 500);
			}
		});

				
		$('li.tax-suggestion').click(function(e){
			$(this).parent().children().removeClass('selected');
			var value = $(this).addClass('selected').text();
			$(this).closest('.tax-wrapper').children('input.taxonomic').val(value);
		});
		
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
				if($(this).hasClass('null')){
					queryObj[$(this).attr('name')] = '';
				} else {
					queryObj[$(this).attr('name')] = $(this).val();
				}
			});
			
			log('Query Object:'); log(queryObj);
			
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
				pred: 'Predators',
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
			// Clear the results container.
			$('#results').html('');
			
			if($('select[name="url"]').val() != 'http://gomexsi.tamucc.edu/gomexsi/query-test-return.php'){
				// Find the end of the array so we can trim any extra characters.
				var trimLimit = data.lastIndexOf(']') + 1;
				
				// Trim any extra characters.
				var results = data.substring(0, trimLimit);
				
				// Convert to JSON.
				if(results){
					results = JSON && JSON.parse(results) || $.parseJSON(results);
				}
				
				log('JSON Parsed Results Object:'); log(results);
				
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
			} else {
				log('Unparsed Results String:'); log(data);
			}
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
