<?php

include 'RequestParser.php';

$request = $_POST;

$handler = new RequestHandler();
$handler->requestHandlerDriver($request);


class RequestHandler
{

	public function __construct()
    {

    }
    public function requestHandlerDriver($urlPost)
    {
    	$this->parsePOST($urlPost); #Parse Post
    	$this->getTrophicService();
    }
    public function parsePOST($urlPost)
    {
    	$parser = new RequestParser();
    	return $parser->parse($urlPost);
    }
    public function getTrophicService()
    {
    	$serviceFactory = new TrophicServiceFactory();
    	$trophicService = $serviceFactory->createServiceOfType('mock');
    	return $trophicService;
    }
    public function creatJSONResponse()
    {

    }
}

?>