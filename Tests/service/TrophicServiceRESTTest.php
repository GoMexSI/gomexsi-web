<?php

class TrophicServiceRESTTest extends PHPUnit_Framework_TestCase {
    public function testFindPreyForPredatorREST() {
        $trophicService = TrophicServiceFactory::createServiceOfType('REST');
        $preyNames = $trophicService->findPreyForPredator('Ariopsis felis');
        $this->assertTrue(count($preyNames) > 1);
    }
}

?>
