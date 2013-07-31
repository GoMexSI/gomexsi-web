<?php

// This file will do a cURL POST to RequestHandler.php based on a query string that is passed to it.
// The resulting data (in CSV format) will be served up with PHP headers that cause it to be
// downloaded instead of shown in the browser.  The filename will also be created based on the
// search terms.

// Target URL, which will give the results.
$url = 'http://gomexsi.tamucc.edu/gomexsi/requestHandler/RequestHandler.php';

// Build the query for POST.
$query = array();
foreach($_GET as $key => $value){
	// Ignore the 'action' parameter. It was used by WordPress already and is not needed downstream.
	if($key != 'action'){
		$query[$key] = $value;
	}
}

// Turn on raw data output from RequestHandler.php.
$query['rawData'] = 'on';

// POST array to string.
$query_string = http_build_query($query);

// Initialize cURL request.
$curl = curl_init($url);

// Setup POST.
curl_setopt($curl, CURLOPT_POST, count($query));
curl_setopt($curl, CURLOPT_POSTFIELDS, $query_string);

// Fail if the other server gives an error.
curl_setopt($curl, CURLOPT_FAILONERROR, true);

// Return result as string instead of parsing.
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

// Execute request and store result.
$result = curl_exec ($curl);

if(curl_error($curl)){
	$result = curl_error($curl);
}

// Close.
curl_close ($curl);

// Set filename based on subject name and date and time of request.
$filename = str_replace('+', '_', $query['subjectName']) . date('Y_m_d_G-i-s') . '.csv';

// Set headers to force download.
header('Content-Type: application/csv');
header('Content-Disposition: attachment; filename=' . $filename);
header('Expires: 0');
header("Content-Transfer-Encoding: binary");
header('Pragma: no-cache');

// Output the returned data. 
echo $result;

// Must die here or else WordPress' Ajax system will die('0') afterwards,
// resulting in a '0' stuck on the end of our returned data.
die();
