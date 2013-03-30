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
    						   "findPrey"=>"on",
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
		unset($this->toParse["findPrey"]);
		$this->toParse["findPredators"] = "on";
		$this->requestParse->parse($this->toParse);
		$this->assertEquals('findPredatorForPrey',  $this->requestParse->getSearchType());
		#mock
		$this->toParse["suggestion"] = "Scomb";
		$this->requestParse->parse($this->toParse);
		$this->assertEquals('fuzzySearch',  $this->requestParse->getSearchType());
		unset($this->toParse["suggestion"]);

		#REST
		$this->toParse["serviceType"] = "REST";
		$this->toParse["findPrey"] = "on";
		unset($this->toParse["findPredators"]);
		$this->requestParse->parse($this->toParse);
		$this->assertEquals('findPreyForPredator', $this->requestParse->getSearchType());
		#REST
		unset($this->toParse["findPrey"]);
		$this->toParse["findPredators"] = "on";
		$this->requestParse->parse($this->toParse);
		$this->assertEquals('findPredatorForPrey',  $this->requestParse->getSearchType());
		#REST
		$this->toParse["suggestion"] = "Scomb";
		$this->requestParse->parse($this->toParse);
		$this->assertEquals('fuzzySearch',  $this->requestParse->getSearchType());
		unset($this->toParse["suggestion"]);

		#live
		$this->toParse["serviceType"] = "live";
		$this->toParse["findPrey"] = "on";
		unset($this->toParse["findPredators"]);
		$this->requestParse->parse($this->toParse);
		$this->assertEquals('findPreyForPredator', $this->requestParse->getSearchType());
		#live
		unset($this->toParse["findPrey"]);
		$this->toParse["findPredators"] = "on";
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
		unset($this->toParse["findPrey"]);
		unset($this->toParse["findPredators"]);
		$this->requestParse->parse($this->toParse);
		$this->requestParse->getSearchType();
	}
	
}
?>