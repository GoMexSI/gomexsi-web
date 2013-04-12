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
    public function findObservedPreyForPredator($predatorTaxon, $preyTaxon)
    {
        $stringArray = array("Micropogonias undulatus", 28.645202, -96.099923, 0.0, "Senol Akin", 923695200000, "Brevoortia patronus", 28.645202, -96.099923, 0.0, "Senol Akin", 923695200000, "Farfantepenaeus aztecus", 28.645202, -96.099923, 0.0, "Senol Akin", 923695200000, "Mollusca",8.645202, -96.099923, 0.0, "Senol Akin", 923695200000, "Bivalvia", 28.645202, -96.099923, 0.0, "Senol Akin", 923695200000, "Actinopterygii", 28.645202, -96.099923, 0.0, "Senol Akin", 923695200000, "Callinectes sapidus", 28.639232, -96.105117, 0.0, "Senol Akin", 923695200000, "Farfantepenaeus aztecus", 28.645202, -96.099923, 0.0, "Senol Akin", 923695200000, "Mollusca", 28.642313, -96.103142, 0.0, "Senol Akin", 923695200000);
        $container = array(); // [row][colum]

        $k=0;
        for($i=0; $i<9; $i++) { // 6 rows of data(mock)
            $container[$i] = array(); 
            for($j=0; $j<6; $j++) { // 5 columns
                $container[$i][$j] = $stringArray[$k];
                $k+=1;
            }
        }
        return $container;
    }
    public function findObservedPredatorForPrey($predatorTaxon, $preyTaxon)
    {

    }
    public function findExternalTaxonURL($taxonName)
    {
        return "http://eol.org/pages/327955";
    }

}

?>
