<?php

class TrophicServiceMock implements TrophicService 
{
    public function findPreyForPredator($predatorScientificName) 
    {
        return array('Synalpheus latastei', 'Lutjanus jocu');
    }

    public function findPredatorForPrey($preyScientificName)
    {
    	return array('Ariopsis felis', 'Scomberomorus cavalla');
    }

    public function findCloseTaxonNameMatches($name)
    {
    	return array('Ariopsis felis', 'Scomberomorus cavalla');
    }
}

?>
