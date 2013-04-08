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
        $stringArray = array("Micropogonias undulatus", 28.645202, -96.099923, 0.0, "Senol Akin", "Brevoortia patronus", 28.645202, -96.099923, 0.0, "Senol Akin", "Farfantepenaeus aztecus", 28.645202, -96.099923, 0.0, "Senol Akin", "Mollusca",8.645202, -96.099923, 0.0, "Senol Akin" , "Bivalvia", 28.645202, -96.099923, 0.0, "Senol Akin","Actinopterygii", 28.645202, -96.099923, 0.0, "Senol Akin");
        $container = array(); // [row][colum]

        $k=0;
        for($i=0; $i<6; $i++) { // 6 rows of data(mock)
            $container[$i] = array(); 
            for($j=0; $j<5; $j++) { // 5 columns
                $container[$i][$j] = $stringArray[$k];
                $k+=1;
            }
        }


        return $container;

    }
    public function findObservedPredatorForPrey($predatorTaxon, $preyTaxon)
    {

    }

    #TODO add findObservedPreyForPredator(predatorTaxon, preyTaxon) and findObservedPredatorForPrey
    #TODO make observed the default action. and the mock data to look just like Reeds mock data
}

?>
