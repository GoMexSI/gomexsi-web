<?php

interface TrophicService 
{
    public function findPreyForPredator($predatorScientificName);

    public function findPredatorForPrey($preyScientificName);

    public function findCloseTaxonNameMatches($name);

    public function findObservedPreyForPredator($predatorTaxon, $interactionFilters, $locationConstraints, $mimeType);

    public function findObservedPredatorsForPrey($predatorTaxon, $interactionFilters, $locationConstraints, $mimeType);

    public function findExternalTaxonURL($taxonName);
}

?>
