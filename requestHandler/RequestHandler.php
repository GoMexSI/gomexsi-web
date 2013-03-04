<?php

require_once 'RequestParser.php';
require_once 'RequestJSONResponse.php';
require_once 'service/TrophicServiceFactory.php'; 

$request = $_POST;

$handler = new RequestHandler();
$handler->requestHandlerDriver($request);


class RequestHandler
{
    private $serviceType; # 'mock', 'REST'

	public function __construct()
    {

    }
    public function requestHandlerDriver($urlPost)
    {
    	$this->parsePOST($urlPost); #Parse Post
    	$this->getTrophicService();
        $this->creatJSONResponse();
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
        $jsonConverter = new RequestJSONResponse();
        $jsonString = $jsonConverter->convertToJSONObject("");
        return $jsonString;
        #send post to Reeds code here
    }
}

?>