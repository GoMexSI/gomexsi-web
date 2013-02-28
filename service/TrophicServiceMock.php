<?php

class TrophicServiceMock implements TrophicService 
{
    public function findPreyForPredator($predatorScientificName) 
    {
        $preyNames = array('Synalpheus latastei', 'Lutjanus jocu');
        return $preyNames;
    }
}

?>
