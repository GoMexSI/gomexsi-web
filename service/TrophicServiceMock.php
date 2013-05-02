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
        $stringArray = array("Micropogonias undulatus",  28.645202, -96.099923, 0.0, "Senol Akin", 923695200000, 1,
                             "Brevoortia patronus",      28.645202, -96.099923, 0.0, "Senol Akin", 923695200000, 1,
                             "Farfantepenaeus aztecus",  28.645202, -96.099923, 0.0, "Senol Akin", 923695200000, 2,
                             "Mollusca",                 8.645202,  -96.099923, 0.0, "Senol Akin", 923695200000, 3,
                             "T-Rex",                    8.645202,  -96.099923, 0.0, "MichaelCas", 923695200000, 3,
                             "Pterodactyl",              8.645202,  -96.099923, 0.0, "MichaelCas", 923695200000, 3);
        $container = array(); // [row][colum]

        $k=0;
        #if you change the above array contents, be sure to adjust values below..
        for($i=0; $i<6; $i++) { // 6 rows of data(mock)
            $container[$i] = array(); 
            for($j=0; $j<7; $j++) { // 7 columns
                $container[$i][$j] = $stringArray[$k];
                $k+=1;
            }
        }
        return $container;
    }
    public function findObservedPredatorsForPrey($predatorTaxon, $preyTaxon)
    {
        $stringArray = array("Micropogonias undulatus", 28.645202, -96.099923, 0.0, "Rogers 1977", 923695200000, 1,
                             "Actinopterygii",          28.645202, -96.099923, 0.0, "Senol Akin",  923695200000, 1,
                             "Great White",             28.645202, -96.099923, 0.0, "John Mayer",  923695200000, 2,
                             "Mollusca",                8.645202,  -96.099923, 0.0, "Senol Akin",  923695200000, 3,
                             "Velociraptor",            8.645202,  -96.099923, 0.0, "MichaelCas",  923695200000, 4,
                             "Tiger Shark",             8.645202,  -96.099923, 0.0, "MichaelCas",  923695200000, 5);
        $container = array(); // [row][colum]

        $k=0;
        #if you change the above array contents, be sure to adjust values below..
        for($i=0; $i<6; $i++) { // 6 rows of data(mock)
            $container[$i] = array(); 
            for($j=0; $j<7; $j++) { // 7 columns
                $container[$i][$j] = $stringArray[$k];
                $k+=1;
            }
        }
        return $container;
    }
    public function findExternalTaxonURL($taxonName)
    {
        return "http://eol.org/pages/327955";
    }

}

?>
