<?php

require_once 'requestHandler/SearchRequestHandler.php';

class SearchRequestHandlerRESTTest extends PHPUnit_Framework_TestCase
{
	private $handler;
	private $postRequest;

	public function setUp()
    {
    	$this->handler = new SearchRequestHandler();
    	$this->postRequest = array("serviceType" => "REST",
			   "findPrey"=>"on",
			   "subjectName" => "Zalieutes mcgintyi");
    }

    public function testRequestHandlerDriver()
    {
    	$jsonTestString = '[{"scientificName":"Zalieutes mcgintyi","preyInstances":[{"prey":["Foraminifera","Goniadella","Goniada maculata","Teleostei","Crustacea","Animalia","Rhachotropis","Paraonidae","Phyllodoce arenae","Opheliidae","Ophiodromus","Spionidae","Amphipoda","Nematoda","Lumbrineridae","Onuphidae","Anchialina typica","Nemertea","Bathymedon","Sediment","Xanthoidea"]}]}]';
    	$returnValue = $this->handler->requestHandlerDriver($this->postRequest);

    	$this->assertEquals($jsonTestString, $returnValue);
    }

	public function testGetTrophicServiceRESTFindPreyForPredator()
	{
		$trophicResultString = array();
		$expectedPreyNames = array("Foraminifera", "Goniadella", "Goniada maculata", "Teleostei", "Crustacea", "Animalia", "Rhachotropis", "Paraonidae", "Phyllodoce arenae", "Opheliidae", "Ophiodromus", "Spionidae", "Amphipoda", "Nematoda", "Lumbrineridae", "Onuphidae", "Anchialina typica", "Nemertea", "Bathymedon", "Sediment", "Xanthoidea");

    	$this->postRequest["subjectName"] = "Zalieutes mcgintyi";
    	$this->postRequest["findPrey"] = "on";
		unset($this->postRequest["findPredators"]);
    	$this->handler->parsePOST($this->postRequest);

		$trophicService = $this->handler->getTrophicService();
		$trophicResultString = $trophicService->findPreyForPredator("Zalieutes mcgintyi");
		
		$this->assertEquals(count($expectedPreyNames), count($trophicResultString));
		
		$iterator = 0;
		foreach ($trophicResultString as $value) {
			$this->assertEquals($expectedPreyNames[$iterator], $value);
			$iterator++;
		}
	}

	public function testCreateJSONResponseMockFindPreyForPredator()
	{
    	$this->postRequest["findPrey"] = "on";
		unset($this->postRequest["findPredators"]);
    	
		$jsonTestString = '[{"scientificName":"Zalieutes mcgintyi","preyInstances":[{"prey":["Foraminifera","Goniadella","Goniada maculata","Teleostei","Crustacea","Animalia","Rhachotropis","Paraonidae","Phyllodoce arenae","Opheliidae","Ophiodromus","Spionidae","Amphipoda","Nematoda","Lumbrineridae","Onuphidae","Anchialina typica","Nemertea","Bathymedon","Sediment","Xanthoidea"]}]}]';
		
		$jsonObject = $this->handler->requestHandlerDriver($this->postRequest);
		$this->assertEquals($jsonTestString, $jsonObject);
	}

	public function testGetTrophicServiceRESTFindPredatorForPrey()
	{
		$trophicResultString = array();
		$expectedPredNames = array("Zalieutes mcgintyi", "Syacium gunteri", "Pomatoschistus microps", "Zoarces viviparus", "Symphurus plagiusa", "Prionotus roseus", "Stenotomus caprinus", "Syacium papillosum", "Monolene sessilicauda", "Fundulus similis", "Trichopsetta ventralis", "Coelorinchus caribbaeus", "Bembrops anatirostris", "Bellator militaris", "Pomatoschistus minutus", "Leiostomus xanthurus", "Crangon crangon", "Platichthys flesus", "Pleuronectes platessa", "Paralichthyes albigutta", "Retusa obtusa", "Symphurus civitatus");

    	$this->postRequest["subjectName"] = "Foraminifera";
    	unset($this->postRequest["findPrey"]);
		$this->postRequest["findPredators"] = "on";
    	$this->handler->parsePOST($this->postRequest);

		$trophicService = $this->handler->getTrophicService();
		$trophicResultString = $trophicService->findPredatorForPrey("Foraminifera");
		
		$this->assertEquals(count($expectedPredNames), count($trophicResultString));
		
		$iterator = 0;
		foreach ($trophicResultString as $value) {
			$this->assertEquals($expectedPredNames[$iterator], $value);
			$iterator++;
		}

	}

	public function testCreateJSONResponseMockFindPredatorForPrey()
	{
		$this->postRequest["subjectName"] = "Foraminifera";
    	unset($this->postRequest["findPrey"]);
		$this->postRequest["findPredators"] = "on";
    	
		$jsonTestString = '[{"scientificName":"Foraminifera","predInstances":[{"pred":["Zalieutes mcgintyi","Syacium gunteri","Pomatoschistus microps","Zoarces viviparus","Symphurus plagiusa","Prionotus roseus","Stenotomus caprinus","Syacium papillosum","Monolene sessilicauda","Fundulus similis","Trichopsetta ventralis","Coelorinchus caribbaeus","Bembrops anatirostris","Bellator militaris","Pomatoschistus minutus","Leiostomus xanthurus","Crangon crangon","Platichthys flesus","Pleuronectes platessa","Paralichthyes albigutta","Retusa obtusa","Symphurus civitatus"]}]}]';
		
		$jsonObject = $this->handler->requestHandlerDriver($this->postRequest);
		$this->assertEquals($jsonTestString, $jsonObject);
	}

	public function testGetTrophicServiceRESTFindCloseTaxonNameMatches()
	{
		$trophicResultString = array();
		$expectedPredNames = array("Admontia blanda", "Admontia seria", "Admontia maculisquama", "Admontia grandicornis");

    	$this->postRequest["suggestion"] = "Adm";
    	$this->handler->parsePOST($this->postRequest);

		$trophicService = $this->handler->getTrophicService();
		$trophicResultString = $trophicService->findCloseTaxonNameMatches("Adm");
		
		$this->assertEquals(count($expectedPredNames), count($trophicResultString));
		
		$iterator = 0;
		foreach ($trophicResultString as $value) {
			$this->assertEquals($expectedPredNames[$iterator], $value);
			$iterator++;
		}
	}

	public function testcreateJSONResponseMockFindCloseTaxonNameMatches()
	{
		$this->postRequest["suggestion"] = "Adm";
    	
		$jsonTestString = '[{"fuzzyName":"Adm","matches":["Admontia blanda","Admontia seria","Admontia maculisquama","Admontia grandicornis"]}]';
		
		$jsonObject = $this->handler->requestHandlerDriver($this->postRequest);
		$this->assertEquals($jsonTestString, $jsonObject);
	}

}
?>