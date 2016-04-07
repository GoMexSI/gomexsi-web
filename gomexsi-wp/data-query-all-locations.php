<?php

// This file will do a cURL POST to RequestHandler.php based on a query string that is passed to it.
// The resulting data (in CSV format) will be served up with PHP headers that cause it to be
// downloaded instead of shown in the browser.  The filename will also be created based on the
// search terms.

// Set header.
header('Content-Type: application/json; charset=utf-8');

// Target URL, which will give the results.
$url = 'http://api.globalbioticinteractions.org/locations?accordingTo=gomexsi';

// Initialize cURL request.
$curl = curl_init($url);

// Set encoding.
curl_setopt($curl, CURLOPT_ENCODING ,"gzip");

// Fail if the other server gives an error.
curl_setopt($curl, CURLOPT_FAILONERROR, true);

// Return result as string instead of parsing.
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

// Execute request and store result.
$result = curl_exec($curl);

if(curl_error($curl)){
	$result = curl_error($curl);
}

// Close.
curl_close($curl);

// Output the returned data. 
$result_object = json_decode($result);

foreach($result_object->data as &$point){
	unset($point[2]);
}

echo json_encode($result_object->data);

// Must die here or else WordPress' Ajax system will die('0') afterwards,
// resulting in a '0' stuck on the end of our returned data.
die();