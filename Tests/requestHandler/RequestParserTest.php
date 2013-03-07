<?php

require_once 'requestHandler/RequestParser.php';

class RequestParserTest extends PHPUnit_Framework_TestCase
{
	private $requestParse;
	private $predParse;
	private $preyParse;

	public function setUp()
    {
    	$this->requestParse = new RequestParser();
    	$this->predParse = array('predName' => 'Scomberomorus cavalla');
    	$this->preyParse = array('preyName' => 'Scomberomorus cavalla');
    }
    public function testParse()
	{
		$this->assertEquals($this->requestParse->parse($this->predParse), "");
		$this->assertEquals($this->requestParse->parse($this->preyParse), "");
	}
    public function testDetermineSearchType()
    {
		$this->requestParse->parse($this->predParse);
		$this->assertEquals('findPreyForPredator', $this->requestParse->getSearchType());

		$this->requestParse->parse($this->preyParse);
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