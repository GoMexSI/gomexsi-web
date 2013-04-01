<?php

require_once 'requestHandler/SearchRequestHandler.php';

class SearchRequestHandlerMockTest extends PHPUnit_Framework_TestCase
{
	private $handler;
	private $postRequest;

	public function setUp()
    {
    	$this->handler = new SearchRequestHandler();
    	$this->postRequest = array("serviceType" => "mock",
					   "findPrey"=>"on",
					   "subjectName" => "Scomberomorus cavalla");
    }

	public function testGetTrophicServiceMockFindPreyForPredator()
	{
		$trophicResultString = array();
		$expectedPreyNames = array("Synalpheus latastei", "Lutjanus jocu");

    	$this->handler->parsePOST($this->postRequest);

		$trophicService = $this->handler->getTrophicService();
		$trophicResultString = $trophicService->findPreyForPredator("Scomberomorus cavalla");
		
		$this->assertEquals(count($expectedPreyNames), count($trophicResultString));

		$iterator = 0;
		foreach ($trophicResultString as $value) {
			$this->assertEquals($value, $expectedPreyNames[$iterator]);
			$iterator++;
		}
	}

	public function testCreateJSONResponseMockFindPreyForPredator()
	{
    	$jsonTestString = '[{"scientificName":"Scomberomorus cavalla","preyInstances":[{"prey":["Synalpheus latastei","Lutjanus jocu"]}]}]';
		
		$jsonObject = $this->handler->requestHandlerDriver($this->postRequest);
		$this->assertEquals($jsonTestString, $jsonObject);
	}

	public function testSearchPredatorAndPrey()
	{
		$this->postRequest["subjectName"] = "Ariopsis felis";
		$this->postRequest["findPredators"] = "on";
		$this->postRequest["findPrey"] = "on";
    	
		$jsonTestString = '[{"scientificName":"Ariopsis felis","preyInstances":[{"prey":["Synalpheus latastei","Lutjanus jocu"]}],"predInstances":[{"pred":["Ariopsis felis","Scomberomorus cavalla"]}]}]';
		
		$jsonObject = $this->handler->requestHandlerDriver($this->postRequest);
		$this->assertEquals($jsonTestString, $jsonObject);
	}


	public function testGetTrophicServiceMockFindPredatorForPrey()
	{
		$trophicResultString = array();
		$expectedPredNames =array("Ariopsis felis", "Scomberomorus cavalla");

		$this->postRequest["subjectName"] = "Mugil cephalus";
    	unset($this->postRequest["findPrey"]);
		$this->postRequest["findPredators"] = "on";
    	$this->handler->parsePOST($this->postRequest);

		$trophicService = $this->handler->getTrophicService();
		$trophicResultString = $trophicService->findPredatorForPrey("Mugil cephalus");
		
		$this->assertEquals(count($expectedPredNames), count($trophicResultString));

		$iterator = 0;
		foreach ($trophicResultString as $value) {
			$this->assertEquals($value, $expectedPredNames[$iterator]);
			$iterator++;
		}
	}

	public function testCreateJSONResponseMockFindPredatorForPrey()
	{
		$this->postRequest["subjectName"] = "Mugil cephalus";
		unset($this->postRequest["findPrey"]);
		$this->postRequest["findPredators"] = "on";
    	
		$jsonTestString = '[{"scientificName":"Mugil cephalus","predInstances":[{"pred":["Ariopsis felis","Scomberomorus cavalla"]}]}]';
		
		$jsonObject = $this->handler->requestHandlerDriver($this->postRequest);
		$this->assertEquals($jsonTestString, $jsonObject);
	}

	public function testGetTrophicServiceMockFindCloseTaxonNameMatches()
	{
		$trophicResultString = array();
		$expectedMatchNames =array('Ariopsis felis', 'Scomberomorus cavalla');

    	$this->postRequest["suggestion"] = "Scomb";
    	$this->handler->parsePOST($this->postRequest);

		$trophicService = $this->handler->getTrophicService();
		$trophicResultString = $trophicService->findCloseTaxonNameMatches("Scomb");
		
		$this->assertEquals(count($expectedMatchNames), count($trophicResultString));

		$iterator = 0;
		foreach ($trophicResultString as $value) {
			$this->assertEquals($value, $expectedMatchNames[$iterator]);
			$iterator++;
		}
	}

	public function testCreateJSONResponseMockFindCloseTaxonNameMatches()
	{
		$this->postRequest["suggestion"] = "Scomb";
    	$this->handler->parsePOST($this->postRequest);

		$this->handler->getTrophicService();

		$jsonTestString = '[{"fuzzyName":"Scomb","matches":["Ariopsis felis","Scomberomorus cavalla"]}]';
		
		$jsonObject = $this->handler->createJSONResponse();
		$this->assertEquals($jsonTestString, $jsonObject);
	}

}


?>