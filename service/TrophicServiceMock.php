<?php

class TrophicServiceMock implements TrophicService 
{

    private function arrayToContainer($stringArray) {
        $container = array(); // [row][colum]

        $k=0;
        #if you change the above array contents, be sure to adjust values below..
        for($i=0; $i<6; $i++) { // 6 rows of data(mock)
            $container[$i] = array(); 
            for($j=0; $j<8; $j++) { // 8 columns
                $container[$i][$j] = $stringArray[$k];
                $k+=1;
            }
        }
        return $container;
    }

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
    // should be phased out, after new interaction functionality is added
    public function findObservedPreyForPredator($predatorTaxon, $preyTaxon, $locationConstraints, $mimeType)
    {
        $stringArray = array("Micropogonias undulatus",  28.645202, -96.099923, 0.0, "Senol Akin", 923695200000, 1,"some WKT",
                             "Brevoortia patronus",      28.645202, -96.099923, 0.0, "Senol Akin", 923695200000, 3,"some WKT",
                             "Farfantepenaeus aztecus",  28.645202, -96.099923, 0.0, "Senol Akin", 923695200000, 2,"some WKT",
                             "Mollusca",                 8.645202,  -96.099923, 0.0, "Senol Akin", 923695200000, 1,"some WKT",
                             "T-Rex",                    8.645202,  -96.099923, 0.0, "MichaelCas", 923695200000, 3,"some WKT",
                             "Pterodactyl",              8.645202,  -96.099923, 0.0, "MichaelCas", 923695200000, 3, "some WKT");
        return $this->arrayToContainer($stringArray);
    }
    // should be phased out, after new interaction functionality is added
    public function findObservedPredatorsForPrey($predatorTaxon, $preyTaxon, $locationConstraints, $mimeType)
    {
        $stringArray = array("Micropogonias undulatus", 28.645202, -96.099923, 0.0, "Rogers 1977", 923695200000, 1,"some WKT",
                             "Actinopterygii",          28.645202, -96.099923, 0.0, "Senol Akin",  923695200000, 1,"some WKT",
                             "Great White",             28.645202, -96.099923, 0.0, "John Mayer",  923695200000, 4,"some WKT",
                             "Mollusca",                8.645202,  -96.099923, 0.0, "Senol Akin",  923695200000, 4,"some WKT",
                             "Velociraptor",            8.645202,  -96.099923, 0.0, "MichaelCas",  923695200000, 2,"some WKT",
                             "Tiger Shark",             8.645202,  -96.099923, 0.0, "MichaelCas",  923695200000, 4, "some WKT");
        return $this->arrayToContainer($stringArray);
    }


    public function findObservedTargetForSource($predatorTaxon, $preyTaxon, $locationConstraints, $mimeType, $interaction)
    {
        if($interaction->getInteractionTitle() == 'eatenBy') {
            $stringArray = array("Micropogonias undulatus", 28.645202, -96.099923, 0.0, "Rogers 1977", 923695200000, 1, "some WKT",
                                 "Actinopterygii",          28.645202, -96.099923, 0.0, "Senol Akin",  923695200000, 1,"some WKT",
                                 "Great White",             28.645202, -96.099923, 0.0, "John Mayer",  923695200000, 4,"some WKT",
                                 "Mollusca",                8.645202,  -96.099923, 0.0, "Senol Akin",  923695200000, 4,"some WKT",
                                 "Velociraptor",            8.645202,  -96.099923, 0.0, "MichaelCas",  923695200000, 2,"some WKT",
                                 "Tiger Shark",             8.645202,  -96.099923, 0.0, "MichaelCas",  923695200000, 4, "some WKT");
            return $this->arrayToContainer(i$stringArray);
        } else {
            $stringArray = array("Micropogonias undulatus",  28.645202, -96.099923, 0.0, "Senol Akin", 923695200000, 1,"some WKT",
                                 "Brevoortia patronus",      28.645202, -96.099923, 0.0, "Senol Akin", 923695200000, 3,"some WKT",
                                 "Farfantepenaeus aztecus",  28.645202, -96.099923, 0.0, "Senol Akin", 923695200000, 2,"some WKT",
                                 "Mollusca",                 8.645202,  -96.099923, 0.0, "Senol Akin", 923695200000, 1,"some WKT",
                                 "T-Rex",                    8.645202,  -96.099923, 0.0, "MichaelCas", 923695200000, 3,"some WKT",
                                 "Pterodactyl",              8.645202,  -96.099923, 0.0, "MichaelCas", 923695200000, 3,"some WKT");
            return $this->arrayToContainer($stringArray);
        }
    }
    public function findExternalTaxonURL($taxonName)
    {
        return "http://eol.org/pages/327955";
    }

}

?>
