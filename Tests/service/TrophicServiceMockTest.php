<?php

class TrophicServiceMockTest extends PHPUnit_Framework_TestCase 
{
    public function testFindPreyForPredatorMock() 
    {
        $trophicService = new TrophicServiceMock();
        $preyNames = $trophicService->findPreyForPredator('Ariopsis felis');
        $this->assertEquals(2, count($preyNames));
    }

}

?>
