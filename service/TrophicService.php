<?php

interface TrophicService 
{
    public function findPreyForPredator($predatorScientificName);

    public function findPredatorForPrey($preyScientificName);

    public function findCloseTaxonNameMatches($name);

    public function findObservedPreyForPredator($predatorTaxon, $interactionFilters, $locationConstraints);

    public function findObservedPredatorsForPrey($predatorTaxon, $interactionFilters, $locationConstraints);

    public function findExternalTaxonURL($taxonName);
}

?>
