<?php

require_once 'requestHandler/SearchRequestHandler.php';

class SearchRequestHandlerRESTTest extends PHPUnit_Framework_TestCase
{
	private $handler;
	private $postRequest;
	private $observationPostRequest;

	public function setUp()
	{
		$this->handler = new SearchRequestHandler();
		$this->postRequest = array("serviceType" => "REST",
			"findPrey"=>"on",
			"subjectName" => "Zalieutes mcgintyi", "listStyle" => true);
		$this->observationPostRequest = array("serviceType" => "REST",
			"findPrey"=>"on",
			"subjectName" => "Ariopsis felis");
    	$this->observationPostRequestPred = array("serviceType" => "REST",
		   "findPredators"=>"on",
		   "subjectName" => "Callinectes sapidus");
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
	public function testGetTrophicServiceRESTFindPredatorForPrey()
	{
		$trophicResultString = array();
		$expectedPredNames = array("Zalieutes mcgintyi", "Syacium gunteri", "Pomatoschistus microps", "Zoarces viviparus", "Pleuronectes platessa", "Paralichthyes albigutta", "Retusa obtusa", "Symphurus civitatus");

		$this->postRequest["subjectName"] = "Foraminifera";
		unset($this->postRequest["findPrey"]);
		$this->postRequest["findPredators"] = "on";
		$this->handler->parsePOST($this->postRequest);

		$trophicService = $this->handler->getTrophicService();
		$trophicResultString = $trophicService->findPredatorForPrey("Foraminifera");
				
		$containsValue = (count($trophicResultString) >= count($expectedPredNames)  ) ? true : false;
		$this->assertTrue($containsValue, 'missing values from predator Foraminifera');
	}

	public function testCreateJSONResponseRESRFindPreyForPredator()
	{
		$this->postRequest["findPrey"] = "on";
		unset($this->postRequest["findPredators"]);

		$jsonTestString = '[{"scientificName":"Zalieutes mcgintyi","preyInstances":[{"prey":["Foraminifera","Goniadella","Goniada maculata","Teleostei","Crustacea","Animalia","Rhachotropis","Paraonidae","Phyllodoce arenae","Opheliidae","Ophiodromus","Spionidae","Amphipoda","Nematoda","Lumbrineridae","Onuphidae","Anchialina typica","Nemertea","Bathymedon","Sediment","Xanthoidea"]}]}]';
		
		$jsonObject = $this->handler->requestHandlerDriver($this->postRequest);
		$this->assertEquals($jsonTestString, $jsonObject);
	}

	public function testCreateJSONResponseRESTFindPredatorForPrey()
	{
		$this->postRequest["subjectName"] = "Foraminifera";
		unset($this->postRequest["findPrey"]);
		$this->postRequest["findPredators"] = "on";

		$jsonTestString = '[{"scientificName":"Foraminifera","predInstances":[{"pred":["Zalieutes mcgintyi","Syacium gunteri","Pomatoschistus microps","Zoarces viviparus","Symphurus plagiusa","Prionotus roseus","Stenotomus caprinus","Syacium papillosum","Monolene sessilicauda","Fundulus similis","Trichopsetta ventralis","Opisthonema oglinum","Coelorinchus caribbaeus","Bembrops anatirostris","Bellator militaris","Pomatoschistus minutus","Leiostomus xanthurus","Crangon crangon","Platichthys flesus","Pleuronectes platessa","Decapterus punctatus","Paralichthyes albigutta","Retusa obtusa","Symphurus civitatus"]}]}]';
		
		$jsonObject = $this->handler->requestHandlerDriver($this->postRequest);
		$this->assertEquals($jsonTestString, $jsonObject);

	}

	public function testCreateJSONResponseRESTFindObservedPrey()
	{
		$actualResponse = $this->handler->requestHandlerDriver($this->observationPostRequest);
		$somePreyValues = array('Actinopterygii', 'Callinectes sapidus', 'Mollusca', 'Portunus', 'Brevoortia patronus', 'Neopanope sayi', 'Ruppia maritima');
		$position = 0;
		$count = 0;
		
		while (($position = strpos($actualResponse, 'Ariopsis felis', $position)) > 0) {
			$count++;
			$position++;
		}

		$this->assertEquals(1, $count, 'expected one match in response to observed prey of Ariopsis felis (subject name only), but found ' . $count);

		foreach ($somePreyValues as $prey) {
			$containsValue = (strpos($actualResponse, $prey) !== FALSE) ? true : false;
			$this->assertTrue($containsValue, $prey . ' is missing from the observed prey list (from the REST service), for the predator Ariopsis felis');
		}
	}

	public function testCreateJSONResponseRESTFindObservedPredator()
	{
		unset($this->observationPostRequestPred["findPrey"]);
		$actualResponse = $this->handler->requestHandlerDriver($this->observationPostRequestPred);
		$somePredValues = array('Micropogonias undulatus', 'Sciaenops ocellatus', 'Sciaenops ocellatus', 'Ariopsis felis', 'Menticirrhus littoralis');
		
		foreach ($somePredValues as $pred) {
			$containsValue = (strpos($actualResponse, $pred) !== FALSE) ? true : false;
			$this->assertTrue($containsValue, $pred . ' is missing from the observed pred list (from the REST service), for the predator Callinectes sapidus');
		}
	}
	
	public function testCreateJSONResponseRESTFindObservedPredatorWithLocation()
	{
		$locationPost = array("serviceType" => "REST",
		   "findPrey"=>"on",
		   "subjectName" => "Ariopsis felis",
		   "boundNorth" => 29.3,
		   "boundEast" => 96.1,
		   "boundSouth" => 26.3,
		   "boundWest" => -97.0);


		$actualResponse = $this->handler->requestHandlerDriver($locationPost);
		$somePredValues = array('Micropogonias undulatus', 'Parasite', 'Actinopterygii', 'Pectinariidae', 'Palaemonetes vulgaris');
		
		foreach ($somePredValues as $pred) {
			$containsValue = (strpos($actualResponse, $pred) !== FALSE) ? true : false;
			$this->assertTrue($containsValue, $pred . ' is missing from the observed pred list (from the REST service), for the predator Callinectes sapidus');
		}
	}

	public function testSearchObservedPredatorAndPreyREST()
	{
		$this->observationPostRequestPred["findPredators"] = "on";
		$this->observationPostRequestPred["findPrey"] = "on";

		$actualResponse = $this->handler->requestHandlerDriver($this->observationPostRequestPred);
		$requiredValues = array('preyInstances', 'predInstances');
		
		foreach ($requiredValues as $value) {
			$containsValue = (strpos($actualResponse, $value) !== FALSE) ? true : false;
			$this->assertTrue($containsValue, $value . ' is missing from the observed list (from the REST service), for the species Callinectes sapidus');
		}
	}

	
	public function testGetTrophicServiceRESTFindCloseTaxonNameMatches()
	{
		$trophicResultString = array();
		$expectedPredNames = array("Admontia blanda", "Admontia seria", "Admontia maculisquama", "Admontia grandicornis");

		$this->postRequest["suggestion"] = "Adm";
		$this->handler->parsePOST($this->postRequest);

		$trophicService = $this->handler->getTrophicService();
		$trophicResultString = $trophicService->findCloseTaxonNameMatches("Adm");
		
		$this->assertEquals(0, count(array_diff($expectedPredNames, $trophicResultString)));
	}

	public function testCreateJSONResponseRESTFindCloseTaxonNameMatches()
	{
		$this->postRequest["suggestion"] = "Adm";

		$expected = json_decode('[{"fuzzyName":"Adm","matches":["Admontia blanda","Admontia seria","Admontia maculisquama","Admontia grandicornis"]}]');
		
		$expectedMatches = $expected[0]->matches;
		
		$jsonObject = json_decode($this->handler->requestHandlerDriver($this->postRequest));
		$actualMatches = $jsonObject[0]->matches;

		$this->assertEquals(0, count(array_diff($expectedMatches, $actualMatches)));
	}

	public function testFindExternalTaxonURLREST()
	{
		$post = array("serviceType" => "rest", "deepLinks" => "Homo sapiens");
		$this->handler->parsePOST($post);

		$this->handler->getTrophicService();

		$jsonTestString = '{"scientificName":"Homo sapiens","URL":"http://eol.org/pages/327955"}';

		$jsonObject = $this->handler->createMimeResponse();
		$this->assertEquals($jsonTestString, $jsonObject);
	}
/*	public function testRawDataDowloadCSV()
	{
		$post = array("serviceType" => "REST",
			//"findPrey"=>"on",
			"findPred"=>"on",
			"subjectName" => "Ariopsis felis",
			"rawData" => "CSV");
		$this->handler->parsePOST($post);

		$this->handler->getTrophicService();

		$jsonTestString = '{"scientificName":"Homo sapiens","URL":"http://eol.org/pages/327955"}';

		$mime = $this->handler->createMimeResponse();
		$this->assertEquals($jsonTestString, $mime);
	}*/
}
?>