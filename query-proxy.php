<?php

// Decode query URL.
$url = urldecode($_POST['queryUrl']);

// POST array to string.
$post_string = http_build_query($_POST);

// Initial cURL request.
$curl = curl_init($url);

// Setup POST.
curl_setopt($curl,CURLOPT_POST, count($_POST));
curl_setopt($curl,CURLOPT_POSTFIELDS, $post_string);

// Return result as string instead of parsing.
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

// Execute request and store result.
$result = curl_exec ($curl);

// Close.
curl_close ($curl);

// Output the returned 
print $result;

?>