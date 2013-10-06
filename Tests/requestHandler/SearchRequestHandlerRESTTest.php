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
		$expected = '[{"scientificName":"Zalieutes mcgintyi","preyInstances":[{"prey":["Foraminifera","Goniadella","Goniada maculata","Teleostei","Crustacea","Animalia","Rhachotropis","Paraonidae","Phyllodoce arenae","Opheliidae","Ophiodromus","Spionidae","Amphipoda","Nematoda","Lumbrineridae","Onuphidae","Anchialina typica","Nemertea","Bathymedon","Sediment","Xanthoidea"]}]}]';
		$actual = $this->handler->requestHandlerDriver($this->postRequest);

		$this->assertSimilarResponse($actual, $expected, "preyInstances", "prey");
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
		$actualPreyNames = $trophicService->findPreyForPredator("Zalieutes mcgintyi");

		$this->assertActualContainsExpected($actualPreyNames, $expectedPreyNames);

	}
	public function testGetTrophicServiceRESTFindPredatorForPrey()
	{
		$expectedPredNames = array("Zalieutes mcgintyi", "Syacium gunteri", "Pomatoschistus microps", "Zoarces viviparus", "Pleuronectes platessa", "Paralichthyes albigutta", "Retusa obtusa", "Symphurus civitatus");

		$this->postRequest["subjectName"] = "Foraminifera";
		unset($this->postRequest["findPrey"]);
		$this->postRequest["findPredators"] = "on";
		$this->handler->parsePOST($this->postRequest);

		$trophicService = $this->handler->getTrophicService();
		$actualPredNames = $trophicService->findPredatorForPrey("Foraminifera");

		$this->assertActualContainsExpected($actualPredNames, $expectedPredNames);
	}

	private function assertActualContainsExpected($actualNames, $expectedNames) {
		foreach ($expectedNames as $name) {
			$this->assertTrue(in_array($name, $actualNames), "expected name [" . $name . "] but wasn't found.");
		}
	}

	public function testCreateJSONResponseRESRFindPreyForPredator()
	{
		$this->postRequest["findPrey"] = "on";
		unset($this->postRequest["findPredators"]);

		$expected = '[{"scientificName":"Zalieutes mcgintyi","preyInstances":[{"prey":["Foraminifera","Goniadella","Goniada maculata","Teleostei","Crustacea","Animalia","Rhachotropis","Paraonidae","Phyllodoce arenae","Opheliidae","Ophiodromus","Spionidae","Amphipoda","Nematoda","Lumbrineridae","Onuphidae","Anchialina typica","Nemertea","Bathymedon","Sediment","Xanthoidea"]}]}]';
		
		$actual = $this->handler->requestHandlerDriver($this->postRequest);
		$this->assertSimilarResponse($actual, $expected, "preyInstances", "prey");
	}

	public function testCreateJSONResponseRESTFindPredatorForPrey()
	{
		$this->postRequest["subjectName"] = "Foraminifera";
		unset($this->postRequest["findPrey"]);
		$this->postRequest["findPredators"] = "on";

		$expected = '[{"scientificName":"Foraminifera","predInstances":[{"pred":["Zalieutes mcgintyi","Syacium gunteri","Pomatoschistus microps","Zoarces viviparus","Symphurus plagiusa","Prionotus roseus","Stenotomus caprinus","Syacium papillosum","Monolene sessilicauda","Fundulus similis","Trichopsetta ventralis","Opisthonema oglinum","Coelorinchus caribbaeus","Bembrops anatirostris","Bellator militaris","Pomatoschistus minutus","Leiostomus xanthurus","Crangon crangon","Platichthys flesus","Pleuronectes platessa","Decapterus punctatus","Paralichthyes albigutta","Retusa obtusa","Symphurus civitatus"]}]}]';
		
		$actual = $this->handler->requestHandlerDriver($this->postRequest);

		$this->assertSimilarResponse($actual, $expected, "predInstances", "pred");
	}

	private function assertSimilarResponse($actual, $expected, $instanceNames, $instanceName) {
		$expectedResponse = json_decode($expected);
		$actualResponse = json_decode($actual);

		$this->assertEquals($expectedResponse[0]->{'scientificName'}, $actualResponse[0]->{'scientificName'});

		$actualNames = $actualResponse[0]->{$instanceNames}[0]->{$instanceName};
		$expectedNames = $expectedResponse[0]->{$instanceNames}[0]->{$instanceName};

		foreach($expectedNames as $expectedName) {
			$this->assertTrue(in_array($expectedName, $actualNames), "missing expected name " . $expectedName);
		}
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

		$this->observationPostRequestPred["subjectName"] = "Scomberomorus cavalla";
		$actualResponse = $this->handler->requestHandlerDriver($this->observationPostRequestPred);

		$containsValue = (strpos($actualResponse, 'preyInstances') !== FALSE) ? true : false;
		$this->assertTrue($containsValue, 'preyInstances' . ' is missing from the observed list (from the REST service), for the species Scomberomorus cavalla');
		
	}

	
	public function testGetTrophicServiceRESTFindCloseTaxonNameMatches()
	{
		$this->postRequest["suggestion"] = "Admonti";
		$this->handler->parsePOST($this->postRequest);

		$trophicService = $this->handler->getTrophicService();
		$actualMatches = $trophicService->findCloseTaxonNameMatches("Admonti");
		
		$this->assertTrue(in_array("Admontia blanda", $actualMatches));		
	}

	public function testCreateJSONResponseRESTFindCloseTaxonNameMatches()
	{
		$this->postRequest["suggestion"] = "Admonti";

		$jsonObject = json_decode($this->handler->requestHandlerDriver($this->postRequest));
		$actualMatches = $jsonObject[0]->matches;

		$this->assertTrue(in_array("Admontia blanda", $actualMatches));		
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
	public function testRawDataDowloadCSV()
	{
		$post = array("serviceType" => "REST",
			"findPrey"=>"on",
			"subjectName" => "Ariopsis felis",
			"rawData" => "CSV");

		#prey test
		$this->handler->parsePOST($post);
		$this->handler->getTrophicService();
		$testValue = 'target_taxon_name';

		$mime = $this->handler->createMimeResponse();
		$containsValue = (strpos($mime, $testValue) !== false) ? true : false;
		$this->assertEquals($containsValue, true, "missing [" . $testValue . "] tag from CSV data dump");

		#predator test
		unset($post['findPrey']);
		$post['findPredators'] = "on";

		$this->handler->parsePOST($post);
		$this->handler->getTrophicService();
		$testValue = 'source_taxon_name';

		$mime = $this->handler->createMimeResponse();
		$containsValue = (strpos($mime, $testValue) !== false) ? true : false;
		$this->assertEquals($containsValue, true, "missing [" . $testValue . "] tag from CSV data dump");

		#predator and prey test
		$post['findPrey'] = "on";

		$this->handler->parsePOST($post);
		$this->handler->getTrophicService();
		$testValue[0] = 'predatorName';
		$testValue[1] = 'preyName';

		$mime = $this->handler->createMimeResponse();

		for($i = 0; $i < 2; $i++) {
			$containsValue = (strpos($mime, $testValue[$i]) !== false) ? true : false;
			$this->assertEquals($containsValue, true, 'missing ' . $testValue[$i] . ' tag from CSV data dump');
		}
	}
}
?>