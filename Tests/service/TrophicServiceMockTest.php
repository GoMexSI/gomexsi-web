<?php

class TrophicServiceMockTest extends PHPUnit_Framework_TestCase 
{
    public function testFindPreyForPredatorMock() 
    {
        $trophicService = new TrophicServiceMock();
        $preyNames = $trophicService->findPreyForPredator('Ariopsis felis');
        $this->assertEquals(2, count($preyNames));
    }

    public function testFindPredatorForPreyMock() 
    {
        $trophicService = new TrophicServiceMock();
        $preyNames = $trophicService->findPredatorForPrey('Anything');
        $this->assertEquals(2, count($preyNames));
    }

}

?>
