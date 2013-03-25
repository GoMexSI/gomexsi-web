<?php

// This file is called in functions.php.  It is separated for easy maintenance.

// Target URL, which will give the results.
$url = $_POST['url'];

// Initialize cURL request.
$curl = curl_init($url);

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

// Output the returned data. 
echo $result;

// Must die here or else WordPress' Ajax system will die('0') afterwards,
// resulting in a '0' stuck on the end of our returned data.
die();
