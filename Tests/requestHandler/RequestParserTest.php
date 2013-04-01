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
    public function testDetermineSearchType()
    {	
    	#mock
    	$this->postRequest["serviceType"] = "mock";
		$this->requestParse->parse($this->postRequest);
		$this->assertEquals('findPreyForPredator', $this->requestParse->getSearchType());
		#mock
		unset($this->postRequest["findPrey"]);
		$this->postRequest["findPredators"] = "on";
		$this->requestParse->parse($this->postRequest);
		$this->assertEquals('findPredatorForPrey',  $this->requestParse->getSearchType());
		#mock
		$this->postRequest["suggestion"] = "Scomb";
		$this->requestParse->parse($this->postRequest);
		$this->assertEquals('fuzzySearch',  $this->requestParse->getSearchType());
		unset($this->postRequest["suggestion"]);

		#REST
		$this->postRequest["serviceType"] = "REST";
		$this->postRequest["findPrey"] = "on";
		unset($this->postRequest["findPredators"]);
		$this->requestParse->parse($this->postRequest);
		$this->assertEquals('findPreyForPredator', $this->requestParse->getSearchType());
		#REST
		unset($this->postRequest["findPrey"]);
		$this->postRequest["findPredators"] = "on";
		$this->requestParse->parse($this->postRequest);
		$this->assertEquals('findPredatorForPrey',  $this->requestParse->getSearchType());
		#REST
		$this->postRequest["suggestion"] = "Scomb";
		$this->requestParse->parse($this->postRequest);
		$this->assertEquals('fuzzySearch',  $this->requestParse->getSearchType());
		unset($this->postRequest["suggestion"]);

		#live
		$this->postRequest["serviceType"] = "live";
		$this->postRequest["findPrey"] = "on";
		unset($this->postRequest["findPredators"]);
		$this->requestParse->parse($this->postRequest);
		$this->assertEquals('findPreyForPredator', $this->requestParse->getSearchType());
		#live
		unset($this->postRequest["findPrey"]);
		$this->postRequest["findPredators"] = "on";
		$this->requestParse->parse($this->postRequest);
		$this->assertEquals('findPredatorForPrey',  $this->requestParse->getSearchType());
		#live
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