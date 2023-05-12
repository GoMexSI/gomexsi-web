 <?php

 require_once 'requestHandler/RequestParser.php';

 class RequestParserTest extends PHPUnit\Framework\TestCase
 {
 	private $requestParse;
 	private $postRequest;
 	private $observationPostRequest;

 	protected function setUp(): void
 	{
 		$this->requestParse = new RequestParser();
 		$this->postRequest = array("serviceType" => "mock",
 			"findPrey"=>"on",
 			"subjectName" => "Scomberomorus cavalla", "listStyle" => true);
 		$this->observationPostRequest = $arrayName = array("serviceType" => "mock",
			   "findPrey"=>"on",
			   "subjectName" => "Ariopsis felis");
 	}
 	public function testDetermineServiceType()
 	{
		#mock
 		$this->requestParse->parse($this->postRequest);
 		$this->assertEquals('mock', $this->requestParse->getServiceType());
 		//observaion
 		$this->requestParse->parse($this->observationPostRequest);
 		$this->assertEquals('mock', $this->requestParse->getServiceType());
		#REST
 		$this->postRequest["serviceType"] = "REST";
 		$this->requestParse->parse($this->postRequest);
 		$this->assertEquals('REST', $this->requestParse->getServiceType());
 		//observation
 		$this->observationPostRequest["serviceType"] = "REST";
 		$this->requestParse->parse($this->observationPostRequest);
 		$this->assertEquals('REST', $this->requestParse->getServiceType());
 	}

 	public function testDetermineSearchTypeMock()
 	{	
    	$this->postRequest["serviceType"] = "mock";
 		$this->requestParse->parse($this->postRequest);
 		$this->assertEquals('exactMatch',  $this->requestParse->getSearchType());
		$this->assertTrue($this->requestParse->shouldIncludePrey());
		$this->assertFalse($this->requestParse->shouldIncludePredators());
		//observation
		$this->observationPostRequest["serviceType"] = "mock";
 		$this->requestParse->parse($this->observationPostRequest);
 		$this->assertEquals('exactMatchObservation',  $this->requestParse->getSearchType());
		$this->assertTrue($this->requestParse->shouldIncludePrey());
		$this->assertFalse($this->requestParse->shouldIncludePredators());

		unset($this->postRequest["findPrey"]);
 		$this->postRequest["findPredators"] = "on";
 		$this->requestParse->parse($this->postRequest);
 		$this->assertEquals('exactMatch',  $this->requestParse->getSearchType());
		$this->assertFalse($this->requestParse->shouldIncludePrey());
		$this->assertTrue($this->requestParse->shouldIncludePredators());
		//observation
		unset($this->observationPostRequest["findPrey"]);
 		$this->observationPostRequest["findPredators"] = "on";
 		$this->requestParse->parse($this->observationPostRequest);
 		$this->assertEquals('exactMatchObservation',  $this->requestParse->getSearchType());
		$this->assertFalse($this->requestParse->shouldIncludePrey());
		$this->assertTrue($this->requestParse->shouldIncludePredators());
	
		$this->postRequest["suggestion"] = "Scomb";
 		$this->requestParse->parse($this->postRequest);
 		$this->assertEquals('fuzzySearch',  $this->requestParse->getSearchType());
 		unset($this->postRequest["suggestion"]);
 	}

 	public function testDetermineSearchTypeREST() 
 	{
 		$this->postRequest["serviceType"] = "REST";
 		$this->postRequest["findPrey"] = "on";
 		unset($this->postRequest["findPredators"]);
 		$this->requestParse->parse($this->postRequest);
		$this->assertTrue($this->requestParse->shouldIncludePrey());
		$this->assertFalse($this->requestParse->shouldIncludePredators());
	
 		unset($this->postRequest["findPrey"]);
 		$this->postRequest["findPredators"] = "on";
 		$this->requestParse->parse($this->postRequest);
 		$this->assertFalse($this->requestParse->shouldIncludePrey());
		$this->assertTrue($this->requestParse->shouldIncludePredators());
	
 		$this->postRequest["suggestion"] = "Scomb";
 		$this->requestParse->parse($this->postRequest);
 		$this->assertEquals('fuzzySearch',  $this->requestParse->getSearchType());
 		unset($this->postRequest["suggestion"]);

 		//observation
 		$this->observationPostRequest["serviceType"] = "REST";
 		$this->observationPostRequest["findPrey"] = "on";
 		unset($this->observationPostRequest["findPredators"]);
 		$this->requestParse->parse($this->observationPostRequest);
		$this->assertTrue($this->requestParse->shouldIncludePrey());
		$this->assertFalse($this->requestParse->shouldIncludePredators());
	
 		unset($this->observationPostRequest["findPrey"]);
 		$this->observationPostRequest["findPredators"] = "on";
 		$this->requestParse->parse($this->observationPostRequest);
 		$this->assertFalse($this->requestParse->shouldIncludePrey());
		$this->assertTrue($this->requestParse->shouldIncludePredators());
	
 		$this->observationPostRequest["suggestion"] = "Scomb";
 		$this->requestParse->parse($this->observationPostRequest);
 		$this->assertEquals('fuzzySearch',  $this->requestParse->getSearchType());
 		unset($this->observationPostRequest["suggestion"]);
 		//END observation
 	}
    /**
     * @expectedException CorruptSearchTypeParameterException
     */
    public function testCorruptSearchTypeParametersListSyleSearch()
    {
    	unset($this->postRequest["findPrey"]);
    	unset($this->postRequest["findPredators"]);
    	$this->requestParse->parse($this->postRequest);
    	$this->requestParse->getSearchType();
    }

     /**
     * @expectedException CorruptSearchTypeParameterException
     */
    public function testCorruptSearchTypeParametersObservationStyleSearch()
    {
    	//observation
    	unset($this->observationPostRequest["findPrey"]);
    	unset($this->observationPostRequest["findPredators"]);
    	$this->requestParse->parse($this->observationPostRequest);
    	$this->requestParse->getSearchType();
    	//END observation
    }

}
?>
