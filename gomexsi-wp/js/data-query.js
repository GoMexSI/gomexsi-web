jQuery(document).ready(function ($) {

    /* =============================================================================
     Data Query
     ========================================================================== */

    // Types of interactions available.
    var types = {
        // 'type' : ['singular', 'plural'],
        'prey': [_q('prey', 'presa'), _q('prey', 'presas')],
        'pred': [_q('predator', 'predador'), _q('predators', 'predadores')]
    };

    // Default mode for non-query pages.
    var mode = 'static';

    if ($('body.page-template-data-query-taxonomic-php').length)
        mode = 'taxonomic';

    if ($('body.page-template-data-query-spatial-php').length)
        mode = 'spatial';

    if ($('body.page-template-data-query-universal-php').length)
        mode = 'spatial';

    if ($('body.page-template-data-query-exploration-php').length)
        mode = 'exploration';

    function modeIs(test) {
        if (mode == test)
            return true;

        if ((mode == 'taxonomic' || mode == 'spatial' || mode == 'exploration') && test == 'query')
            return true;

        // Otherwise…
        return false;
    }

    function nameSafe(name) {
        return name.replace(/[^a-zA-Z0-9-_.]/g, '_').toLowerCase();
    }

    // Start by checking to see if we're on the data query page template.
    if (modeIs('query')) {
        var subjectNameInput = $('form#data-query input[name="subjectName"]');

        /* ==============================
         Miscellaneous
         ============================*/

        function nameTip(scientificName) {
            return '<div class="name-tip-wrapper"><a href="#" class="name-tip-link">' + scientificName + '</a></div>';
        }

        function formatType(type, plural, capitalized) {
            plural = (plural != null ? plural : false);
            capitalized = (capitalized != null ? capitalized : false);

            if (plural) {
                var formattedType = types[type][1];
            } else {
                var formattedType = types[type][0];
            }

            if (capitalized) {
                formattedType = capitalizeFirstLetter(formattedType);
            }

            return formattedType;
        }

        function capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }

        function getDate(UNIX_timestamp) {
            var a = new Date(UNIX_timestamp);
            var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            var year = a.getFullYear();
            var month = months[a.getMonth()];
            var date = a.getDate();
            var formatted = month + ' ' + date + ', ' + year;
            return formatted;
        }

        function arraysEqual(arr1, arr2) {
            if (arr1.length !== arr2.length)
                return false;
            for (var i = arr1.length; i--;) {
                if (arr1[i] !== arr2[i])
                    return false;
            }
            return true;
        }

        function arrayContainsArray(containerArray, itemArray) {
            for (var i = containerArray.length; i--;) {
                if (arraysEqual(containerArray[i], itemArray)) {
                    return true;
                }
            }
            return false;
        }

        /* ==============================
         Fuzzy Search Suggestions
         ============================*/

        // Handle for the suggestion setTimeout() function.
        // Declared outside the function so it can be cancelled by a separate instance of the function.
        var sugTimeout;

        // Fuzzy search suggestions.
        $('input.taxonomic').keydown(function (e) {
            // Which key was pressed? (Key code number.)
            var key = e.which;

            // Convenience handles.
            var taxWrap = $(this).parent('.tax-wrapper');
            var taxSugList = $(taxWrap).find('.tax-suggestions');
            var taxSugItems = $(taxWrap).find('.tax-suggestion');

            // Index of the currently selected suggestion in the list.
            var i = $(taxWrap).find('.tax-suggestion.selected').index();

            // Down arrow.
            if (key == '40') {
                if (i < (taxSugItems.length - 1)) {
                    i++;
                    var value = $(taxSugItems).removeClass('selected').eq(i).addClass('selected').text();
                    $(this).val(value);
                }
            }

            // Up arrow.
            if (key == '38') {
                if (i > 0) {
                    i--;
                    var value = $(taxSugItems).removeClass('selected').eq(i).addClass('selected').text();
                    $(this).val(value);
                } else {
                    i = -1;
                    $(taxSugItems).removeClass('selected')
                }
            }

            // Tab or enter or escape.
            if (key == '9' || key == '13' || key == '27') {
                if ($(taxWrap).find('ul.tax-suggestions').length) {
                    e.preventDefault();
                    $('ul.tax-suggestions').remove();
                }
            }

            // Don't trigger another set of suggestions when the input changes to the selected suggestion.
            clearTimeout(sugTimeout);
        });

        $('input.taxonomic').keyup(function (e) {
            // Which key was pressed? (Key code number.)
            var key = e.which;

            // Convenience handles.
            var taxWrap = $(this).parent('.tax-wrapper');
            var taxSugList = $(taxWrap).find('.tax-suggestions');
            var taxSugItems = $(taxWrap).find('.tax-suggestion');

            // These keys are modifiers or arrow keys that should not trigger a suggestion request.
            var noTriggerKeys = [16, 17, 18, 20, 37, 38, 39, 40, 91, 93, 27, 110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123, 124, 125, 126, 127, 128, 129, 130];

            // If not an arrow or modifier key.
            if ($.inArray(key, noTriggerKeys) < 0) {
                // Cancel any previous request.
                clearTimeout(sugTimeout);

                // The value to send for suggestions.
                var sugValue = $(this).val();

                // If we have a suggestion fragment.
                if (sugValue.length) {
                    // Put this action on a 250 millisecond delay to avoid unnecessary requests.
                    sugTimeout = setTimeout(function () {
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
                            function (data, textStatus, jqXHR) {
                                if (data) {
                                    // Convert to JSON.
                                    results = JSON && JSON.parse(data) || $.parseJSON(data);

                                    var suggestions = results[0].matches;

                                    // Remove the old suggestion list.
                                    if ($(taxWrap).find('ul.tax-suggestions').length) {
                                        $(taxWrap).find('ul.tax-suggestions').remove();
                                    }

                                    // If we have at least one suggestion and it is not identical to the text in the input.
                                    if ($(suggestions).length > 0 && suggestions[0] != sugValue) {
                                        // Attach a new suggestion list.
                                        $(taxWrap).append('<ul class="tax-suggestions" />');
                                        var taxSugList = $(taxWrap).find('ul.tax-suggestions');

                                        // Add each suggestion to the list.
                                        $.each(suggestions, function (i) {
                                            $(taxSugList).append('<li class="tax-suggestion">' + suggestions[i] + '</li>');
                                        });

                                        // Event listener for suggestion items to make them clickable.
                                        suggestionClickListener();
                                    }
                                }
                            }
                        );
                    }, 100); // 100 millisecond delay.
                } else {
                    // If we don't have a suggestion fragment (e.g., the user deleted all text from the input) remove the suggestion list.
                    $(taxWrap).find('ul.tax-suggestions').remove();
                }
            }
        });

        // If the user clicks anywhere on the page outside of the input box or the suggestion list, remove the list.
        $('body').click(function (e) {
            $('ul.tax-suggestions').remove();
        });

        // Attach click event listener to suggestion list items. This function must be run after the list items are created.
        function suggestionClickListener() {
            $('li.tax-suggestion').click(function (e) {
                // Stop propagation so that this does not count as a body click (and thereby remove the list).
                //e.stopPropagation();
                $(this).parent().children().removeClass('selected');
                var value = $(this).addClass('selected').text();
                $(this).closest('.tax-wrapper').children('input.taxonomic').val(value);

                // Stop the suggestion function from triggering because the input changed.
                clearTimeout(sugTimeout);
            });
        }

        $('input.taxonomic').click(function (e) {
            // Stop propagation so that this does not count as a body click (and thereby remove the list).
            e.stopPropagation();
        });

        /* ==============================
         Results
         ============================*/

        // We'll need this to keep track of all the locations where we have results.
        var resultLocations = Array();

        function Results(data) {
            if (data) {
                try {
                    this.subjects = JSON && JSON.parse(data) || $.parseJSON(data);
                    log('JSON Raw Results Object:'); log(data);
                    //log('JSON Parsed Results Object:'); log(this.subjects);
                    //log('Processed Data Object:'); log(this);
                }
                catch (err) {
                    this.subjects = '';
                    //log('Error message: ' + err.message);
                    //log('Unparsed Results String:'); log(this);
                }
            } else {
                this.subjects = '';
                //log('No data detected. Raw output from RequestHandler.php:'); log(data);
            }
        }

        // Iterate over prey, predators, etc., to get make cumulative lists and get some statistics.
        Results.prototype.processResults = function () {
            var totalInstanceCount = 0;
            var subjects = this.subjects;

            $.each(subjects, function (i) {
                var subject = subjects[i];

                for (type in types) {
                    subject[type + 'InstanceCount'] = 0;
                    subject[type + 'List'] = [];

                    var instances = (typeof subject[type + 'Instances'] === 'object' ? subject[type + 'Instances'] : []);

                    $.each(instances, function (j) {
                        var instance = instances[j];
                        var skip = [];

                        subject[type + 'InstanceCount']++;
                        totalInstanceCount++;

                        // Add this location to the list of results locations. This will later be used to color-code the markers on the map of the spatial query page.
                        var lat = ('lat' in instance ? instance.lat : false);
                        var long = ('long' in instance ? instance.long : false);
                        var instanceLatLong = [lat, long];
                        if (!arrayContainsArray(resultLocations, instanceLatLong)) {
                            resultLocations.push(instanceLatLong);
                        }

                        $.each(instance[type + 'Data'], function (k) {
                            var singleTypeName = instance[type + 'Data'][k][type];
                            var safeName = nameSafe(singleTypeName);
                            var inSkip = ($.inArray(safeName, skip) == -1 ? false : true);

                            if (safeName in subject[type + 'List'] && !inSkip) {
                                skip.push(safeName);
                                subject[type + 'List'][safeName].count++;
                            } else if (!inSkip) {
                                skip.push(safeName);
                                subject[type + 'List'][safeName] = {};
                                subject[type + 'List'][safeName].scientificName = singleTypeName;
                                subject[type + 'List'][safeName].count = 1;
                            }
                        });
                    });
                }
            });

            this.totalInstanceCount = totalInstanceCount;

            if (modeIs('spatial')) {
                log(resultLocations);
                log(markersInGulf);

                var zIndex = markersInGulf.length;

                var north = parseFloat($('form#data-query input[name="boundNorth"]').val());
                var east = parseFloat($('form#data-query input[name="boundEast"]').val());
                var south = parseFloat($('form#data-query input[name="boundSouth"]').val());
                var west = parseFloat($('form#data-query input[name="boundWest"]').val());

                var searchCoords = [
                    {lat: north, lng: east},
                    {lat: south, lng: east},
                    {lat: south, lng: west},
                    {lat: north, lng: west}
                ];

                var searchPoly = new google.maps.Polygon({paths: searchCoords});

                for (var i = markersInGulf.length; i--;) {
                    var markerPosition = markersInGulf[i].getPosition();

                    if (google.maps.geometry.poly.containsLocation(markerPosition, searchPoly)) {
                        var markerPositionArray = [markerPosition.lat(), markerPosition.lng()];
                        if (arrayContainsArray(resultLocations, markerPositionArray)) {
                            markersInGulf[i].setIcon('/wp-content/themes/gomexsi-wp/img/map_marker_dot_green.png');
                            zIndex++;
                            markersInGulf[i].setZIndex(zIndex);
                        } else {
                            markersInGulf[i].setIcon('/wp-content/themes/gomexsi-wp/img/map_marker_dot_gray_50.png');
                        }
                    } else {
                        markersInGulf[i].setIcon('/wp-content/themes/gomexsi-wp/img/map_marker_dot_red.png');
                    }
                }
            }
        };

        // Make the subject blocks on the page.
        Results.prototype.makeSubjects = function () {
            this.totalSubjectCount = $(this.subjects).length;
            var subjects = this.subjects;

            $('#results-area').empty();

            $.each(subjects, function (i) {
                var subject = subjects[i];

                subject.baseID = nameSafe(subject.scientificName);
                $('#results-area').append('<div id="' + subject.baseID + '" class="query-results" />');
                var subjectTitleID = subject.baseID + '-title';
                $('#' + subject.baseID).append('<h2 id="' + subjectTitleID + '" class="subject-name">' + nameTip(subject.scientificName) + ' <span class="common-name"></span></h2>');

                if ('commonNames' in subject) {
                    var commonNames = subject.commonNames;
                    var commonNamesText = '&nbsp;&ndash;&nbsp; ';

                    $.each(commonNames, function (j) {
                        commonNamesText += commonNames[j];
                        if ((j + 1) < $(commonNames).length) {
                            commonNamesText += ', ';
                        }
                    });
                    $('#' + subjectTitleID + ' .common-name').html(commonNamesText);
                }

                for (var type in types) {
                    // Check to see if we have instances of this type.
                    if (subject[type + 'InstanceCount'] > 1) {
                        // Results section for type.
                        var sectionID = subject.baseID + '-' + type + '-section';
                        $('#' + subject.baseID).append('<div id="' + sectionID + '" class="results-section" />');
                        var resultsSection = $('#' + sectionID);
                        $(resultsSection).append('<h3 class="section-title toggle">' + formatType(type, true, true) + '</h3>');
                        $(resultsSection).append('<div class="container" />');

                        // Type summary.
                        $(resultsSection).children('.container').append('<div class="results-subsection ' + type + '-summary" />');
                        var typeSummary = $('#' + subject.baseID + ' .' + type + '-summary');
                        if (qtranx_language !== 'es') {
                            // English
                            $(typeSummary).append('<form><label class="view-option toggle-summary-all"><input type="checkbox" /> Show All ' + formatType(type, true, true) + '</label> <div class="top-ten-note view-option">Top ten items shown.</div></form>');
                        } else {
                            // Spanish
                            if (formatType(type) === 'presa') {
                                $(typeSummary).append('<form><label class="view-option toggle-summary-all"><input type="checkbox" /> Mostrar Todas las ' + formatType(type, true, true) + '</label> <div class="top-ten-note view-option">Se muestran los diez principales elementos.</div></form>');
                            } else if (formatType(type) === 'predador') {
                                $(typeSummary).append('<form><label class="view-option toggle-summary-all"><input type="checkbox" /> Mostrar Todos los ' + formatType(type, true, true) + '</label> <div class="top-ten-note view-option">Se muestran los diez principales elementos.</div></form>');
                            }
                        }
                        if (qtranx_language !== 'es') {
                            // English
                            $(typeSummary).append('<h4 class="subsection-title toggle">' + formatType(type, true, true) + ' Summary</h4>');
                        } else {
                            // Spanish
                            $(typeSummary).append('<h4 class="subsection-title toggle">Resumen de ' + formatType(type, true, true) + '</h4>');
                        }
                        $(typeSummary).append('<div class="container" />');
                        $(typeSummary).children('.container').append('<table class="summary"><tbody></tbody></table>');
                        $(typeSummary).children('.container').append('<div class="summary-description">' + _q('Percent frequency of occurrence over all instances queried.', 'Percentage de frecuencia de ocurrencia de todos los elementos consultados.') + '</div>');

                        // Sort the prey list by number of instances for a given prey. We must dump the prey list into an array so it can be sorted.
                        var typeList = subject[type + 'List'];
                        var typeListDesc = [];

                        for (var aType in typeList) {
                            typeListDesc.push({
                                safeName: aType,
                                scientificName: typeList[aType].scientificName,
                                count: typeList[aType].count,
                                percent: ( ( typeList[aType].count / subject[type + 'InstanceCount'] ) * 100 ).toFixed(2)
                            });
                        }

                        // Sort using a custom function.
                        typeListDesc.sort(function (a, b) {
                            // If the count is the same for two items, sort alphabetically ascending.
                            if (b.count === a.count) {
                                return a.scientificName < b.scientificName ? -1 : a.scientificName > b.scientificName ? 1 : 0;
                            }

                            // Otherwise, sort by count descending.
                            return b.count - a.count;
                        });

                        var rowCount = 0;

                        $.each(typeListDesc, function (i) {
                            rowCount++;
                            var aType = typeListDesc[i];
                            if (rowCount < 11) {
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

                        // Type Instance Details
                        var instanceDetailsID = subject.baseID + '-' + type + '-instance-details';
                        $(resultsSection).children('.container').append('<div id="' + instanceDetailsID + '" class="results-subsection instance-details" />');
                        var typeInstanceDetails = $('#' + instanceDetailsID);
                        if (qtranx_language != 'es') {
                            // English
                            $(typeInstanceDetails).append('<form><label class="view-option toggle-references"><input type="checkbox" /> References</label><label class="view-option toggle-stats"><input type="checkbox" /> ' + formatType(type, true, true) + ' Stats</label></form>');
                        } else {
                            // Spanish
                            if (formatType(type) == 'presa') {
                                $(typeInstanceDetails).append('<form><label class="view-option toggle-references"><input type="checkbox" /> Referencias</label><label class="view-option toggle-stats"><input type="checkbox" /> Estadísticas de las ' + formatType(type, true, true) + '</label></form>');
                            } else if (formatType(type) == 'predador') {
                                $(typeInstanceDetails).append('<form><label class="view-option toggle-references"><input type="checkbox" /> Referencias</label><label class="view-option toggle-stats"><input type="checkbox" /> Estadísticas de los ' + formatType(type, true, true) + '</label></form>');
                            }
                        }
                        $(typeInstanceDetails).append('<h4 class="subsection-title toggle">' + _q('Instance Details', 'Detalles de Elemento') + '</h4>');
                        $(typeInstanceDetails).append('<div class="container" />');

                        $.each(subject[type + 'Instances'], function (i) {
                            var instance = subject[type + 'Instances'][i];

                            var instanceID = subject.baseID + '-' + type + '-instance-' + i;

                            $(typeInstanceDetails).children('.container').append('<div id="' + instanceID + '" class="single-instance clearfix" />');
                            var singleInstance = $('#' + instanceID);

                            var instanceNumber = i + 1;
                            $(singleInstance).append('<div class="instance-number">' + instanceNumber + '</div>');

                            var instanceDate = ('date' in instance ? parseInt(instance.date) : 'unknown');
                            if (instanceDate) {
                                instanceDate = getDate(instanceDate);
                            } else {
                                instanceDate = _q('Unknown', 'Desconocida');
                            }
                            $(singleInstance).append('<div class="date"><h5 class="label">' + _q('Date Collected:', 'Fecha de Colecta:') + '</h5> ' + instanceDate + '</div>');

                            var instanceLocation = ('loc' in instance ? instance.loc : _q('Unnamed Location', 'Lugar Desconocido'));
                            var lat = ('lat' in instance ? instance.lat : '');
                            var long = ('long' in instance ? instance.long : '');
                            var footprintWKT = ('footprintWKT' in instance ? instance.footprintWKT : '');
                            $(singleInstance).append('<div class="location"><h5 class="label">' + _q('Location:', 'Lugar:') + '</h5> ' + instanceLocation + ' <a href="#map-canvas" class="map-link" data-lat="' + lat + '" data-lon="' + long + '" data-footprintWKT="' + footprintWKT + '">' + _q('Map', 'Mapa') + '</a></div>');

                            var instanceTypeListID = instanceID + '-' + type + '-list';
                            $(singleInstance).append('<div class="prey species-list"><h5 class="label">' + formatType(type, true, true) + ':</h5><ul id="' + instanceTypeListID + '"></ul></div>');
                            $.each(instance[type + 'Data'], function (j) {
                                var singleTypeName = instance[type + 'Data'][j][type];

                                var singleDetails = [];

                                var singleLifeStage = instance[type + 'Data'][j][type + 'LifeStage'];
                                var singleBodyPart = instance[type + 'Data'][j][type + 'BodyPart'];
                                var singlePhysiologicalState = instance[type + 'Data'][j][type + 'PhysiologicalState'];

                                if (typeof singleLifeStage !== 'undefined' && singleLifeStage && singleLifeStage.toLowerCase() !== 'unknown') {
                                    singleDetails.push(singleTypeName + ' ' + singleLifeStage.toLowerCase());
                                }
                                if (typeof singleBodyPart !== 'undefined' && singleBodyPart && singleBodyPart.toLowerCase() !== 'unknown') {
                                    singleDetails.push(singleBodyPart);
                                }
                                if (typeof singlePhysiologicalState !== 'undefined' && singlePhysiologicalState && singlePhysiologicalState.toLowerCase() !== 'unknown') {
                                    singleDetails.push(singlePhysiologicalState);
                                }
                                if (singleDetails.length == 0) {
                                    singleDetails.push(_q('No details.', 'Sin detalles.'));
                                }

                                var li = '<li class="clearfix">';
                                li += '<div class="name">' + nameTip(singleTypeName) + '</div>';
                                li += '<div class="details">';
                                $.each(singleDetails, function (k) {
                                    if (k > 0) {
                                        li += ', ';
                                    }

                                    li += singleDetails[k];
                                });
                                li += '</div>';
                                li += '</li>';

                                $('#' + instanceTypeListID).append(li);
                            });

                            var instanceReference = ('ref' in instance ? instance.ref : 'Reference unknown.');
                            $(singleInstance).append('<div class="reference"><h5 class="label">Reference:</h5> <div class="ref-tag-wrapper"><a href="#" class="ref-tag-link">' + instanceReference + '</a></div></div>');
                        });

                        $(resultsSection).append('<hr class="section-break" />');

                    }
                }
            });
        }

        // Populate the results header area.
        Results.prototype.makeResultsHeader = function () {
            if (qtranx_language != 'es') {
                // English
                $('#query-results-info').html('Returned ' + this.totalSubjectCount + ($(this.totalSubjectCount).length > 1 ? ' results' : ' result') + ' with ' + this.totalInstanceCount + ' instances recorded.');
            } else {
                // Spanish
                $('#query-results-info').html(this.totalSubjectCount + ($(this.totalSubjectCount).length > 1 ? ' resultados encontrados' : ' resultado encontrado') + ' con ' + this.totalInstanceCount + ' casos registrados.');
            }
            $('#query-results-download, #nametip-instructions').removeClass('visuallyhidden');
        }


        // Exploration Mode
        Results.prototype.clearExArea = function () {
            jsPlumb.Defaults.Container = 'ex-area';

            var subjectID = $('.ex-subject').first().attr('id');
            jsPlumb.remove(subjectID);

            var exPred = $('#ex-area .ex-pred');

            $.each(exPred, function (i) {
                try {
                    var predID = $(this).attr('id');
                    jsPlumb.remove(predID);
                } catch (err) {
                    log(err);
                }
            });

            var exPrey = $('#ex-area .ex-prey');

            $.each(exPrey, function (i) {
                try {
                    var preyID = $(this).attr('id');
                    jsPlumb.remove(preyID);
                } catch (err) {
                    log(err);
                }
            });
        }

        Results.prototype.makeExArea = function () {
            $('#ex-area').html('');

            var subject = this.subjects[0];

            makeExPred(subject);

            var subjectID = 'subject-' + nameSafe(subject.scientificName);
            $('#ex-area').append('<div id="' + subjectID + '" class="ex-subject ex-species gradient" data-sci-name="' + subject.scientificName + '">' + nameTip(subject.scientificName) + '</div>');

            makeExPrey(subject);
        }

        function makeExPred(subject) {
            if (subject.predInstanceCount < 1)
                return false;

            var exArea = $('#ex-area');

            for (var predKey in subject.predList) {
                var pred = subject.predList[predKey];
                var predID = 'pred-' + nameSafe(pred.scientificName);

                $(exArea).append('<div id="' + predID + '" class="ex-pred ex-species gradient" data-sci-name="' + pred.scientificName + '"><div class="ex-label">Predator</div>' + nameTip(pred.scientificName) + '<a class="ex-link" href="/query-database/exploration/?subjectName=' + encodeURI(pred.scientificName) + '&findPrey=on&findPredators=on&serviceType=rest&action=rhm_data_query">' + _q('Explore This', 'Explorar Este') + '</a></div>');
            }
        }

        function makeExPrey(subject) {
            if (subject.preyInstanceCount < 1)
                return false;

            var exArea = $('#ex-area');

            for (var preyKey in subject.preyList) {
                var prey = subject.preyList[preyKey];
                var preyID = 'prey-' + nameSafe(prey.scientificName);

                $(exArea).append('<div id="' + preyID + '" class="ex-prey ex-species gradient" data-sci-name="' + prey.scientificName + '"><div class="ex-label">Prey</div>' + nameTip(prey.scientificName) + '<a class="ex-link" href="/query-database/exploration/?subjectName=' + encodeURI(prey.scientificName) + '&findPrey=on&findPredators=on&serviceType=rest&action=rhm_data_query">Explore This</a></div>');
            }
        }

        Results.prototype.makeExLines = function () {
            jsPlumb.Defaults.Container = 'ex-area';
            jsPlumb.Defaults.PaintStyle = {
                lineWidth: 4,
                strokeStyle: '#990014'
            }

            var subjectID = $('.ex-subject').first().attr('id');
            var subjectBottomEndpoint, subjectTopEndpoint;

            var exPred = $('#ex-area .ex-pred');
            var exPredLength = $(exPred).length;
            var exPredMod = exPredLength % 3;
            switch (exPredMod) {
                case 0:
                    var exPredLineIndices = [exPredLength - 1, exPredLength - 2, exPredLength - 3];
                    break;
                case 1:
                    var exPredLineIndices = [exPredLength - 1, exPredLength - 2, exPredLength - 4];
                    break;
                case 2:
                    var exPredLineIndices = [exPredLength - 1, exPredLength - 2];
                    break;
            }

            $.each(exPred, function (i) {
                try {
                    // Limit lines to the last box in each column.
                    if ($.inArray(i, exPredLineIndices) != -1) {
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
                    }
                } catch (err) {
                    log("Some lines may not have drawn properly because of: " + err + ". Don't worry, it's no big deal.");
                }
            });

            jsPlumb.Defaults.PaintStyle = {
                lineWidth: 4,
                strokeStyle: '#2e9900'
            }

            var exPrey = $('#ex-area .ex-prey');

            $.each(exPrey, function (i) {
                try {
                    // Limit lines to the first three boxes only.
                    if (i < 3) {
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
                    }
                } catch (err) {
                    log("Some lines may not have drawn properly because of: " + err + ". Don't worry, it's no big deal.");
                }
            });
        }


        $('body').on('click', '.toggle', function (e) {
            $(this).parent().toggleClass('closed');
        });

        $('body').on('click', '.toggle-summary-all', function (e) {
            if ($(this).children('input').prop('checked')) {
                $(this).closest('.results-subsection').find('.overflow').show();
                $(this).closest('.results-subsection').find('.top-ten-note').hide();
            } else {
                $(this).closest('.results-subsection').find('.overflow').hide();
                $(this).closest('.results-subsection').find('.top-ten-note').show();
            }
        });

        $('body').on('click', '.toggle-stats', function (e) {
            if ($(this).children('input').prop('checked')) {
                $(this).closest('.instance-details').find('.species-list').addClass('expanded');
            } else {
                $(this).closest('.instance-details').find('.species-list').removeClass('expanded');
            }
        });

        $('body').on('click', '.toggle-references', function (e) {
            if ($(this).children('input').prop('checked')) {
                $(this).closest('.instance-details').find('.reference').show();
            } else {
                $(this).closest('.instance-details').find('.reference').hide();
            }
        });

        $('body').on('click', '.reference-link', function (e) {
            e.preventDefault();

            var refId = $(this).attr('href');
            refId = refId.replace('#', '');
            var citation = $('#' + refId).html();

            $.fancybox({
                type: 'inline',
                href: '#' + refId,
                onClosed: function () {
                    $('.fancybox-inline-tmp').html(citation).attr('id', refId).attr('style', '').removeClass('fancybox-inline-tmp');
                }
            });
        });

        $('body').on('click', '.name-tip-link', function (e) {
            e.preventDefault();
            e.stopPropagation();

            // Remove any existing name tip boxes.
            $('.name-tip-box').remove();

            var scientificName = $(this).html();
            var wrapper = $(this).parent('.name-tip-wrapper');
            $(wrapper).append('<div class="name-tip-box"><div class="container"><ul></ul></div><div class="bridge"></div></div>');
            var linkList = $(wrapper).find('ul');
            if (!modeIs('exploration')) {
                $(linkList).append('<li><a href="/query-database/exploration/?subjectName=' + encodeURI(scientificName) + '&findPrey=on&findPredators=on&serviceType=rest&action=rhm_data_query">' + _q('View in Explorer Mode', 'Ver en Modo de Exploración') + '</a></li>');
            }
            if (modeIs('exploration')) {
                $(linkList).append('<li><a href="/query-database/taxonomic/?subjectName=' + encodeURI(scientificName) + '&findPrey=on&findPredators=on&serviceType=rest&action=rhm_data_query">' + _q('Taxonomic Query', 'Consulta Taxonómica') + '</a></li>');
            }

            var postData = {
                url: 'http://gomexsi.tamucc.edu/gomexsi/requestHandler/RequestHandler.php',
                action: 'rhm_data_query',
                deepLinks: scientificName,
                serviceType: 'rest'
            };

            // POST to the WordPress Ajax system.
            $.post(
                // URL to the WordPress Ajax system.
                '/wp-admin/admin-ajax.php',

                // The object containing the POST data.
                postData,

                // Success callback function.
                function (data, textStatus, jqXHR) {
                    var externalUrls = data['URL'];

                    if (typeof externalUrls !== 'undefined') {
                        var externalUrlToListItem = function (externalUrl) {
                            var mapping = {
                                'eol.org': _q('Encyclopedia of Life', 'Enciclopedia de la Vida')
                                , 'fishbase.org': 'Fishbase'
                                , 'sealifebase.org' : 'SeaLifeBase'
                                , 'gulfbase.org' : 'GulfBase'
                                , 'gbif.org' : 'GBIF'
                                , 'inaturalist.org' : 'iNaturalist'
                                , 'itis.gov' : 'ITIS'
                                , 'ncbi.nlm.nih.gov' : 'NCBI'
                                , 'marine.csiro.au' : 'IRMNG'
                                , 'opentreeoflife.org' : 'Open Tree of Life'
                                , 'marinespecies.org' : 'WoRMS'};

                            var keySelected = Object.keys(mapping).filter(function(key) { return externalUrl.indexOf(key) !== -1; });
                            var externalSource = keySelected.length === 0 ? 'link' : mapping[keySelected];

                            return '<li><a href="' + externalUrl + '" class="external" target="_blank">' + externalSource + '</a></li>';
                        };
                        $(linkList).append(externalUrls.map(externalUrlToListItem));
                    }
                },

                // Expect JSON data.
                'json'

                // Failure callback function.
            ).fail(function (jqXHR, textStatus, errorThrown) {

                });
        });

        $('body').click(function (e) {
            // If a name tip box is open, then clicking anywhere else will remove it.
            $('.name-tip-box').remove();
        });

        $('body').on('click', '.map-link', function (e) {
            e.preventDefault();

            var lat = $(this).attr('data-lat');
            var lon = $(this).attr('data-lon');
            var latLon = new google.maps.LatLng(lat, lon);
            var polygonText = $(this).attr('data-footprintWKT');
            if (polygonText != 'null' && polygonText != '') {
                var wkt = new Wkt.Wkt();
                wkt.read(polygonText);
                var footprintWKT = wkt.toObject({
                    strokeColor: '#FFFF00',
                    strokeOpacity: 0.5,
                    strokeWeight: 1,
                    fillColor: '#FFFF00',
                    fillOpacity: 0.1
                });
            }

            if (lat && lon) {
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

                if (polygonText != 'null' && polygonText != '') {
                    footprintWKT.setMap(map);
                }

                $.fancybox({
                    'type': 'inline',
                    'href': '#map-canvas',
                    'onClosed': function () {
                        $('div#hideaway .fancybox-inline-tmp').attr('id', 'map-canvas').attr('style', '').removeClass('fancybox-inline-tmp');
                    }
                });
            }
        });


        /* ==============================
         Query Form
         ============================*/

        // Conditional switch listener.
        $('.switch').click(function (e) {
            toggleSwitch($(this));
        });

        // Run this on the initial page load in case "switches" are set by the URL.
        $('.switch').each(function (i, e) {
            toggleSwitch(e);
        });

        function toggleSwitch(theSwitch) {
            var checked = $(theSwitch).prop('checked');
            var switchName = $(theSwitch).attr('data-switch');
            var conditional = $('.conditional[data-switch="' + switchName + '"]');
            if (checked) {
                $(conditional).show();
            } else {
                $(conditional).hide();
                $(conditional).find('input[type="checkbox"], input[type="radio"]').prop('checked', false);
                $(conditional).find('input[type="text"], textarea').val('');
                $(conditional).find('select').prop('selectedIndex', 0);
            }
        }

        // Master checkbox listener.
        $('.master-checkbox').click(function (e) {
            var section = $($(this)).closest('.form-section');
            var checkboxes = checkboxCheck(section);

            if (checkboxes.unchecked > 0) {
                $(this).prop('indeterminate', false);
                $(this).prop('checked', true);
            } else {
                $(this).prop('checked', false);
            }

            var checked = $(this).prop('checked');

            var checkboxInputs = $(section).find('input[type="checkbox"]').not('.master-checkbox');
            $(checkboxInputs).prop('checked', checked).each(function (i) {
                toggleSwitch($(checkboxInputs)[i]);
            });
        });

        $('input[type="checkbox"]').not('.master-checkbox').click(function (e) {
            setMasterCheckbox($(this).closest('.form-section'));
        });

        setMasterCheckbox($('#form-section-find'));

        function setMasterCheckbox(section) {
            var checkboxes = checkboxCheck(section);

            if (checkboxes.checked && checkboxes.unchecked) {
                $(section).find('.master-checkbox').prop('indeterminate', true);
            } else if (checkboxes.checked && checkboxes.unchecked == 0) {
                $(section).find('.master-checkbox').prop('indeterminate', false);
                $(section).find('.master-checkbox').prop('checked', true);
            } else if (checkboxes.checked == 0 && checkboxes.unchecked) {
                $(section).find('.master-checkbox').prop('indeterminate', false);
                $(section).find('.master-checkbox').prop('checked', false);
            }
        }

        function checkboxCheck(section) {
            var checked = 0;
            var unchecked = 0;

            $(section).find('input[type="checkbox"]').not('.master-checkbox').each(function (i) {
                if ($(this).prop('checked')) {
                    checked++;
                } else {
                    unchecked++;
                }
            });

            var checkboxes = { 'checked': checked, 'unchecked': unchecked };

            return checkboxes;
        }

        // Query Map
        if (modeIs('spatial')) {
            // This will be used later.
            var markersInGulf = Array();

            var qMapLatLon = new google.maps.LatLng(25, -90);

            var qMapOptions = {
                center: qMapLatLon,
                zoom: 5,
                mapTypeId: google.maps.MapTypeId.TERRAIN
            };

            var qMap = new google.maps.Map(document.getElementById('query-map'), qMapOptions);

            var qShape;

            var qShapeBounds = new google.maps.LatLngBounds(
                new google.maps.LatLng(
                    parseFloat($('form#data-query input[name="boundSouth"]').val()),
                    parseFloat($('form#data-query input[name="boundWest"]').val())
                ),
                new google.maps.LatLng(
                    parseFloat($('form#data-query input[name="boundNorth"]').val()),
                    parseFloat($('form#data-query input[name="boundEast"]').val())
                )
            );

            qShape = new google.maps.Rectangle({
                bounds: qShapeBounds,
                map: qMap,
                editable: true,
                strokeColor: "#ffff00",
                strokeOpacity: 1,
                strokeWeight: 1,
                fillColor: "#ffff00",
                fillOpacity: 0.1,
            });

            function updateBounds(bounds) {
                var boundN = bounds.getNorthEast().lat();
                var boundE = bounds.getNorthEast().lng();
                var boundS = bounds.getSouthWest().lat();
                var boundW = bounds.getSouthWest().lng();

                $('form#data-query input[name="boundNorth"]').val(boundN);
                $('form#data-query input[name="boundEast"]').val(boundE);
                $('form#data-query input[name="boundSouth"]').val(boundS);
                $('form#data-query input[name="boundWest"]').val(boundW);
            }

            updateBounds(qShape.bounds);

            google.maps.event.addListener(qShape, 'bounds_changed', function () {
                updateBounds(qShape.bounds);
            });

            // POST to the WordPress Ajax system.
            $.post(
                '/wp-admin/admin-ajax.php',
                'action=rhm_data_locations',
                function (data, textStatus, jqXHR) {
                    log(data);
                    $.each(data, function (i, point) {
                        var latLon = new google.maps.LatLng(point[0], point[1]);

                        var marker = new google.maps.Marker({
                            position: latLon,
                            icon: '/wp-content/themes/gomexsi-wp/img/map_marker_dot_red.png',
                            map: qMap
                        });
                        markersInGulf.push(marker);
                    });
                },
                'json'
            ).fail(function (jqXHR, textStatus, errorThrown) {
                    log(errorThrown);
                });
        }

        // Data query form submit action.
        $('form#data-query').submit(function (e) {

            // Prevent actual form submission.  We'll do this with AJAX.
            e.preventDefault();

            // The query object that we'll submit via POST.
            log($(this));
            var queryString = $(this).serialize();

            // Update the page URL.
            var pathArray = window.location.pathname.split('?');
            var newPath = pathArray[0] + '?' + queryString;
            log(newPath);
            window.history.pushState({}, '', newPath);

            // Make sure there is a valid query.
            var validInteraction = false;
            var validSubject = false;
            var queryError = '';

            // Must have a name in the subjectName field.
            if ($(subjectNameInput).val()) {
                validSubject = true;
            } else {
                queryError += '<p><img src="/wp-content/themes/gomexsi-wp/img/error.png" alt="Error" style="position: relative; top: 2px" /> Please enter a name or taxonomy in the <em><strong>Name</strong></em> section.</p>';
            }

            // Must have at least one interaction type selected.
            var queryTypes = ['findPrey', 'findPredators', 'findParasites', 'findMutualists', 'findCommonsals', 'findAmensals', 'findPrimaryHosts', 'findSecondaryHosts'];
            for (var i = 0; i < queryTypes.length; i++) {
                if (queryString.indexOf(queryTypes[i]) != -1) {
                    validInteraction = true;
                }
            }

            if (!validInteraction) {
                queryError += '<p><img src="/wp-content/themes/gomexsi-wp/img/error.png" alt="Error" style="position: relative; top: 2px" /> Please select at least one type of interaction in the <em><strong>Find</strong></em> section.</p>';
            }

            // If either case is not satisfied, show the error message and return false.
            if (!validInteraction || !validSubject) {
                $.fancybox({
                    padding: 20,
                    content: queryError,
                    centerOnScroll: true
                });
                return false;
            }

            // Clear the status container.
            $('#status').removeClass('success failure');
            $('#status').addClass('loading');
            $('#status').html(_q('Loading...', 'Descargando...'));

            log('Query String:');
            log(queryString);

            // POST to the WordPress Ajax system.
            $.post(

                // URL to the WordPress Ajax system.
                '/wp-admin/admin-ajax.php',

                // The object containing the POST data.
                queryString,

                // Success callback function.
                function (data, textStatus, jqXHR) {
                    // The function to process the results data.
                    var r = new Results(data);

                    r.queryString = queryString;

                    r.processResults();

                    if (modeIs('taxonomic') || modeIs('spatial')) {
                        r.makeSubjects();
                        r.makeResultsHeader();
                        //r.mapListner();
                    } else if (modeIs('exploration')) {
                        r.clearExArea();
                        r.makeExArea();
                        r.makeExLines();
                    }

                    // Show status on page.
                    $('#status').removeClass('loading failure');
                    $('#status').addClass('success');
                    $('#status').html(_q('Query complete.', 'Consulta finalizada.'));

                    // Build the raw data download link.
                    $('#query-results-download').attr('href', 'http://gomexsi.tamucc.edu/gomexsi/gomexsi-wp/data-query-raw.php?' + r.queryString);

                    // Animate scrolling to the results area.
                    if (modeIs('spatial') || modeIs('taxonomic')) {
                        $(document.body).animate({ 'scrollTop': $('#query-results-header').offset().top - 50 }, 1000);
                    } else if (modeIs('exploration')) {
                        $(document.body).animate({ 'scrollTop': $('#ex-area').offset().top - 50 }, 1000);
                    }
                }

                // Failure callback function.
            ).fail(function (jqXHR, textStatus, errorThrown) {

                    // Show status on page.
                    $('#status').removeClass('loading success');
                    $('#status').addClass('failure');
                    $('#status').html('Error: ' + textStatus);

                    // Clear results area.
                    $('#query-results').html('').hide();

                    // Clear the raw data download link.
                    $('#query-results-download').attr('href', '#');
                });
        });

        if ($(subjectNameInput).val()) {
            $('form#data-query').trigger('submit');
        } else {
            $(subjectNameInput).focus();
        }

    }

});
