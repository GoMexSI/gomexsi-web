 <?php

 require_once 'requestHandler/RequestParser.php';

 class RequestParserTest extends PHPUnit_Framework_TestCase
 {
 	private $requestParse;
 	private $postRequest;

 	public function setUp()
 	{
 		$this->requestParse = new RequestParser();
 		$this->postRequest = array("serviceType" => "mock",
 			"findPrey"=>"on",
 			"subjectName" => "Scomberomorus cavalla");
 	}
 	public function testDetermineServiceType()
 	{
		#mock
 		$this->requestParse->parse($this->postRequest);
 		$this->assertEquals('mock', $this->requestParse->getServiceType());
		#REST
 		$this->postRequest["serviceType"] = "REST";
 		$this->requestParse->parse($this->postRequest);
 		$this->assertEquals('REST', $this->requestParse->getServiceType());
		#live
 		$this->postRequest["serviceType"] = "live";
 		$this->requestParse->parse($this->postRequest);
 		$this->assertEquals('live', $this->requestParse->getServiceType());
 	}

 	public function testDetermineSearchTypeMock()
 	{	
    	$this->postRequest["serviceType"] = "mock";
 		$this->requestParse->parse($this->postRequest);
 		$this->assertEquals('exactMatch',  $this->requestParse->getSearchType());
		$this->assertTrue($this->requestParse->shouldIncludePrey());
		$this->assertFalse($this->requestParse->shouldIncludePredators());
	
		unset($this->postRequest["findPrey"]);
 		$this->postRequest["findPredators"] = "on";
 		$this->requestParse->parse($this->postRequest);
 		$this->assertEquals('exactMatch',  $this->requestParse->getSearchType());
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
 	}

 	public function testDetermineSearchTypeLive() 
 	{
		
 		$this->postRequest["serviceType"] = "live";
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
 	}

    /**
     * @expectedException CorruptSearchTypeParameterException
     */
    public function testCorruptSearchTypeParameters()
    {
    	unset($this->postRequest["findPrey"]);
    	unset($this->postRequest["findPredators"]);
    	$this->requestParse->parse($this->postRequest);
    	$this->requestParse->getSearchType();
    }

}
?>