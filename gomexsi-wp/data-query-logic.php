<?php

// This file is called in functions.php.  It is separated for easy maintenance.

// Target URL, which will give the results.
$url = 'http://gomexsi.tamucc.edu/gomexsi/query-test-return.php';
//$url = 'http://gomexsi.tamucc.edu/gomexsi/requestHandler/RequestHandler.php';

// Build the query for POST.
$query = array();
foreach($_POST as $key => $value){
	// Ignore the 'action' parameter. It was used by WordPress earlier and is not needed downstream.
	if($key != 'action'){
		$query[$key] = $value;
	}
}

// POST array to string.
$query_string = http_build_query($query);

// Initialize cURL request.
$curl = curl_init($url);

// Setup POST.
curl_setopt($curl,CURLOPT_POST, count($query));
curl_setopt($curl,CURLOPT_POSTFIELDS, $query_string);

// Return result as string instead of parsing.
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

// Execute request and store result.
$result = curl_exec ($curl);

// Close.
curl_close ($curl);

// Output the returned data. 
echo $result;

// Must die here or else WordPress' Ajax system will die('0') afterwards,
// resulting in a '0' stuck on the end of our returned data.
die();
