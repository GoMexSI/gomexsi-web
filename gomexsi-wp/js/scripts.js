jQuery(document).ready(function($) {

/* =============================================================================
   Data Query
   ========================================================================== */
	
	// Start by checking to see if we're on the data query page template.
	if($('body.page-template-data-query-taxonomic-php').length){
		$('input[name="subjectName"]').focus();
		
		/* ==============================
		   Miscellaneous
		   ============================*/
		
		function nameTip(scientificName){
			var nameTip = '';
			nameTip += '<div class="name-tip-wrapper">';
			nameTip += '<a href="#" class="name-tip-link">';
			nameTip += scientificName;
			nameTip += '</a>';
			nameTip += '<div class="name-tip-box"><div class="container">';
			nameTip += '<ul>';
			nameTip += '<li><a href="#">View in Explorer Mode</a></li>';
			nameTip += '<li><a href="http://fishbase.org/summary/' + scientificName.replace(' ', '-') + '.html" class="external" target="_blank">View on FishBase.org</a></li>';
			nameTip += '</ul>';
			nameTip += '</div>'; // .container
			nameTip += '<div class="bridge"></div>';
			nameTip += '</div></div>'; // .name-tip-box, .name-tip-wrapper
			return nameTip;
		}
		
		
		function nameSafe(name){
			var safeName = name.replace(' ', '_').toLowerCase();
			return safeName;
		}
		
		/* ==============================
		   Fuzzy Search Suggestions
		   ============================*/
		
		// Fuzzy search suggestions.
		$('input.taxonomic').keydown(function(e){
			// Which key was pressed? (Key code number.)
			var key = e.which;
			
			// Convenience handles.
			var taxWrap = $(this).parent('.tax-wrapper');
			var taxSugList = $(taxWrap).find('.tax-suggestions');
			var taxSugItems = $(taxWrap).find('.tax-suggestion');
			
			// Index of the currently selected suggestion in the list.
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
		
		// Handle for the suggestion setTimeout() function.
		// Declared outside the function so it can be cancelled by a separate instance of the function.
		var sugTimeout;
		
		$('input.taxonomic').keyup(function(e){
			// Which key was pressed? (Key code number.)
			var key = e.which;
			
			// Convenience handles.
			var taxWrap = $(this).parent('.tax-wrapper');
			var taxSugList = $(taxWrap).find('.tax-suggestions');
			var taxSugItems = $(taxWrap).find('.tax-suggestion');
			
			// These keys are modifiers or arrow keys that should not trigger a suggestion request.
			var noTriggerKeys = [16, 17, 18, 20, 37, 38, 39, 40, 91, 93, 27, 110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123, 124, 125, 126, 127, 128, 129, 130];
			
			// If not an arrow or modifier key.
			if($.inArray(key, noTriggerKeys) < 0){
				// Cancel any previous request.
				clearTimeout(sugTimeout);
				
				// The value to send for suggestions.
				var sugValue = $(this).val();
				
				// If we have a suggestion fragment.
				if(sugValue.length){
					// Put this action on a 250 millisecond delay to avoid unnecessary requests.
					sugTimeout = setTimeout(function(){
						// Post to the request handler.
						$.post(
							'/wp-admin/admin-ajax.php',
							{
								action: 'rhm_data_query',
								url: 'http://gomexsi.tamucc.edu/gomexsi/requestHandler/RequestHandler.php',
								serviceType: 'rest',
								suggestion: sugValue
							},
							// Success function.
							function(data, textStatus, jqXHR){
								if(data){
									// Convert to JSON.
									results = JSON && JSON.parse(data) || $.parseJSON(data);
									
									var suggestions = results[0].matches;
									
									// Remove the old suggestion list.
									if($(taxWrap).find('ul.tax-suggestions').length){
										$(taxWrap).find('ul.tax-suggestions').remove();
									}
									
									// If we have at least one suggestion and it is not identical to the text in the input.
									if($(suggestions).length > 0 && suggestions[0] != sugValue){
										// Attach a new suggestion list.
										$(taxWrap).append('<ul class="tax-suggestions" />');
										var taxSugList = $(taxWrap).find('ul.tax-suggestions');
										
										// Add each suggestion to the list.
										$.each(suggestions, function(i){
											$(taxSugList).append('<li class="tax-suggestion">' + suggestions[i] + '</li>');
										});
										
										// Event listener for suggestion items to make them clickable.
										suggestionClickListener();
									}
								}
							}
						);
					}, 250); // 250 millisecond delay.
				} else {
					// If we don't have a suggestion fragment (e.g., the user deleted all text from the input) remove the suggestion list.
					$(taxWrap).find('ul.tax-suggestions').remove();
				}
			}
		});
		
		// If the user clicks anywhere on the page outside of the input box or the suggestion list, remove the list.
		$('body').click(function(e){
			$('ul.tax-suggestions').remove();
		});
		
		// Attach click event listener to suggestion list items. This function must be run after the list items are created.
		function suggestionClickListener(){
			$('li.tax-suggestion').click(function(e){
				// Stop propagation so that this does not count as a body click (and thereby remove the list).
				e.stopPropagation();
				$(this).parent().children().removeClass('selected');
				var value = $(this).addClass('selected').text();
				$(this).closest('.tax-wrapper').children('input.taxonomic').val(value);
			});
		}
		
		$('input.taxonomic').click(function(e){
			// Stop propagation so that this does not count as a body click (and thereby remove the list).
			e.stopPropagation();
		});
		
		/* ==============================
		   Results
		   ============================*/
		
		function Results(data){
			if(data){
				try{
					this.subjects = JSON && JSON.parse(data) || $.parseJSON(data);
					log('JSON Parsed Results Object:'); log(this.subjects);
					log('Processed Data Object:'); log(this);
				}
				catch(err){
					this.subjects = '';
					log('Error message: ' + err.message);
					log('Unparsed Results String:'); log(this);
				}
			} else {
				this.subjects = '';
				log('No data.');
			}
		}
		
		Results.prototype.prepResultsArea = function(){
			$('#results-area').html('');
		}
		
		// Iterate over prey, predators, etc., to get make cumulative lists and get some statistics.
		Results.prototype.makeSpecimenList = function(){
			var totalInstanceCount = 0;
			var subjects = this.subjects;
			
			$.each(subjects, function(i){
				var subject = subjects[i];
				
				subject.preyInstanceCount = 0;
				subject.preyList = [];
				
				subject.predInstanceCount = 0;
				subject.predList = [];
				
				var preyInstances = subjects[i].preyInstances;
				
				$.each(preyInstances, function(j){
					var instance = preyInstances[j];
					
					subject.preyInstanceCount++;
					totalInstanceCount++;
					
					var prey = instance.prey;
				
					$.each(prey, function(k){
						var safeName = nameSafe(prey[k]);
						
						if(safeName in subject.preyList){
							subject.preyList[safeName].count++;
						} else {
							subject.preyList[safeName] = {};
							subject.preyList[safeName].scientificName = prey[k];
							subject.preyList[safeName].count = 1;
						}
					});
				});
					
				var predInstances = subjects[i].predInstances;
				
				$.each(predInstances, function(j){
					var instance = predInstances[j];
					
					subject.predInstanceCount++;
					totalInstanceCount++;
					
					if(typeof instance.pred == 'object'){
						var safeName = nameSafe(instance.pred[0]);
					} else {
						var safeName = nameSafe(instance.pred);
					}
					
					if(safeName in subject.predList){
						subject.predList[safeName].count++;
					} else {
						subject.predList[safeName] = {};
						subject.predList[safeName].scientificName = instance.pred;
						subject.predList[safeName].count = 1;
					}
				});
			});
			
			this.totalInstanceCount = totalInstanceCount;
		}
		
		// Make the subject blocks on the page.
		Results.prototype.makeSubjects = function(){
			this.totalSubjectCount = $(this.subjects).length;
			var subjects = this.subjects;
			
			$('#results-area').empty();
			
			$.each(subjects, function(i){
				var subject = subjects[i];
				
				subject.baseID = subject.scientificName.replace(' ', '-').toLowerCase();
				$('#results-area').append('<div id="' + subject.baseID + '" class="query-results" />');
				var subjectTitleID = subject.baseID + '-title';
				$('#' + subject.baseID).append('<h2 id="' + subjectTitleID + '" class="subject-name">' + nameTip(subject.scientificName) + ' <span class="common-name"></span></h2>');
				
				if('commonNames' in subject){
					var commonNames = subject.commonNames;
					var commonNamesText = '&nbsp;&ndash;&nbsp; ';
					
					$.each(commonNames, function(j){
						commonNamesText += commonNames[j];
						if((j + 1) < $(commonNames).length){
							commonNamesText += ', ';
						}
					});
					$('#' + subjectTitleID + ' .common-name').html(commonNamesText);
				}
				
				makePrey(subject);
				makePred(subject);
			});
		}
		
		// Make the Prey section within a subject block.
		// Called in this.makeSubjects.
		function makePrey(subject){
			if(subject.preyInstanceCount){
				// Results section for prey.
				var sectionID = subject.baseID + '-prey-section';
				$('#' + subject.baseID).append('<div id="' + sectionID + '" class="results-section" />');
				var resultsSection = $('#' + sectionID);
				$(resultsSection).append('<h3 class="section-title toggle">Prey</h3>');
				$(resultsSection).append('<div class="container" />');
				
				// Prey Summary.
				$(resultsSection).children('.container').append('<div class="results-subsection prey-summary" />');
				var preySummary = $('#' + subject.baseID + ' .prey-summary');
				$(preySummary).append('<form><label class="view-option toggle-summary-all"><input type="checkbox" /> Show All Prey</label></form>');
				$(preySummary).append('<h4 class="subsection-title toggle">Prey Summary</h4>');
				$(preySummary).append('<div class="container" />');
				$(preySummary).children('.container').append('<table class="summary"><tbody></tbody></table>');
				
				// Sort the prey list by number of instances for a given prey. We must dump the prey list into an array so it can be sorted.
				var preyList = subject.preyList;
				var preyListDesc = [];
				
				for(var prop in preyList){
					preyListDesc.push({
						safeName: prop,
						scientificName: preyList[prop].scientificName,
						count: preyList[prop].count,
						percent: Math.round( ( preyList[prop].count / subject.preyInstanceCount ) * 100 )
					});
				}
				
				// Sort using a custom function.
				preyListDesc.sort(function(a,b){
					// If the count is the same for two items, sort alphabetically ascending.
					if(b.count === a.count){
						return a.scientificName < b.scientificName ? -1 : a.scientificName > b.scientificName ? 1 : 0;
					}
					
					// Otherwise, sort by count descending.
					return b.count - a.count;
				});
				
				var rowCount = 0;
				
				$.each(preyListDesc, function(i){
					rowCount++;
					var prey = preyListDesc[i];
					if(rowCount < 11){
						var row = '<tr>';
					} else {
						var row = '<tr class="overflow">';
					}
					row += '<td class="species-name">' + nameTip(prey.scientificName) + '</td>';
					row += '<td class="percent-number">' + prey.percent + '%</td>';
					row += '<td class="percent-bar"><div class="percent-bar-total"><div class="percent-bar-value" style="width:' + prey.percent + '%"></div></div></td>';
					row += '</tr>';
					
					$('#' + subject.baseID + ' .prey-summary table.summary').append(row);
				});
				
				// Prey Instance Details
				var instanceDetailsID = subject.baseID + '-prey-instance-details';
				$(resultsSection).children('.container').append('<div id="' + instanceDetailsID + '" class="results-subsection instance-details" />');
				var preyInstanceDetails = $('#' + instanceDetailsID);
				$(preyInstanceDetails).append('<form><label class="view-option toggle-references"><input type="checkbox" /> References</label><label class="view-option toggle-stats"><input type="checkbox" /> Prey Stats</label></form>');
				$(preyInstanceDetails).append('<h4 class="subsection-title toggle">Instance Details</h4>');
				$(preyInstanceDetails).append('<div class="container" />');
				
				$.each(subject.preyInstances, function(i){
					var instance = subject.preyInstances[i];
					
					var instanceID = subject.baseID + '-prey-instance-' + i;
					
					$(preyInstanceDetails).children('.container').append('<div id="' + instanceID + '" class="single-instance clearfix" />');
					var singleInstance = $('#' + instanceID);
					
					var instanceDate = ('date' in instance ? instance.date : 'unknown');
					$(singleInstance).append('<div class="date"><h5 class="label">Date:</h5> ' + instanceDate + '</div>');
					
					var instanceLocation = ('loc' in instance ? instance.loc : 'unknown');
					var lat = ('lat' in instance ? instance.lat : '');
					var lon = ('lon' in instance ? instance.lon : '');
					$(singleInstance).append('<div class="location"><h5 class="label">Location:</h5> ' + instanceLocation + ' <a href="#map-canvas" class="map-link" data-lat="' + lat + '" data-lon="' + lon + '">Map</a></div>');
					
					var instancePreyListID = instanceID + '-prey-list';
					$(singleInstance).append('<div class="prey species-list"><h5 class="label">Prey:</h5><ul id="' + instancePreyListID + '"></ul></div>');
					$.each(instance.prey, function(j){
						var prey = instance.prey[j];
						
						var li = '<li class="clearfix">';
						li += '<div class="name">' + nameTip(prey) + '</div>';
						li += '<div class="details">Details go here</div>';
						li += '</li>';
						
						$('#' + instancePreyListID).append(li);
					});
					
					var instanceReference = ('ref' in instance ? instance.ref : 'unknown');
					$(singleInstance).append('<div class="reference"><h5 class="label">Reference:</h5> ' + instanceReference + '</div>');
				});
				
				$(resultsSection).append('<hr class="section-break" />');
			}
		}
		
		// Make the Prey section within a subject block.
		// Called in this.makeSubjects.
		function makePred(subject){
			if(subject.predInstanceCount){
				// Results section for predators.
				var sectionID = subject.baseID + '-pred-section';
				$('#' + subject.baseID).append('<div id="' + sectionID + '" class="results-section" />');
				var resultsSection = $('#' + sectionID);
				$(resultsSection).append('<h3 class="section-title toggle">Predators</h3>');
				$(resultsSection).append('<div class="container" />');
				
				// Predator Summary.
				$(resultsSection).children('.container').append('<div class="results-subsection pred-summary" />');
				var predSummary = $('#' + subject.baseID + ' .pred-summary');
				$(predSummary).append('<form><label class="view-option toggle-summary-all"><input type="checkbox" /> Show All Predators</label></form>');
				$(predSummary).append('<h4 class="subsection-title toggle">Predator Summary</h4>');
				$(predSummary).append('<div class="container" />');
				$(predSummary).children('.container').append('<table class="summary"><tbody></tbody></table>');
				
				// Sort the predator list by number of instances for a given predator. We must dump the predator list into an array so it can be sorted.
				var predList = subject.predList;
				var predListDesc = [];
				
				for(var prop in predList){
					predListDesc.push({
						safeName: prop,
						scientificName: predList[prop].scientificName,
						count: predList[prop].count,
						percent: Math.round( ( predList[prop].count / subject.predInstanceCount ) * 100 )
					});
				}
				
				// Sort using a custom function.
				predListDesc.sort(function(a,b){
					// If the count is the same for two items, sort alphabetically ascending.
					if(b.count === a.count){
						return a.scientificName < b.scientificName ? -1 : a.scientificName > b.scientificName ? 1 : 0;
					}
					
					// Otherwise, sort by count descending.
					return b.count - a.count;
				});
				
				var rowCount = 0;
				
				$.each(predListDesc, function(i){
					rowCount++;
					var pred = predListDesc[i];
					if(rowCount < 11){
						var row = '<tr>';
					} else {
						var row = '<tr class="overflow">';
					}
					row += '<td class="species-name">' + nameTip(pred.scientificName) + '</td>';
					row += '<td class="percent-number">' + pred.percent + '%</td>';
					row += '<td class="percent-bar"><div class="percent-bar-total"><div class="percent-bar-value" style="width:' + pred.percent + '%"></div></div></td>';
					row += '</tr>';
					
					$('#' + subject.baseID + ' .pred-summary table.summary').append(row);
				});
				
				// Predator Instance Details
				var instanceDetailsID = subject.baseID + '-pred-instance-details';
				$(resultsSection).children('.container').append('<div id="' + instanceDetailsID + '" class="results-subsection instance-details" />');
				var predInstanceDetails = $('#' + instanceDetailsID);
				$(predInstanceDetails).append('<form><label class="view-option toggle-references"><input type="checkbox" /> References</label><label class="view-option toggle-stats"><input type="checkbox" /> Predator Stats</label></form>');
				$(predInstanceDetails).append('<h4 class="subsection-title toggle">Instance Details</h4>');
				$(predInstanceDetails).append('<div class="container" />');
				
				$.each(subject.predInstances, function(i){
					var instance = subject.predInstances[i];
					
					var instanceID = subject.baseID + '-pred-instance-' + i;
					
					$(predInstanceDetails).children('.container').append('<div id="' + instanceID + '" class="single-instance clearfix" />');
					var singleInstance = $('#' + instanceID);
					
					var instanceDate = ('date' in instance ? instance.date : 'unknown');
					$(singleInstance).append('<div class="date"><h5 class="label">Date:</h5> ' + instanceDate + '</div>');
					
					var instanceLocation = ('loc' in instance ? instance.loc : 'unknown');
					var lat = ('lat' in instance ? instance.lat : '');
					var lon = ('lon' in instance ? instance.lon : '');
					$(singleInstance).append('<div class="location"><h5 class="label">Location:</h5> ' + instanceLocation + ' <a href="#map-canvas" class="map-link" data-lat="' + lat + '" data-lon="' + lon + '">Map</a></div>');
					
					var instancePredListID = instanceID + '-pred-list';
					$(singleInstance).append('<div class="predator species-list"><h5 class="label">Predator:</h5><ul id="' + instancePredListID + '"></ul></div>');

					var li = '<li class="clearfix">';
					li += '<div class="name">' + nameTip(instance.pred) + '</div>';
					li += '<div class="details">Details go here</div>';
					li += '</li>';
					$('#' + instancePredListID).append(li);
					
					var instanceReference = ('ref' in instance ? instance.ref : 'unknown');
					$(singleInstance).append('<div class="reference"><h5 class="label">Reference:</h5> ' + instanceReference + '</div>');
				});
				
				$(resultsSection).append('<hr class="section-break" />');
			}
		}
		
		// Populate the results header area.
		Results.prototype.makeResultsHeader = function(){
			$('#query-results-info').html('Returned ' + this.totalSubjectCount + ($(this.totalSubjectCount).length > 1 ? ' results' : ' result') + ' with ' + this.totalInstanceCount + ' instances recorded.');
		}
		
		// Section toggles.
		Results.prototype.toggleListener = function(){
			$('.toggle').click(function(e){
				$(this).parent().toggleClass('closed');
			});
			
			$('.toggle-summary-all').click(function(e){
				if($(this).children('input').prop('checked')){
					$(this).closest('.results-subsection').find('.overflow').show();
				} else {
					$(this).closest('.results-subsection').find('.overflow').hide();
				}
			});
			
			$('.toggle-stats').click(function(e){
				if($(this).children('input').prop('checked')){
					$(this).closest('.instance-details').find('.species-list').addClass('expanded');
				} else {
					$(this).closest('.instance-details').find('.species-list').removeClass('expanded');
				}
			});
			
			$('.toggle-references').click(function(e){
				if($(this).children('input').prop('checked')){
					$(this).closest('.instance-details').find('.reference').show();
				} else {
					$(this).closest('.instance-details').find('.reference').hide();
				}
			});
		}
		
		Results.prototype.mapListner = function(){
			$('.map-link').click(function(e){
				e.preventDefault();
				
				var lat = $(this).attr('data-lat');
				var lon = $(this).attr('data-lon');
				var latLon = new google.maps.LatLng(lat, lon);
				
				if(lat && lon){
					var mapOptions = {
						center: latLon,
						zoom: 8,
						mapTypeId: google.maps.MapTypeId.TERRAIN
					};
					
					var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
					
					var marker = new google.maps.Marker({
						position: latLon,
						map: map
					});
					
					$.fancybox({
						'type': 'inline',
						'href': '#map-canvas',
						'onClosed': function(){
							$('div#hideaway .fancybox-inline-tmp').attr('id', 'map-canvas').attr('style','').removeClass('fancybox-inline-tmp');
						}
					});
				}
			});
		}
		
		
		
		/* ==============================
		   Query Form
		   ============================*/
		
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
		
		// Data query form submit action.
		$('form#data-query').submit(function(e){
			
			// Prevent actual form submission.  We'll do this with AJAX.
			e.preventDefault();
			
			// Clear the status container.
			$('#status').html('');
			
			// The query object that we'll submit via POST.
			var queryString = $(this).serialize();
			
			log('Query String:'); log(queryString);
			
			// POST to the WordPress Ajax system.
			$.post(
				
				// URL to the WordPress Ajax system.
				'/wp-admin/admin-ajax.php',
				
				// The object containing the POST data.
				queryString,
				
				// Success callback function.
				function(data, textStatus, jqXHR){
					// The function to process the results data.
					var r = new Results(data);
					
					r.prepResultsArea();
					r.makeSpecimenList();
					r.makeSubjects();
					r.makeResultsHeader();
					r.toggleListener();
					r.mapListner();
					
					// Show status on page.
					$('#status').html(textStatus);
					
					// Show raw results on page.
					$('#raw-results').html(data);
				}
			
			// Failure callback function.
			).fail(function(jqXHR, textStatus, errorThrown){
				
				// Show status on page.
				$('#status').html(textStatus);
				
				// Clear results area.
				$('#query-results').html('').hide();
				$('#raw-results').html('');
			});
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

$("a[href$='.jpg'],a[href$='.jpeg'],a[href$='.png'],a[href$='.gif']").fancybox();