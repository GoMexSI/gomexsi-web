 <?php

require_once 'requestHandler/RequestParser.php';

class RequestParserTest extends PHPUnit_Framework_TestCase
{
	private $requestParse;

	public function setUp()
    {
    	$this->requestParse = new RequestParser();
    }
    public function testDetermineServiceType()
	{
		#mock
    	$predParseMock = array("serviceType" => "mock", "predName" => "Scomberomorus cavalla");
		$this->requestParse->parse($predParseMock);
		$this->assertEquals('mock', $this->requestParse->getServiceType());

		#REST
		$predParseREST = array("serviceType" => "REST", "predName" => "Scomberomorus cavalla");
		$this->requestParse->parse($predParseREST);
		$this->assertEquals('REST', $this->requestParse->getServiceType());

		#live
		$predParseLive = array("serviceType" => "live", "predName" => "Scomberomorus cavalla");
		$this->requestParse->parse($predParseLive);
		$this->assertEquals('live', $this->requestParse->getServiceType());
	}
    public function testDetermineSearchType()
    {	
    	#mock
    	$predParseMock = array("serviceType" => "mock", "predName" => "Scomberomorus cavalla");
		$this->requestParse->parse($predParseMock);
		$this->assertEquals('findPreyForPredator', $this->requestParse->getSearchType());
		#mock
		$preyParseMock = array("serviceType" => "mock", "preyName" => "Scomberomorus cavalla");
		$this->requestParse->parse($preyParseMock);
		$this->assertEquals('findPredatorForPrey',  $this->requestParse->getSearchType());

		#REST
		$predParseREST = array("serviceType" => "REST", "predName" => "Scomberomorus cavalla");
		$this->requestParse->parse($predParseREST);
		$this->assertEquals('findPreyForPredator', $this->requestParse->getSearchType());
		#REST
		$preyParseREST = array("serviceType" => "REST", "preyName" => "Scomberomorus cavalla");
		$this->requestParse->parse($preyParseREST);
		$this->assertEquals('findPredatorForPrey',  $this->requestParse->getSearchType());

		#live
		$predParseLive = array("serviceType" => "live", "predName" => "Scomberomorus cavalla");
		$this->requestParse->parse($predParseLive);
		$this->assertEquals('findPreyForPredator', $this->requestParse->getSearchType());
		#live
		$preyParseLive = array("serviceType" => "live", "preyName" => "Scomberomorus cavalla");
		$this->requestParse->parse($preyParseLive);
		$this->assertEquals('findPredatorForPrey',  $this->requestParse->getSearchType());
    }

    /**
     * @expectedException CorruptSearchTypeParameterException
     */
	public function testCorruptSearchTypeParameters()
	{
		$badURL = array('nonValidKey' => '', );
		$this->requestParse->parse($badURL);
		$this->requestParse->getSearchType();
	}
	
}
?>