<?php

require_once 'requestHandler/RequestParser.php';

class RequestParserTest extends PHPUnit_Framework_TestCase
{
	private $requestParse;

	public function setUp()
    {
    	$this->requestParse = new RequestParser();
    }
    public function testDetermineSearchType()
    {
		$toParseA = array('predName' => 'Scomberomorus cavalla');
		$this->assertEquals('findPreyForPredator', $this->requestParse->determineSearchType($toParseA));

		$toParseB = array('preyName' => 'Scomberomorus cavalla');
		$this->assertEquals('findPredatorForPrey',  $this->requestParse->determineSearchType($toParseB));
    }
	public function testParse()
	{
		$toParseA = array('predName' => 'Scomberomorus cavalla');
		$this->assertEquals($this->requestParse->parse($toParseA), "");

		$toParseB = array('preyName' => 'Scomberomorus cavalla');
		$this->assertEquals($this->requestParse->parse($toParseB), "");
	}
    /**
     * @expectedException CorruptSearchTypeParameterException
     */
	public function testCorruptSearchTypeParameters()
	{
		$badURL = array('nonValidKey' => '', );
		$this->requestParse->determineSearchType($badURL);
	}
	
}
?>