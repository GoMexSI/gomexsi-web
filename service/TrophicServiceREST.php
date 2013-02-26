<?php

include 'TrophicService.php';

class TrophicServiceREST implements TrophicService {
    public function findPreyForPredator($predatorScientificName) {
        $url_prefix = 'http://46.4.36.142:8080/predator/';
        $url_suffix = '/listPrey';
        $url = $url_prefix . rawurlencode($predatorScientificName) . $url_suffix;
    
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
