<?php
require_once 'TrophicService.php';

class NotImplementedException extends Exception {}
class NonValidLocationParameterException extends Exception {}

class TrophicServiceREST implements TrophicService 
{
    public function findPreyForPredator($srcTaxon) 
    {
        $constraints['includeObservations'] = false;
        return $this->queryBuilder($srcTaxon, 'preysOn', $constraints);
    }

    public function findPredatorForPrey($srcTaxon)
    {
        $constraints['includeObservations'] = false;
        return $this->queryBuilder($srcTaxon, 'preyedUponBy', $constraints);
    }

    public function findObservedPreyForPredator($srcTaxon, $interactionFilters, $locationConstraints)
    {
        $constraints['includeObservations'] = true;
        $this->setLocationConstraints($locationConstraints, $constraints);
        return $this->queryBuilder($srcTaxon, 'preysOn', $constraints);
    }

    public function findObservedPredatorsForPrey($srcTaxon, $interactionFilters, $locationConstraints)
    {
        $constraints['includeObservations'] = true;
        $this->setLocationConstraints($locationConstraints, $constraints);
        return $this->queryBuilder($srcTaxon, 'preyedUponBy', $constraints);
    }

    public function findCloseTaxonNameMatches($name)
    {
        return $this->query('findCloseMatchesForTaxon', $name, null);
    }

    public function findExternalTaxonURL($taxonName)
    {
        return $this->query('findExternalUrlForTaxon', $taxonName, null);
    }
    private function setLocationConstraints($locationConstraints, &$constraints)
    {
        if(isset($locationConstraints)) {

            if(!isset($locationConstraints['nw_lat']) || !isset($locationConstraints['nw_lng']) || !isset($locationConstraints['se_lat']) || !isset($locationConstraints['se_lng'])) {
                throw new NonValidLocationParameterException('Missing parameter(s) for location constraints');
            }
            $constraints['nw_lat'] =  $locationConstraints['nw_lat'];
            $constraints['nw_lng'] =  $locationConstraints['nw_lng'];
            $constraints['se_lat'] =  $locationConstraints['se_lat'];
            $constraints['se_lng'] =  $locationConstraints['se_lng'];
        }else { //else set default polygon parameters
            $constraints['nw_lat'] =  30.28;    # currently these four represent a rectangular approximation of the gulf
            $constraints['nw_lng'] =  -97.89;   # These can be changed for a better fit, will be updated to a polygon later down the line  
            $constraints['se_lat'] =  18.04;
            $constraints['se_lng'] =  -80.61;
        }
    }
    #Helper fuction for query. Builds the correct strings and parameters for the query function
    private function queryBuilder($scientificName, $interactionType, $constraints) 
    {
        $operation = $interactionType;
        if ($constraints['includeObservations']) {
            $operation = $operation . '?includeObservations=true';
            if(isset($constraints['nw_lat'])) {
                $operation = $operation . '&nw_lat=' . $constraints['nw_lat'] . '&nw_lng=' . $constraints['nw_lng'] . '&se_lat=' . $constraints['se_lat'] . '&se_lng=' . $constraints['se_lng'];
            }
        }   
        return $this->query('taxon', $scientificName, $operation);
    }
    /* 
    
        Function: query
        Purpose : call the rest service using the proper URL and receive the respose containing the data
        Parameters:
                    method: the rest service method
                    name: the name of the thing being operated on, the subject of the query
                    operation: what is the operation that is being done to the subject
    */
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
