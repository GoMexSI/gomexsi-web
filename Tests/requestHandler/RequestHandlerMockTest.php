<?php

require_once 'requestHandler/RequestHandler.php';

class RequestHandlerTest extends PHPUnit_Framework_TestCase
{
	private $handler;

	public function setUp()
    {
    	$this->handler = new RequestHandler();

    }
    //Test for Use case One
	public function testGetTrophicServiceMockFindPreyForPredator()
	{
    	$mockURL = array("serviceType" => "mock", "predName" => "Scomberomorus cavalla");
    	$this->handler->parsePOST($mockURL);

		$trophicService = $this->handler->getTrophicService();
		$preyNames = array("Synalpheus latastei", "Lutjanus jocu");

		$iterator = 0;
		foreach ($trophicService as $value) {
			$this->assertEquals($value, $preyNames[$iterator]);
			$iterator++;
		}
	}
	//Test for Use case One
	public function testCreatJSONResponseMockFindPreyForPredator()
	{
		$mockURL = array("serviceType" => "mock", "predName" => "Scomberomorus cavalla");
    	$this->handler->parsePOST($mockURL);

		$this->handler->getTrophicService();

		$jsonTestString = '[{"scientificName":"Scomberomorus cavalla","subjectInstances":[{"prey":["Synalpheus latastei","Lutjanus jocu"]}]}]';
		// http://jsonlint.com/ will format this for anyone who wants to look at it in a more readable structure 

		$jsonObject = $this->handler->creatJSONResponse();
		$this->assertEquals($jsonTestString, $jsonObject);
	}
	//Test for Use case Two
	public function testGetTrophicServiceMockFindPredatorForPrey()
	{
    	$mockURL = array("serviceType" => "mock", "preyName" => "Mugil cephalus");
    	$this->handler->parsePOST($mockURL);

		$trophicService = $this->handler->getTrophicService();
		$predNames = array("Ariopsis felis", "Scomberomorus cavalla");

		$iterator= 0;
		foreach ($trophicService as $value) {
			$this->assertEquals($value, $predNames[$iterator]);
			$iterator++;
		}
	}
	//Test for Use case Two
	public function testCreatJSONResponseMockFindPredatorForPrey()
	{
		$mockURL = array("serviceType" => "mock", "preyName" => "Mugil cephalus");
    	$this->handler->parsePOST($mockURL);

		$this->handler->getTrophicService();

		$jsonTestString = '[{"scientificName":"Mugil cephalus","subjectInstances":[{"pred":["Ariopsis felis","Scomberomorus cavalla"]}]}]';
		// http://jsonlint.com/ will format this for anyone who wants to look at it in a more readable structure 

		$jsonObject = $this->handler->creatJSONResponse();
		$this->assertEquals($jsonTestString, $jsonObject);
	}

}
?>