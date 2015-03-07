<?php

class TrophicServiceRESTTest extends PHPUnit_Framework_TestCase
{
    public function testFindPreyForPredator()
    {
        $trophicService = new TrophicServiceREST();
        $preyNames = $trophicService->findPreyForPredator('Ariopsis felis');
        $this->assertTrue(count($preyNames) > 10, 'expected at least 10 prey, but found ' . count($preyNames));
    }
    
    public function testFindPredatorForPrey() 
    {
        $trophicService = new TrophicServiceREST();
        $predatorNames = $trophicService->findPredatorForPrey('Hemiramphus brasiliensis');
        $this->assertTrue(count($predatorNames) > 5, 'expected at least 5 predators, but found ' . count($predatorNames));
    }
    
    public function testFindObservedPreyForPredator()
    {
        $trophicService = new TrophicServiceREST();
        $predatorNames = $trophicService->findObservedPreyForPredator('Ariopsis felis', null, null, null);
        $this->assertTrue(count($predatorNames) > 500, 'expected at least 500 prey items, but found ' . count($predatorNames));
    }

    public function testFindObservedPredatorsForPrey()
    {
        $trophicService = new TrophicServiceREST();
        $predatorNames = $trophicService->findObservedPredatorsForPrey('Crustacea', null, null, null);
        $this->assertTrue(count($predatorNames) > 100, 'expected at least 500 predators, but found ' . count($predatorNames));
    }
    
    public function testFindExternalTaxonURL()
    {
        $trophicService = new TrophicServiceREST();
        $taxonURL = $trophicService->findExternalTaxonURL('Homo sapiens');
        $this->assertStringStartsWith('http', $taxonURL)
    }
    public function testBuildInteractionFilterURL()
    {
        $interactionFilter = array('prey' => 'Callinectes sapidus');
        $trophicService = new TrophicServiceREST();
        $trophicService->findObservedPreyForPredator('Scomberomorus cavalla', $interactionFilter, null, null);
        $expected = $trophicService->getURLPrefix() . "taxon/Scomberomorus%20cavalla/preysOn/Callinectes%20sapidus?includeObservations=true&nw_lat=30.28&nw_lng=-97.89&se_lat=18.04&se_lng=-80.61";
        $this->assertEquals($expected, $trophicService->getURL());
    }
    public function testRawDataDownloadURL()
    {
        $trophicService = new TrophicServiceREST();
        $trophicService->findObservedPreyForPredator('Ariopsis felis', null, null, "CSV");
        $expected = $trophicService->getURLPrefix() . "taxon/Ariopsis%20felis/preysOn?includeObservations=true&nw_lat=30.28&nw_lng=-97.89&se_lat=18.04&se_lng=-80.61&type=csv";
        $this->assertEquals($expected, $trophicService->getURL());

        $trophicService->findObservedPredatorsForPrey('Ariopsis felis', null, null, "CSV");
        $expected = $trophicService->getURLPrefix() . "taxon/Ariopsis%20felis/preyedUponBy?includeObservations=true&nw_lat=30.28&nw_lng=-97.89&se_lat=18.04&se_lng=-80.61&type=csv";
        $this->assertEquals($expected, $trophicService->getURL());
    }
    public function testFindSupportedInteractions()
    {
        $trophicService = new TrophicServiceREST();
        $interactionObject = $trophicService->findSupportedInteractions();
        $this->assertTrue((isset($interactionObject["preysOn"]) && isset($interactionObject["preyedUponBy"])), "Missing interactons");
    }
}
?>
