<?php

interface TrophicService 
{
    public function findPreyForPredator($predatorScientificName);

    public function findPredatorForPrey($preyScientificName);

    public function findCloseTaxonNameMatches($name);

    public function findObservedPreyForPredator($predatorTaxon, $preyTaxon);

    public function findObservedPredatorForPrey($predatorTaxon, $preyTaxon);

    public function findExternalTaxonURL($taxonName);
}

?>
