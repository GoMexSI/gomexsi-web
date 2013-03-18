<?php

interface TrophicService 
{
    public function findPreyForPredator($predatorScientificName);

    public function findPredatorForPrey($preyScientificName);

    public function findCloseTaxonNameMatches($name);
}

?>
