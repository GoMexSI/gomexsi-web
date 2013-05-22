<?php

interface TrophicService 
{
    public function findPreyForPredator($predatorScientificName);

    public function findPredatorForPrey($preyScientificName);

    public function findCloseTaxonNameMatches($name);

    public function findObservedPreyForPredator($predatorTaxon, $preyTaxon, $locationConstraints);

    public function findObservedPredatorsForPrey($predatorTaxon, $preyTaxon, $locationConstraints);

    public function findExternalTaxonURL($taxonName);
}

?>
