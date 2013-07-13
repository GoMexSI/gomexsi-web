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
    #toDO build test to check new URL for interaction filters
}
?>
