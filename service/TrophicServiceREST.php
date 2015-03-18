<?php
require_once 'TrophicService.php';
require_once __DIR__.'/../interaction/SupportedInteractions.php';

class NotImplementedException extends Exception {}
class NonValidLocationParameterException extends Exception {}
class UnsupportedInteractionTypeException extends Exception {}

class TrophicServiceREST implements TrophicService 
{
    const URL_PREFIX = 'http://api.globalbioticinteractions.org/';

    private $finalURL;

    public function findPreyForPredator($srcTaxon)
    {
        $constraints['includeObservations'] = false;
        return $this->queryBuilder($srcTaxon, 'eats', $constraints);
    }

    public function findPredatorForPrey($srcTaxon)
    {
        $constraints['includeObservations'] = false;
        return $this->queryBuilder($srcTaxon, 'eatenBy', $constraints);
    }
    // these functions should be disappearing as code is updated to allow for more types of interactions 
    // such as: Mutalists, Commensals, Amensals, Primary Hosts, Secondary Hosts
    public function findObservedPreyForPredator($srcTaxon, $interactionFilters, $locationConstraints, $mimeType)
    {
        $constraints['includeObservations'] = true;
        $constraints['mime'] = ($mimeType == "CSV") ? "csv" : null; # if we ever add support for xml or other mime types probably just always include a "type=someMimeType", but for now only needed for type=csv
        $this->setInteractionFilters($interactionFilters['prey'], $constraints);
        $this->setLocationConstraints($locationConstraints, $constraints);
        return $this->queryBuilder($srcTaxon, 'eats', $constraints);
    }
    // these functions should be disappearing as code is updated to allow for more types of interactions 
    public function findObservedPredatorsForPrey($srcTaxon, $interactionFilters, $locationConstraints, $mimeType)
    {
        $constraints['includeObservations'] = true;
        $constraints['mime'] = ($mimeType == "CSV") ? "csv" : null; # if we ever add support for xml or other mime types probably just always include a "type=someMimeType", but for now only needed for type=csv
        $this->setInteractionFilters($interactionFilters['pred'], $constraints);
        $this->setLocationConstraints($locationConstraints, $constraints);
        return $this->queryBuilder($srcTaxon, 'eatenBy', $constraints);
    }
    // new function to replace the above ones
    public function findObservedTargetForSource($srcTaxon, $interactionFilters, $locationConstraints, $mimeType, $interaction)
    {
        $constraints['includeObservations'] = true;
        $constraints['mime'] = ($mimeType == "CSV") ? "csv" : null; # if we ever add support for xml or other mime types probably just always include a "type=someMimeType", but for now only needed for type=csv
        $this->setInteractionFilters($interactionFilters[$interaction->getTargetTitle()], $constraints);
        $this->setLocationConstraints($locationConstraints, $constraints);
        return $this->queryBuilder($srcTaxon, $interaction->getInteractionTitle(), $constraints);
    }
    public function findCloseTaxonNameMatches($name)
    {
        return $this->query('findCloseMatchesForTaxon', $name, null);
    }

    public function findExternalTaxonURL($taxonName)
    {
        return $this->query('findExternalUrlForTaxon', $taxonName, null);
    }
    public function findSupportedInteractions()
    {
        return $this->query('interactionTypes', null, null);
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
    // each call will pass in the relevant filter one at a time, must do it this way because each findObserved* is treated as a unique rest call and needs a unique URL
    private function setInteractionFilters($interactionFilter, &$constraints)
    {
        if($interactionFilter != false) {
            $constraints['interactionFilter'] = $interactionFilter; // the type of interaction does not matter. The URL will build itself regardless of type(ie pred, prey, parasite... etc)
        }

    }
    #Helper fuction for query. Builds the correct strings and parameters for the query function
    private function queryBuilder($scientificName, $interactionType, $constraints) 
    {
        $operation = $interactionType;
        if(isset($constraints['interactionFilter'])) { #if there is some interaction filter, it should be added after the interactionType
            $operation = $operation . '/' . rawurlencode($constraints['interactionFilter']);
        }
        if ($constraints['includeObservations']) {
            $operation = $operation . '?includeObservations=true';
            if(isset($constraints['nw_lat'])) {
                $operation = $operation . '&nw_lat=' . $constraints['nw_lat'] . '&nw_lng=' . $constraints['nw_lng'] . '&se_lat=' . $constraints['se_lat'] . '&se_lng=' . $constraints['se_lng'];
            }
        }
        if(!empty($constraints['mime'])) {
            $operation = $operation . '&type=' . $constraints['mime'];
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
        $url_prefix = self::URL_PREFIX . $method . '/' . rawurlencode($name);

        if (isset($operation)){
            $url = $url_prefix . '/' . $operation;
        } else {
            $url = $url_prefix;
        }

        $this->finalURL = $url;

        $response = file_get_contents($url);
        $undecodedResponse = $response; # for csv and such
        $arrayResponse = json_decode($response, true); # to return json in array format
        $response = json_decode($response); # to return json in std object format

        if((strpos($operation, 'Observations') !== FALSE) && (strpos($operation, 'csv') === FALSE)) { # Observational query
            return $this->observationalSearchContainerPopulator($response);
        } elseif ((strpos($operation, 'Observations') !== FALSE) && (strpos($operation, 'csv') !== FALSE)) { # Observational query, with non JSON response(csv)
            return $undecodedResponse; // used for anything returned from the rest non JSON encoded
        } elseif ($method == 'findExternalUrlForTaxon') {  # External URL lookup query
            return $response->{'url'};
        } elseif (isset($operation)) { # exhaustive list return
            $columns = $response->{'columns'};
            $i = 0;
            $has_target_taxon_name_column = false;
            foreach ($columns as $column) {
                if ($column == 'target_taxon_name') {
                    $has_target_taxon_name_column = true;
                    break;
                }
                $i+=1;
            }

            $taxonDataList = $response->{'data'};
            $taxonNames = array();
            
            foreach ($taxonDataList as $taxonData)
            if ($has_target_taxon_name_column) {
                foreach ($taxonData[$i] as $taxonName) {
                    $taxonNames[] = $taxonName;
                }
            }
            
            return $taxonNames;
        } elseif ($method == 'findCloseMatchesForTaxon') { # Fuzzy lookup 
            $columns = $response->{'columns'};
            $taxonMatches = $response->{'data'};
            $taxonNames = array();
            foreach ($taxonMatches as $taxonMatch) {
                $taxonNames[] = $taxonMatch[0];
            }
            return $taxonNames;
        } elseif ($method == 'interactionTypes') { # supported interaction types
                $interactions = array();
            foreach ($arrayResponse as $interactionName => $interactionGroup) {
                $interactions[$interactionName] = array('source' => $interactionGroup['source'], 'target' => $interactionGroup['target']);
            }
            return $interactions;
        }
    }
    /*
      FUNCTION: observationalSearchContainerPopulator

      PURPOSE: Helper function that populates the return container for observational queries

      This function takes the response from the rest service and converts it into a more usable format


      NOTE: due to the requirements of this specfic project there should never be a time when the predator_body_part
            or predator_physiological_state is returned to UI. We will remove them from the REST return at this point in
            the code. 

            Case interaction_type == eatenBy -> then target represents predator; do not populate
            Case interaction_type == eats -> then source represents predator; do not populate

            source_taxon_name is what the user search is based on. IE if they type in 'Callinectes sapidus' then this will
            always be the source. The interaction_type will change based on what they want(this is subject to change down the line
            if the REST is optimized).
    */
    private function observationalSearchContainerPopulator($response){

            $columns = $response->{'columns'};
            $dataList = $response->{'data'};
            $container = array();
            $headerPositions = array();
            $interactions = new SupportedInteractions();

            $j = 0;
            foreach ($columns as $headerTitle) {
                switch ($headerTitle) {
                    case 'target_taxon_name':
                        $headerPositions['tax'] = $j; 
                        break;
                    case 'interaction_type':
                        $headerPositions['interactionType'] = $j; 
                        break;
                    case 'latitude':
                        $headerPositions['lat'] = $j;
                        break;
                    case 'longitude':
                        $headerPositions['long'] = $j;
                        break;
                    case 'altitude':
                        $headerPositions['alt'] = $j;
                        break;
                    case 'study_title':
                        $headerPositions['study'] = $j;
                        break;
                    case 'collection_time_in_unix_epoch':
                        $headerPositions['epoch'] = $j;
                        break;

                    case 'tmp_and_unique_source_specimen_id':
                        // note that this id is used to aggregate results.  
                        // for eats interaction, the source specimen id is the predator specimen id.
                        $headerPositions['id'] = $j;
                        break;
                    case 'source_specimen_life_stage':
                        $headerPositions['sourceLS'] = $j;
                        break;
                    case 'target_specimen_life_stage':
                        $headerPositions['targetLS'] = $j;
                        break;
                    case 'source_specimen_body_part':
                        $headerPositions['sourceBP'] = $j;
                        break;
                    case 'target_specimen_body_part':
                        $headerPositions['targetBP'] = $j;
                        break;
                    case 'source_specimen_physiological_state':
                        $headerPositions['sourcePS'] = $j;
                        break;
                    case 'target_specimen_physiological_state':
                        $headerPositions['targetPS'] = $j;
                        break;
                    default:
                        #some new header property
                        break;
                }

                $j+=1;
            }
            if(empty($dataList)){ # if nothng is returned from the rest, dont do anything below this
                return null;
            }
            $interactionType = $dataList[0][$headerPositions['interactionType']];
            $target = $interactions->interactionToTarget($interactionType);
            $source = $interactions->interactionToSource($interactionType);
            # here we change the words target and source to the real represenation of the name IE pred, prey, host.. or whatever
            if(in_array($interactionType, $interactions->getSupportedInteractions())) {
                $headerPositions[$target . 'LS'] = $headerPositions['targetLS'];
                $headerPositions[$source . 'LS'] = $headerPositions['sourceLS'];
                $headerPositions[$target . 'BP'] = $headerPositions['targetBP'];
                $headerPositions[$source . 'BP'] = $headerPositions['sourceBP'];
                $headerPositions[$target . 'PS'] = $headerPositions['targetPS'];
                $headerPositions[$source . 'PS'] = $headerPositions['sourcePS'];
            }else {
                throw new UnsupportedInteractionTypeException('Interaction type ' . $dataList[0][$headerPositions['interactionType']] . ' is not yet supported!');
            }

            $i = 0;
            foreach ($dataList as $taxonData) 
            {
                $container[$i] = array();
                $container[$i][0] = $taxonData[$headerPositions['tax']]; #subjectTaxon
                $container[$i][1] = $taxonData[$headerPositions['lat']]; #latitude
                $container[$i][2] = $taxonData[$headerPositions['long']]; #longitude
                $container[$i][3] = $taxonData[$headerPositions['alt']]; #altitude
                $container[$i][4] = $taxonData[$headerPositions['study']]; #contributor
                $container[$i][5] = $taxonData[$headerPositions['epoch']]; #unix epoch
                $container[$i][6] = $taxonData[$headerPositions['id']]; #tmp_and_unique_specimen_id
                $container[$i][$target . 'LS']  = $taxonData[$headerPositions[$target . 'LS']];
                $container[$i][$source . 'LS'] = $taxonData[$headerPositions[$source . 'LS']];
                $container[$i][$target . 'BP']  = $taxonData[$headerPositions[$target . 'BP']]; 
                $container[$i][$source . 'BP'] = $taxonData[$headerPositions[$source . 'BP']];
                $container[$i][$target . 'PS']  = $taxonData[$headerPositions[$target . 'PS']];
                $container[$i][$source . 'PS'] = $taxonData[$headerPositions[$source . 'PS']]; 


                $i+=1;
            }
            return $container;
    }
    #function only used to test URL in unit tests
    public function getURL()
    {
        return $this->finalURL;
    }

    public function getURLPrefix()
    {
        return self::URL_PREFIX;
    }

}

?>
