<?php

require_once 'requestHandler/RequestHandler.php';

class RequestHandlerTest extends PHPUnit_Framework_TestCase
{
	private $handler;
	private $serviceFactory;

	public function setUp()
    {
    	$this->handler = new RequestHandler();
    	//$this->serviceFactory = new TrophicServiceFactory();
    }
	public function testParsePOST()
	{
		$this->assertEquals("", $this->handler->parsePOST(""));
	}
	public function testGetTrophicService()
	{
		$trophicService = $this->handler->getTrophicService();
		$preyNames = array('Synalpheus latastei', 'Lutjanus jocu');

		$iterator = 0;
		foreach ($trophicService as $value) {
			$this->assertEquals($trophicService[$iterator], $value);
			$iterator++;
		}
	}
	public function testCreatJSONResponse()
	{
		$jsonTestString = '[{"scientificName": "Scomberomorus cavalla", "subjectInstances": {"prey": [{"scientificName": "Synalpheus latastei"}, {"scientificName": "Lutjanus jocu"}]}}]';
		// http://jsonlint.com/ will format this for anyone who wants to look at it in a more readable structure 

		$jsonObject = $this->handler->creatJSONResponse('');
		$this->assertEquals($jsonTestString, $jsonObject);
	}
}
?>