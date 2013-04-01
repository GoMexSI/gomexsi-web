<?php

require_once 'requestHandler/SearchRequestHandler.php';

class SearchRequestHandlerMockTest extends PHPUnit_Framework_TestCase
{
	private $handler;
	private $toParse;

	public function setUp()
    {
    	$this->handler = new SearchRequestHandler();
    	$this->toParse = array("serviceType" => "mock",
					   "findPrey"=>"on",
					   "subjectName" => "Scomberomorus cavalla");
    }

	public function testGetTrophicServiceMockFindPreyForPredator()
	{
		$trophicResultString = array();
		$expectedPreyNames = array("Synalpheus latastei", "Lutjanus jocu");

    	$this->handler->parsePOST($this->toParse);

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
    	$this->handler->parsePOST($this->toParse);

		$this->handler->getTrophicService();

		$jsonTestString = '[{"scientificName":"Scomberomorus cavalla","preyInstances":[{"prey":["Synalpheus latastei","Lutjanus jocu"]}]}]';
		// http://jsonlint.com/ will format this for anyone who wants to look at it in a more readable structure 

		$jsonObject = $this->handler->creatJSONResponse();
		$this->assertEquals($jsonTestString, $jsonObject);
	}

	public function testGetTrophicServiceMockFindPredatorForPrey()
	{
		$trophicResultString = array();
		$expectedPredNames =array("Ariopsis felis", "Scomberomorus cavalla");

		$this->toParse["subjectName"] = "Mugil cephalus";
    	unset($this->toParse["findPrey"]);
		$this->toParse["findPredators"] = "on";
    	$this->handler->parsePOST($this->toParse);

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
		$this->toParse["subjectName"] = "Mugil cephalus";
		unset($this->toParse["findPrey"]);
		$this->toParse["findPredators"] = "on";
    	$this->handler->parsePOST($this->toParse);

		$this->handler->getTrophicService();

		$jsonTestString = '[{"scientificName":"Mugil cephalus","predInstances":[{"pred":["Ariopsis felis","Scomberomorus cavalla"]}]}]';
		// http://jsonlint.com/ will format this for anyone who wants to look at it in a more readable structure 

		$jsonObject = $this->handler->creatJSONResponse();
		$this->assertEquals($jsonTestString, $jsonObject);
	}

	public function testGetTrophicServiceMockFindCloseTaxonNameMatches()
	{
		$trophicResultString = array();
		$expectedMatchNames =array('Ariopsis felis', 'Scomberomorus cavalla');

    	$this->toParse["suggestion"] = "Scomb";
    	$this->handler->parsePOST($this->toParse);

		$trophicService = $this->handler->getTrophicService();
		$trophicResultString = $trophicService->findCloseTaxonNameMatches("Scomb");
		
		$this->assertEquals(count($expectedMatchNames), count($trophicResultString));

		$iterator = 0;
		foreach ($trophicResultString as $value) {
			$this->assertEquals($value, $expectedMatchNames[$iterator]);
			$iterator++;
		}
	}

	public function testCreatJSONResponseMockFindCloseTaxonNameMatches()
	{
		$this->toParse["suggestion"] = "Scomb";
    	$this->handler->parsePOST($this->toParse);

		$this->handler->getTrophicService();

		$jsonTestString = '[{"fuzzyName":"Scomb","matches":["Ariopsis felis","Scomberomorus cavalla"]}]';
		// http://jsonlint.com/ will format this for anyone who wants to look at it in a more readable structure 

		$jsonObject = $this->handler->creatJSONResponse();
		$this->assertEquals($jsonTestString, $jsonObject);
	}

}


?>