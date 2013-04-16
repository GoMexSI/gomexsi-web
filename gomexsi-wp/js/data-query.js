jQuery(document).ready(function($) {

/* =============================================================================
   Data Query
   ========================================================================== */
	
	// Types of interactions available.
	var types = {
		// 'type' : ['singular', 'plural'],
		'prey' : ['prey', 'prey'],
		'pred' : ['predator', 'predators']
	};
	
	// Default mode for non-query pages.
	var mode = 'static';
	
	if($('body.page-template-data-query-taxonomic-php').length)
		mode = 'taxonomic';
	
	if($('body.page-template-data-query-spatial-php').length)
		mode = 'spatial';
	
	if($('body.page-template-data-query-exploration-php').length)
		mode = 'exploration';
	
	function modeIs(test){
		if(mode == test)
			return true;
		
		if((mode == 'taxonomic' || mode == 'spatial' || mode == 'exploration') && test == 'query')
			return true;
		
		// Otherwise…
		return false;
	}
	
	// Start by checking to see if we're on the data query page template.
	if(modeIs('query')){
		var subjectNameInput = $('form#data-query input[name="subjectName"]');
		$(subjectNameInput).focus();
		
		/* ==============================
		   Miscellaneous
		   ============================*/
		
		function nameTip(scientificName){
			return '<div class="name-tip-wrapper"><a href="#" class="name-tip-link">' + scientificName + '</a></div>';
		}
		
		function nameSafe(name){
			var safeName = name.replace(' ', '_').toLowerCase();
			return safeName;
		}
		
		function formatType(type, plural, capitalized){
			plural = (plural != null ? plural : false);
			capitalized = (capitalized != null ? capitalized : false);
			
			if(plural){
				var formattedType = types[type][1];
			} else {
				var formattedType = types[type][0];
			}
			
			if(capitalized){
				formattedType = capitalizeFirstLetter(formattedType);
			}
			
			return formattedType;
		}
		
		function capitalizeFirstLetter(string){
			return string.charAt(0).toUpperCase() + string.slice(1);
		}
		
		function getDate(UNIX_timestamp){
			var a = new Date(UNIX_timestamp);
			var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
			var year = a.getFullYear();
			var month = months[a.getMonth()];
			var date = a.getDate();
			var formatted = month + ' ' + date + ', ' + year;
			return formatted;
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
		
		// Iterate over prey, predators, etc., to get make cumulative lists and get some statistics.
		Results.prototype.processResults = function(){
			var totalInstanceCount = 0;
			var subjects = this.subjects;
			
			$.each(subjects, function(i){
				var subject = subjects[i];
				
				for(type in types){
					subject[type + 'InstanceCount'] = 0;
					subject[type + 'List'] = [];
					
					var instances = (typeof subject[type + 'Instances'] == 'object' ? subject[type + 'Instances'] : []);
					
					$.each(instances, function(j){
						var instance = instances[j];
						var skip = [];
						
						subject[type + 'InstanceCount']++;
						totalInstanceCount++;
						
						$.each(instance[type], function(k){
							var aTypeName = instance[type][k];
							var safeName = nameSafe(aTypeName);
							var inSkip = ($.inArray(safeName, skip) == -1 ? false : true);
							
							if(safeName in subject[type + 'List'] && !inSkip){
								skip.push(safeName);
								subject[type + 'List'][safeName].count++;
							} else if(!inSkip){
								skip.push(safeName);
								subject[type + 'List'][safeName] = {};
								subject[type + 'List'][safeName].scientificName = aTypeName;
								subject[type + 'List'][safeName].count = 1;
							}
						});
					});
				}
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
				
				for(type in types){
					// Check to see if we have instances of this type.
					if(subject[type + 'InstanceCount'] > 1){
						// Results section for type.
						var sectionID = subject.baseID + '-' + type + '-section';
						$('#' + subject.baseID).append('<div id="' + sectionID + '" class="results-section" />');
						var resultsSection = $('#' + sectionID);
						$(resultsSection).append('<h3 class="section-title toggle">' + formatType(type, true, true) + '</h3>');
						$(resultsSection).append('<div class="container" />');
						
						// Type summary.
						$(resultsSection).children('.container').append('<div class="results-subsection ' + type + '-summary" />');
						var typeSummary = $('#' + subject.baseID + ' .' + type + '-summary');
						$(typeSummary).append('<form><label class="view-option toggle-summary-all"><input type="checkbox" /> Show All ' + formatType(type, true, true) + '</label></form>');
						$(typeSummary).append('<h4 class="subsection-title toggle">' + formatType(type, true, true) + ' Summary</h4>');
						$(typeSummary).append('<div class="container" />');
						$(typeSummary).children('.container').append('<table class="summary"><tbody></tbody></table>');
						
						
						// Sort the prey list by number of instances for a given prey. We must dump the prey list into an array so it can be sorted.
						var typeList = subject[type + 'List'];
						var typeListDesc = [];
						
						for(var aType in typeList){
							typeListDesc.push({
								safeName: aType,
								scientificName: typeList[aType].scientificName,
								count: typeList[aType].count,
								percent: ( ( typeList[aType].count / subject[type + 'InstanceCount'] ) * 100 ).toFixed(2)
							});
						}
						
						// Sort using a custom function.
						typeListDesc.sort(function(a,b){
							// If the count is the same for two items, sort alphabetically ascending.
							if(b.count === a.count){
								return a.scientificName < b.scientificName ? -1 : a.scientificName > b.scientificName ? 1 : 0;
							}
							
							// Otherwise, sort by count descending.
							return b.count - a.count;
						});
						
						var rowCount = 0;
						
						$.each(typeListDesc, function(i){
							rowCount++;
							var aType = typeListDesc[i];
							if(rowCount < 11){
								var row = '<tr>';
							} else {
								var row = '<tr class="overflow">';
							}
							row += '<td class="species-name">' + nameTip(aType.scientificName) + '</td>';
							row += '<td class="percent-number">' + aType.percent + '%</td>';
							row += '<td class="percent-bar"><div class="percent-bar-total"><div class="percent-bar-value" style="width:' + aType.percent + '%"></div></div></td>';
							row += '</tr>';
							
							$('#' + subject.baseID + ' .' + type + '-summary table.summary').append(row);
						});
						
						// Prey Instance Details
						var instanceDetailsID = subject.baseID + '-' + type + '-instance-details';
						$(resultsSection).children('.container').append('<div id="' + instanceDetailsID + '" class="results-subsection instance-details" />');
						var typeInstanceDetails = $('#' + instanceDetailsID);
						$(typeInstanceDetails).append('<form><label class="view-option toggle-references"><input type="checkbox" /> References</label><label class="view-option toggle-stats"><input type="checkbox" /> ' + formatType(type, true, true) + ' Stats</label></form>');
						$(typeInstanceDetails).append('<h4 class="subsection-title toggle">Instance Details</h4>');
						$(typeInstanceDetails).append('<div class="container" />');
						
						$.each(subject[type + 'Instances'], function(i){
							var instance = subject[type + 'Instances'][i];
							
							var instanceID = subject.baseID + '-' + type + '-instance-' + i;
							
							$(typeInstanceDetails).children('.container').append('<div id="' + instanceID + '" class="single-instance clearfix" />');
							var singleInstance = $('#' + instanceID);
							
							var instanceDate = ('date' in instance ? instance.date : 'unknown');
							if(instanceDate){
								instanceDate = getDate(instanceDate);
							} else {
								instanceDate = 'unknown';
							}
							$(singleInstance).append('<div class="date"><h5 class="label">Date:</h5> ' + instanceDate + '</div>');
							
							var instanceLocation = ('loc' in instance ? instance.loc : 'Unnamed location.');
							var lat = ('lat' in instance ? instance.lat : '');
							var long = ('long' in instance ? instance.long : '');
							$(singleInstance).append('<div class="location"><h5 class="label">Location:</h5> ' + instanceLocation + ' <a href="#map-canvas" class="map-link" data-lat="' + lat + '" data-lon="' + long + '">Map</a></div>');
							
							var instanceTypeListID = instanceID + '-' + type + '-list';
							$(singleInstance).append('<div class="prey species-list"><h5 class="label">' + formatType(type, true, true) + ':</h5><ul id="' + instanceTypeListID + '"></ul></div>');
							$.each(instance[type], function(j){
								var aTypeName = instance[type][j];
								
								var li = '<li class="clearfix">';
								li += '<div class="name">' + nameTip(aTypeName) + '</div>';
								li += '<div class="details">Details go here</div>';
								li += '</li>';
								
								$('#' + instanceTypeListID).append(li);
							});
							
							var instanceReference = ('ref' in instance ? instance.ref : 'Reference unknown.');
							$(singleInstance).append('<div class="reference"><h5 class="label">Reference:</h5> ' + instanceReference + '</div>');
						});
						
						$(resultsSection).append('<hr class="section-break" />');
						
					}
				}
			});
		}
		
		// Populate the results header area.
		Results.prototype.makeResultsHeader = function(){
			$('#query-results-info').html('Returned ' + this.totalSubjectCount + ($(this.totalSubjectCount).length > 1 ? ' results' : ' result') + ' with ' + this.totalInstanceCount + ' instances recorded.');
		}
		
		// Section toggles and name links.
		Results.prototype.resultsListeners = function(){
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
			
			$('.name-tip-link').click(function(e){
				e.preventDefault();
				e.stopPropagation();
				
				// Remove any existing name tip boxes.
				$('.name-tip-box').remove();

				var scientificName = $(this).html();
				var wrapper = $(this).parent('.name-tip-wrapper');
				$(wrapper).append('<div class="name-tip-box"><div class="container"><ul></ul></div><div class="bridge"></div></div>');
				var linkList = $(wrapper).find('ul');
				$(linkList).append('<li><a href="/query-database/exploration/" class="ex-link">View in Explorer Mode<form class="visuallyhidden" method="post" action="/query-database/exploration/"><input type="hidden" name="subjectName" value="' + scientificName + '" /></form></a></li>')
				
				// POST to the WordPress Ajax system.
				$.post(
					// URL to the WordPress Ajax system.
					'/wp-admin/admin-ajax.php',
					
					// The object containing the POST data.
					{
						url : 'http://gomexsi.tamucc.edu/gomexsi/requestHandler/RequestHandler.php',
						action : 'rhm_data_query',
						deepLinks : scientificName
					},
					
					// Success callback function.
					function(data, textStatus, jqXHR){
						log(data);
						//nameTip += '<li><a href="http://fishbase.org/summary/' + scientificName.replace(' ', '-') + '.html" class="external" target="_blank">View on FishBase.org</a></li>';
					}
				
				// Failure callback function.
				).fail(function(jqXHR, textStatus, errorThrown){
					
				});
			});
			
			$('body').click(function(e){
				// If a name tip box is open, then clicking anywhere else will remove it.
				$('.name-tip-box').remove();
			});
			
			$('.ex-link').click(function(e){
				e.preventDefault();
				$(this).children('form').submit();
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
		
		// Exploration Mode
		Results.prototype.makeExArea = function(){
			$('#ex-area').html('');
			
			var subject = this.subjects[0];
			
			makeExPred(subject);
			
			var subjectID = 'subject-' + nameSafe(subject.scientificName);
			$('#ex-area').append('<div id="' + subjectID + '" class="ex-subject ex-species gradient" data-sci-name="' + subject.scientificName + '">' + subject.scientificName + '</div>');
			
			makeExPrey(subject);
		}
		
		function makeExPred(subject){
			if(subject.predInstanceCount < 1)
				return false;
			
			var exArea = $('#ex-area');
			
			for(var predKey in subject.predList){
				var pred = subject.predList[predKey];
				var predID = 'pred-' + nameSafe(pred.scientificName);
				
				$(exArea).append('<div id="' + predID + '" class="ex-pred ex-species gradient" data-sci-name="' + pred.scientificName + '"><div class="ex-label">Predator</div>' + pred.scientificName + '</div>');
			}
		}
		
		function makeExPrey(subject){
			if(subject.preyInstanceCount < 1)
				return false;
			
			var exArea = $('#ex-area');
			
			for(var preyKey in subject.preyList){
				var prey = subject.preyList[preyKey];
				var preyID = 'prey-' + nameSafe(prey.scientificName);
				
				$(exArea).append('<div id="' + preyID + '" class="ex-prey ex-species gradient" data-sci-name="' + prey.scientificName + '"><div class="ex-label">Prey</div>' + prey.scientificName + '</div>');
			}
		}
		
		Results.prototype.makeExLines = function(){
			jsPlumb.Defaults.Container = 'ex-area';
			jsPlumb.Defaults.PaintStyle = {
				lineWidth: 4,
				strokeStyle: '#990014'
			}
			
			var subjectID = $('.ex-subject').first().attr('id');
			var subjectBottomEndpoint, subjectTopEndpoint;
			
			$.each($('#ex-area .ex-pred'), function(){
				subjectTopEndpoint = jsPlumb.addEndpoint(subjectID, {
					anchor: 'TopCenter',
					endpoint: 'Blank'
				});
				
				var predID = $(this).attr('id');
				var predEndpoint = jsPlumb.addEndpoint(predID, {
					anchor: 'BottomCenter',
					endpoint: 'Blank'
				});
				
				jsPlumb.connect({
					source: subjectTopEndpoint,
					target: predEndpoint,
					connector: ['Straight', {strokeStyle: '#'}]
				});
				
			});
			
			jsPlumb.Defaults.PaintStyle = {
				lineWidth: 4,
				strokeStyle: '#2e9900'
			}
			
			$.each($('#ex-area .ex-prey'), function(){
				subjectBottomEndpoint = jsPlumb.addEndpoint(subjectID, {
					anchor: 'BottomCenter',
					endpoint: 'Blank'
				});

				var preyID = $(this).attr('id');
				var preyEndpoint = jsPlumb.addEndpoint(preyID, {
					anchor: 'TopCenter',
					endpoint: 'Blank'
				});
				
				jsPlumb.connect({
					source: preyEndpoint,
					target: subjectBottomEndpoint,
					connector: 'Straight'
				});
				
			});
		}
		
		Results.prototype.exListeners = function(){
			$('.ex-species').click(function(e){
				var sciName = $(this).attr('data-sci-name');
				
				$('form#data-query input[name="subjectName"]').val(sciName);
				$('form#data-query').submit();
			});
		}
		
		
		/* ==============================
		   Query Form
		   ============================*/
		
		// Conditional switch.
		$('.switch').click(function(e){
			toggleSwitch($(this));
		});
		
		function toggleSwitch(theSwitch){
			var checked = $(theSwitch).prop('checked');
			var switchName = $(theSwitch).attr('data-switch');
			var conditional = $('.conditional[data-switch="'+switchName+'"]');
			if(checked){
				$(conditional).show();
			} else {
				$(conditional).hide();
				$(conditional).find('input[type="checkbox"], input[type="radio"]').prop('checked', false);
				$(conditional).find('input[type="text"], textarea').val('');
				$(conditional).find('select').prop('selectedIndex', 0);
			}
		}

		// Master checkbox.
		$('.master-checkbox').click(function(e){
			var section = $(this).closest('.form-section');
			var checkboxes = checkboxCheck(section);
			
			if(checkboxes.unchecked > 0){
				$(this).prop('indeterminate', false);
				$(this).prop('checked', true);
			} else {
				$(this).prop('checked', false);
			}
			
			var checked = $(this).prop('checked');
			
			var checkboxInputs = $(section).find('input[type="checkbox"]').not('.master-checkbox');
			$(checkboxInputs).prop('checked', checked).each(function(i){
				toggleSwitch($(checkboxInputs)[i]);
			});
		});
		
		$('input[type="checkbox"]').not('.master-checkbox').click(function(e){
			var section = $(this).closest('.form-section');
			var checkboxes = checkboxCheck(section);
			
			if(checkboxes.checked && checkboxes.unchecked){
				$(section).find('.master-checkbox').prop('indeterminate', true);
			} else if(checkboxes.checked && checkboxes.unchecked == 0){
				$(section).find('.master-checkbox').prop('indeterminate', false);
				$(section).find('.master-checkbox').prop('checked', true);
			} else if(checkboxes.checked == 0 && checkboxes.unchecked){
				$(section).find('.master-checkbox').prop('indeterminate', false);
				$(section).find('.master-checkbox').prop('checked', false);
			}
		});
		
		function checkboxCheck(section){
			var checked = 0;
			var unchecked = 0;
			
			$(section).find('input[type="checkbox"]').not('.master-checkbox').each(function(i){
				if($(this).prop('checked')){
					checked++;
				} else {
					unchecked++;
				}
			});
			
			var checkboxes = { 'checked': checked, 'unchecked': unchecked };
			
			return checkboxes;
		}
		
		
		// Query Map
		if(modeIs('spatial')){
			var qMapLatLon = new google.maps.LatLng(25, -90);
			
			var qMapOptions = {
				center: qMapLatLon,
				zoom: 5,
				mapTypeId: google.maps.MapTypeId.TERRAIN
			};
			
			var qMap = new google.maps.Map(document.getElementById('query-map'), qMapOptions);
			
			var qShape;
			
			var qShapeBounds = new google.maps.LatLngBounds(
				new google.maps.LatLng(20, -100),
				new google.maps.LatLng(30, -80)
			);
			
			qShape = new google.maps.Rectangle({
				bounds: qShapeBounds,
				map: qMap,
				editable: true,
				strokeColor: "#ffff00",
				strokeOpacity: 1,
				strokeWeight: 1,
				fillColor: "#ffff00",
				fillOpacity: 0.1
			});
			
			function updateBounds(bounds){
				var boundNE = bounds.getNorthEast();
				var boundSW = bounds.getSouthWest();
				var boundN = boundNE.jb;
				var boundE = boundNE.kb;
				var boundS = boundSW.jb;
				var boundW = boundSW.kb;
				
				$('form#data-query input[name="boundNorth"]').val(boundN);
				$('form#data-query input[name="boundEast"]').val(boundE);
				$('form#data-query input[name="boundSouth"]').val(boundS);
				$('form#data-query input[name="boundWest"]').val(boundW);
			}
			
			updateBounds(qShape.bounds);
			
			google.maps.event.addListener(qShape, 'bounds_changed', function() {
				updateBounds(qShape.bounds);
			});
		}
		
		// Data query form submit action.
		$('form#data-query').submit(function(e){
			
			// Prevent actual form submission.  We'll do this with AJAX.
			e.preventDefault();
			
			// The query object that we'll submit via POST.
			var queryString = $(this).serialize();
			
			// Make sure there is a valid query.
			var validInteraction = false;
			var validSubject = false;
			var queryError = '';
			
			// Must have a name in the subjectName field.
			if($('input[name="subjectName"]').val()){
				validSubject = true;
			} else {
				queryError += '<p><img src="/wp-content/themes/gomexsi-wp/img/error.png" alt="Error" style="position: relative; top: 2px" /> Please enter a name or taxonomy in the <em><strong>Name</strong></em> section.</p>';
			}
			
			// Must have at least one interaction type selected.
			var queryTypes = ['findPrey','findPredators','findParasites','findMutualists','findCommonsals','findAmensals','findPrimaryHosts','findSecondaryHosts'];
			for(var i = 0; i < queryTypes.length; i++){
				if(queryString.indexOf(queryTypes[i]) != -1){
					validInteraction = true;
				}
			}
			
			if(!validInteraction){
				queryError += '<p><img src="/wp-content/themes/gomexsi-wp/img/error.png" alt="Error" style="position: relative; top: 2px" /> Please select at least one type of interaction in the <em><strong>Find</strong></em> section.</p>';
			}
			
			// If either case is not satisfied, show the error message and return false.
			if(!validInteraction || !validSubject){
				$.fancybox({
					padding: 20,
					content: queryError,
					centerOnScroll: true
				});
				return false;
			}
			
			// Clear the status container.
			$('#status').html('');
			
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
					
					r.processResults();
					
					if(modeIs('taxonomic') || modeIs('spatial')){
						r.makeSubjects();
						r.makeResultsHeader();
						r.resultsListeners();
						r.mapListner();
					} else {
						r.makeExArea();
						r.makeExLines();
						r.exListeners();
					}
					
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
		
		if($(subjectNameInput).val()){
			$('form#data-query').submit();
		}
		

	}
	
});