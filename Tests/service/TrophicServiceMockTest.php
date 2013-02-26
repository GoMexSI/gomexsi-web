<?php

class TrophicServiceMockTest extends PHPUnit_Framework_TestCase {
    public function testFindPreyForPredatorMock() {
        $trophicService = TrophicServiceFactory::createServiceOfType('mock');
        $preyNames = $trophicService->findPreyForPredator('Ariopsis felis');
        $this->assertEquals(3, count($preyNames));
    }

}

?>
