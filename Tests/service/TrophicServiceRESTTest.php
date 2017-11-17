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
        $this->assertGreaterThan(100, count($predatorNames), 'expected at least 500 predators, but found ' . count($predatorNames));
    }
    
    public function testFindExternalTaxonURL()
    {
        $trophicService = new TrophicServiceREST();
        $taxonURL = $trophicService->findExternalTaxonURL('Ariopsis felis');
        $this->assertContains('http://fishbase.org/summary/947', $taxonURL);
    }
    public function testBuildInteractionFilterURL()
    {
        $interactionFilter = array('prey' => 'Callinectes sapidus');
        $trophicService = new TrophicServiceREST();
        $trophicService->findObservedPreyForPredator('Scomberomorus cavalla', $interactionFilter, null, null);
        $expected = $trophicService->getURLPrefix() . "taxon/Scomberomorus%20cavalla/eats/Callinectes%20sapidus?includeObservations=true&bbox=-97.89%2C18.04%2C-80.61%2C30.28";
        $this->assertEquals($expected, $trophicService->getURL());
    }
    public function testRawDataDownloadURL()
    {
        $trophicService = new TrophicServiceREST();
        $trophicService->findObservedPreyForPredator('Ariopsis felis', null, null, "CSV");
        $expected = $trophicService->getURLPrefix() . "taxon/Ariopsis%20felis/eats?includeObservations=true&bbox=-97.89%2C18.04%2C-80.61%2C30.28&type=csv";
        $this->assertEquals($expected, $trophicService->getURL());

        $trophicService->findObservedPredatorsForPrey('Ariopsis felis', null, null, "CSV");
        $expected = $trophicService->getURLPrefix() . "taxon/Ariopsis%20felis/eatenBy?includeObservations=true&bbox=-97.89%2C18.04%2C-80.61%2C30.28&type=csv";
        $this->assertEquals($expected, $trophicService->getURL());
    }

    public function testRawDataDownloadURLByFishbaseID()
    {
        $trophicService = new TrophicServiceREST();
        $trophicService->findObservedPreyForPredator('FBC:FB:SpecCode:120', null, null, "CSV");
        $expected = $trophicService->getURLPrefix() . "taxon/FBC%3AFB%3ASpecCode%3A120/eats?includeObservations=true&bbox=-97.89%2C18.04%2C-80.61%2C30.28&type=csv";
        $this->assertEquals($expected, $trophicService->getURL());

        $trophicService->findObservedPredatorsForPrey('FBC:FB:SpecCode:120', null, null, "CSV");
        $expected = $trophicService->getURLPrefix() . "taxon/FBC%3AFB%3ASpecCode%3A120/eatenBy?includeObservations=true&bbox=-97.89%2C18.04%2C-80.61%2C30.28&type=csv";
        $this->assertEquals($expected, $trophicService->getURL());
    }

    public function testFindSupportedInteractions()
    {
        $trophicService = new TrophicServiceREST();
        $interactionObject = $trophicService->findSupportedInteractions();
        $this->assertTrue((isset($interactionObject["eats"]) && isset($interactionObject["eatenBy"])), "Missing interactons");
    }
}
?>
