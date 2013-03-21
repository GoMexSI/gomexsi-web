<?php

require_once 'requestHandler/SearchRequestHandler.php';

class SearchRequestHandlerMockTest extends PHPUnit_Framework_TestCase
{
	private $handler;

	public function setUp()
    {
    	$this->handler = new SearchRequestHandler();
    }

	public function testGetTrophicServiceMockFindPreyForPredator()
	{
		$trophicResultString = array();
		$expectedPreyNames = array("Synalpheus latastei", "Lutjanus jocu");

    	$mockURL = array("serviceType" => "mock", "predName" => "Scomberomorus cavalla");
    	$this->handler->parsePOST($mockURL);

		$trophicService = $this->handler->getTrophicService();
		$trophicResultString = $trophicService->findPreyForPredator("Scomberomorus cavalla");
		
		$this->assertEquals(count($expectedPreyNames), count($trophicResultString));

		$iterator = 0;
		foreach ($trophicResultString as $value) {
			$this->assertEquals($value, $expectedPreyNames[$iterator]);
			$iterator++;
		}
	}

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

	public function testGetTrophicServiceMockFindPredatorForPrey()
	{
		$trophicResultString = array();
		$expectedPredNames =array("Ariopsis felis", "Scomberomorus cavalla");

    	$mockURL = array("serviceType" => "mock", "preyName" => "Mugil cephalus");
    	$this->handler->parsePOST($mockURL);

		$trophicService = $this->handler->getTrophicService();
		$trophicResultString = $trophicService->findPredatorForPrey("Mugil cephalus");
		
		$this->assertEquals(count($expectedPredNames), count($trophicResultString));

		$iterator = 0;
		foreach ($trophicResultString as $value) {
			$this->assertEquals($value, $expectedPredNames[$iterator]);
			$iterator++;
		}
	}

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