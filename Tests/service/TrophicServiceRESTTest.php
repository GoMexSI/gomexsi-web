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
        $this->assertTrue(count($predatorNames) > 5, 'expected at least 10 predators, but found ' . count($predatorNames));
    }
}

?>
