<?php

require_once 'requestHandler/RequestHandler.php';

class RequestHandlerRESTTest extends PHPUnit_Framework_TestCase
{
	private $handler;

	public function setUp()
    {
    	$this->handler = new RequestHandler();

    }
    //Test for Use case One
	public function testGetTrophicServiceRESTFindPreyForPredator()
	{
		$trophicResultString = array();
		$expectedPreyNames = array("Foraminifera", "Goniadella", "Goniada maculata", "Teleostei", "Crustacea", "Animalia", "Rhachotropis", "Paraonidae", "Phyllodoce arenae", "Opheliidae", "Ophiodromus", "Spionidae", "Amphipoda", "Nematoda", "Lumbrineridae", "Onuphidae", "Anchialina typica", "Nemertea", "Bathymedon", "Sediment", "Xanthoidea");

    	$RESTURL = array("serviceType" => "REST", "predName" => "Zalieutes mcgintyi");
    	$this->handler->parsePOST($RESTURL);

		$trophicService = $this->handler->getTrophicService();
		$trophicResultString = $trophicService->findPreyForPredator("Zalieutes mcgintyi");
		
		$this->assertEquals(count($expectedPreyNames), count($trophicResultString));
		
		$iterator = 0;
		foreach ($trophicResultString as $value) {
			$this->assertEquals($expectedPreyNames[$iterator], $value);
			$iterator++;
		}

	}
	//Test for Use case One
	public function testCreatJSONResponseMockFindPreyForPredator()
	{
		$RESTURL = array("serviceType" => "REST", "predName" => "Zalieutes mcgintyi");
    	$this->handler->parsePOST($RESTURL);

		$this->handler->getTrophicService();

		$jsonTestString = '[{"scientificName":"Zalieutes mcgintyi","subjectInstances":[{"prey":["Foraminifera","Goniadella","Goniada maculata","Teleostei","Crustacea","Animalia","Rhachotropis","Paraonidae","Phyllodoce arenae","Opheliidae","Ophiodromus","Spionidae","Amphipoda","Nematoda","Lumbrineridae","Onuphidae","Anchialina typica","Nemertea","Bathymedon","Sediment","Xanthoidea"]}]}]';
		// http://jsonlint.com/ will format this for anyone who wants to look at it in a more readable structure 

		$jsonObject = $this->handler->creatJSONResponse();
		$this->assertEquals($jsonTestString, $jsonObject);
	}
}
?>