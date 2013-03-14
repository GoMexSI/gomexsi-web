<?php

require_once 'requestHandler/RequestHandler.php';

class RequestHandlerRESTTest extends PHPUnit_Framework_TestCase
{
	private $handler;

	public function setUp()
    {
    	$this->handler = new RequestHandler();

    }
    //Test for Use case One
	public function testGetTrophicServiceRESTFindPreyForPredator()
	{
		$trophicResultString = array();
		$expectedPreyNames = array("Digestate", "Actinopterygii", "Penaeidae", "Gobiidae", "NomenNescio", "Aegathoa oculata", "Pleocyemata spp.");

    	$RESTURL = array("serviceType" => "REST", "predName" => "Caranx hippos");
    	$this->handler->parsePOST($RESTURL);

		$trophicService = $this->handler->getTrophicService();
		$trophicResultString = $trophicService->findPreyForPredator("Caranx hippos");
		
		$this->assertEquals(count($expectedPreyNames), count($trophicResultString));
		
		$iterator = 0;
/*		foreach ($trophicResultString as $value) {
			$this->assertEquals($expectedPreyNames[$iterator], $value);
			$iterator++;
		}*/

	}
	//Test for Use case One
/*	public function testCreatJSONResponseMockFindPreyForPredator()
	{
		$mockURL = array("serviceType" => "mock", "predName" => "Scomberomorus cavalla");
    	$this->handler->parsePOST($mockURL);

		$this->handler->getTrophicService();

		$jsonTestString = '[{"scientificName":"Scomberomorus cavalla","subjectInstances":[{"prey":["Synalpheus latastei","Lutjanus jocu"]}]}]';
		// http://jsonlint.com/ will format this for anyone who wants to look at it in a more readable structure 

		$jsonObject = $this->handler->creatJSONResponse();
		$this->assertEquals($jsonTestString, $jsonObject);
	}*/
}
?>