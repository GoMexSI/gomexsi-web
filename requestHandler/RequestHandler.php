<?php

require_once 'SearchRequestHandler.php';
require_once '../service/TrophicServiceFactory.php';

$request = $_POST;

$handler = new SearchRequestHandler();
$jsonString = $handler->requestHandlerDriver($request);
echo $jsonString;

?>