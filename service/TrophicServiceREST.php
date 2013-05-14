<?php
require_once 'TrophicService.php';

class NotImplementedException extends Exception {}

class TrophicServiceREST implements TrophicService 
{
    public function findPreyForPredator($srcTaxon) 
    {
        return $this->taxonQuery($srcTaxon, 'preysOn');
    }

    public function findPredatorForPrey($srcTaxon)
    {
        return $this->taxonQuery($srcTaxon, 'preyedUponBy');
    }

    public function findObservedPreyForPredator($srcTaxon, $targetTaxon)
    {
        return $this->taxonQuery($srcTaxon, 'preysOn', true);
    }

    public function findObservedPredatorsForPrey($srcTaxon, $targetTaxon)
    {
        return $this->taxonQuery($srcTaxon, 'preyedUponBy', true);
    }

    public function findCloseTaxonNameMatches($name)
    {
        return $this->query('findCloseMatchesForTaxon', $name, null);
    }

    public function findExternalTaxonURL($taxonName)
    {
        return $this->query('findExternalUrlForTaxon', $taxonName, null);
    }

    # taxon Query is probably a stupid name and is not very clear.. TODO rename to something better
    private function taxonQuery($scientificName, $interactionType, $includeObservations = false) 
    {
        $operation = $interactionType;
        if ($includeObservations) 
        {
            $operation = $operation . '?includeObservations=true';
        }   
        return $this->query('taxon', $scientificName, $operation);
    }

    private function query($method, $name, $operation) 
    {
        $url_prefix = 'http://46.4.36.142:8080/' . $method . '/' . rawurlencode($name);

        if (isset($operation)){
            $url = $url_prefix . '/' . $operation;
        } else {
            $url = $url_prefix;
        }

        $response = file_get_contents($url);
        $response = json_decode($response);

        if(strpos($operation, 'Observations') !== FALSE) { # Observational query
            return $this->observationalSearchContainerPopulator($response);
        } elseif ($method == 'findExternalUrlForTaxon') {  # External URL lookup query
            return $response->{'url'};
        } else {                                           # Fuzzy lookup returns and exhaustive list return, TODO contemplate moving this into a function
            $columns = $response->{'columns'};
            $preyDataList = $response->{'data'};
            $preyNames = array();
            foreach ($preyDataList as $preyData) {
                foreach ($preyData as $preyName) {
                    $preyNames[] = $preyName;
                }
            }
            return $preyNames;
        }
    }
    /*
      FUNCTION: observationalSearchContainerPopulator

      PURPOSE: Helper function that populates the return container for observational queries

      This function takes the response from the rest service and converts it into a more usable format
    */
    private function observationalSearchContainerPopulator($response){

            $columns = $response->{'columns'};
            $dataList = $response->{'data'};
            $container = array();
            $i = 0;
            foreach ($dataList as $taxonData) 
            {
                $container[$i] = array();
                $container[$i][0] = $taxonData[0]; #preyName
                $container[$i][1] = $taxonData[1]; #latitude
                $container[$i][2] = $taxonData[2]; #longitude
                $container[$i][3] = $taxonData[3]; #altitude
                $container[$i][4] = $taxonData[4]; #contributor
                $container[$i][5] = $taxonData[5]; #unix epoch
                $container[$i][6] = $taxonData[6]; #tmp_and_unique_specimen_id
                $container[$i][7] = $taxonData[7]; #predator life stage
                $container[$i][8] = $taxonData[8]; #prey life stage
                $container[$i][9] = $taxonData[9]; #predator body part
                $container[$i][10] = $taxonData[10]; #prey body part
                $container[$i][11] = $taxonData[11]; #predator physiological state
                $container[$i][12] = $taxonData[12]; #prey physiological state

                #could do a nested for, but the straight assignment will probably be faster for huge datasets
                $i+=1;
            }
            return $container;
    }
}

?>
