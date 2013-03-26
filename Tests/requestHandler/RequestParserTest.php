 <?php

require_once 'requestHandler/RequestParser.php';

class RequestParserTest extends PHPUnit_Framework_TestCase
{
	private $requestParse;
	private $toParse;

	public function setUp()
    {
    	$this->requestParse = new RequestParser();
    	$this->toParse = array("serviceType" => "mock",
    						   "findPrey"=>"true",
    						   "findPredators" => "false",
    						   "subjectName" => "Scomberomorus cavalla");
    }
    public function testDetermineServiceType()
	{
		#mock
		$this->requestParse->parse($this->toParse);
		$this->assertEquals('mock', $this->requestParse->getServiceType());
		#REST
		$this->toParse["serviceType"] = "REST";
		$this->requestParse->parse($this->toParse);
		$this->assertEquals('REST', $this->requestParse->getServiceType());
		#live
		$this->toParse["serviceType"] = "live";
		$this->requestParse->parse($this->toParse);
		$this->assertEquals('live', $this->requestParse->getServiceType());
	}
    public function testDetermineSearchType()
    {	
    	#mock
    	$this->toParse["serviceType"] = "mock";
		$this->requestParse->parse($this->toParse);
		$this->assertEquals('findPreyForPredator', $this->requestParse->getSearchType());
		#mock
		$this->toParse["findPrey"] = "false";
		$this->toParse["findPredators"] = "true";
		$this->requestParse->parse($this->toParse);
		$this->assertEquals('findPredatorForPrey',  $this->requestParse->getSearchType());
		#mock
		$this->toParse["suggestion"] = "Scomb";
		$this->requestParse->parse($this->toParse);
		$this->assertEquals('fuzzySearch',  $this->requestParse->getSearchType());
		unset($this->toParse["suggestion"]);

		#REST
		$this->toParse["serviceType"] = "REST";
		$this->toParse["findPrey"] = "true";
		$this->toParse["findPredators"] = "false";
		$this->requestParse->parse($this->toParse);
		$this->assertEquals('findPreyForPredator', $this->requestParse->getSearchType());
		#REST
		$this->toParse["findPrey"] = "false";
		$this->toParse["findPredators"] = "true";
		$this->requestParse->parse($this->toParse);
		$this->assertEquals('findPredatorForPrey',  $this->requestParse->getSearchType());
		#REST
		$this->toParse["suggestion"] = "Scomb";
		$this->requestParse->parse($this->toParse);
		$this->assertEquals('fuzzySearch',  $this->requestParse->getSearchType());
		unset($this->toParse["suggestion"]);

		#live
		$this->toParse["serviceType"] = "live";
		$this->toParse["findPrey"] = "true";
		$this->toParse["findPredators"] = "false";
		$this->requestParse->parse($this->toParse);
		$this->assertEquals('findPreyForPredator', $this->requestParse->getSearchType());
		#live
		$this->toParse["findPrey"] = "false";
		$this->toParse["findPredators"] = "true";
		$this->requestParse->parse($this->toParse);
		$this->assertEquals('findPredatorForPrey',  $this->requestParse->getSearchType());
		#live
		$this->toParse["suggestion"] = "Scomb";
		$this->requestParse->parse($this->toParse);
		$this->assertEquals('fuzzySearch',  $this->requestParse->getSearchType());
		unset($this->toParse["suggestion"]);
    }

    /**
     * @expectedException CorruptSearchTypeParameterException
     */
	public function testCorruptSearchTypeParameters()
	{
		$this->toParse["findPrey"] = "false";
		$this->toParse["findPredators"] = "false";
		$this->requestParse->parse($this->toParse);
		$this->requestParse->getSearchType();
	}
	
}
?>