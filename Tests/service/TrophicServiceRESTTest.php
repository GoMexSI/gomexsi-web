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
        $predatorNames = $trophicService->findObservedPreyForPredator('Ariopsis felis', null, null);
        $this->assertTrue(count($predatorNames) > 500, 'expected at least 500 prey items, but found ' . count($predatorNames));
    }

    public function testFindObservedPredatorsForPrey()
    {
        $trophicService = new TrophicServiceREST();
        $predatorNames = $trophicService->findObservedPredatorsForPrey('Crustacea', null, null);
        $this->assertTrue(count($predatorNames) > 100, 'expected at least 500 predators, but found ' . count($predatorNames));
    }
    
    public function testFindExternalTaxonURL()
    {
        $trophicService = new TrophicServiceREST();
        $taxonURL = $trophicService->findExternalTaxonURL('Homo sapiens');
        $expected = "http://eol.org/pages/327955";
        $this->assertEquals($expected, $taxonURL);
    }
    public function testBuildInteractionFilterURL()
    {
        $interactionFilter = array('prey' => 'Rattus rattus');
        $trophicService = new TrophicServiceREST();
        $trophicService->findObservedPreyForPredator('Homo sapiens', $interactionFilter, null);
        $expected = "http://46.4.36.142:8080/taxon/Homo%20sapiens/preysOn/Rattus%20rattus?includeObservations=true&nw_lat=30.28&nw_lng=-97.89&se_lat=18.04&se_lng=-80.61";
        $this->assertEquals($expected, $trophicService->getURL());
    }
}
?>
