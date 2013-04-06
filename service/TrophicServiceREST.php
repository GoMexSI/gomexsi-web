<?php
require_once 'TrophicService.php';

class NotImplementedException extends Exception {}

class TrophicServiceREST implements TrophicService 
{
    public function findPreyForPredator($predatorScientificName) 
    {
        return $this->query('predator', $predatorScientificName, 'listPrey');
    }

    public function findPredatorForPrey($preyScientificName)
    {
        return $this->query('prey', $preyScientificName, 'listPredators');
    }
    public function findCloseTaxonNameMatches($name)
    {
        return $this->query('findTaxon', $name, null);
    }
    public function findObservedPreyForPredator($predatorTaxon, $preyTaxon)
    {
        throw new NotImplementedException('REST findObservedPreyForPredator not implemented');
    }
    public function findObservedPredatorForPrey($predatorTaxon, $preyTaxon)
    {
        throw new NotImplementedException('REST findObservedPredatorForPrey not implemented');
    }
    private function query($method, $name, $operation) {
        $url_prefix = 'http://46.4.36.142:8080/' . $method . '/' . rawurlencode($name);

        if (isset($operation)){
            $url = $url_prefix . '/' . $operation;
        } else {
            $url = $url_prefix;
        }
        
        $response = file_get_contents($url);
        $response = json_decode($response);
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
?>
