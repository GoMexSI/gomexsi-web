<?php

class TrophicServiceMock implements TrophicService 
{
    public function findPreyForPredator($predatorScientificName) 
    {
        $preyNames = array('Brachyura', 'Hippomedon', 'Sicyonia dorsalis');
        return $preyNames;
    }
}

?>
