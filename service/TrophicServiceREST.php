<?php
require_once 'TrophicService.php';

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
        return $this->query('findTaxon', $name);
    }
    private function query($method, $name, $operation) {
        $url_prefix = 'http://46.4.36.142:8080/' . $method . '/';

        if (isset($operation)){
            $url = $url_prefix . rawurlencode($name) . '/' . $operation;
        } else {
            $url = $url_prefix . rawurlencode($name);
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
