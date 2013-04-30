<?php

class RequestJSONResponse
{
	public function convertToJSONObject($phpObject)
	{
		return(json_encode($phpObject));
	}

    public function addPreyListToResponse($responseObject, $serviceObject) {
        $i = 0;
        $preyList = array();
        foreach ($serviceObject as $thePrey) {
            $preyList[$i] = $thePrey;
            $i++;
        }
        $prey = array('prey' => $preyList);

        $responseObject->preyInstances[0] = $prey;
    }

    public function addPredatorListToResponse($responseObject, $serviceObject) {
        $i = 0;
        $predList = array();
        foreach ($serviceObject as $thePred) {
            $predList[$i] = $thePred;
            $i++;
        }
        $pred = array('pred' => $predList);

        $responseObject->predInstances[0] = $pred;
    }

    public function addPreyObservationToResponse($responseObject, $serviceObject)
    {
        $observation = 0;
        $locationCheckSum = 0;
        $oldID = 0;
        $i = 0; #each element in the preyList list
        $lastPrey = array(); # used in order to save the last instnace

        $latitude = -999; #for first run in loop
        $longitude = -999;
        $altitude = -999;
        $contributor = "ERROR"; # this should never be used as the contributor, but have to define varible
        $unixEpoch = null;

        $preyList = array();
        foreach ($serviceObject as $thePrey) { # thePrey is a single observation
            $lastPrey = $thePrey;
            if((($oldID - $thePrey[6]) != 0) && (($observation + $i) != 0)) { #new observation, but should not occur the first time in the foreach
                $prey = array('prey' => $preyList);
                $responseObject->preyInstances[$observation] = array('prey' => $preyList, 'date' => $unixEpoch, 'lat' => $latitude, 'long' => $longitude, 'alt' => $altitude, 'ref' => $contributor);
                $observation+=1;
                $i = 0;
                unset($preyList);
            }

            $preyName = $thePrey[0];
            $latitude = $thePrey[1];
            $longitude = $thePrey[2];
            $altitude = $thePrey[3];
            $contributor = $thePrey[4];
            $unixEpoch = $thePrey[5];
            $oldID = $thePrey[6];

            $preyList[$i] = $preyName;

            $locationCheckSum = $latitude + $longitude + $altitude + $unixEpoch;

            $i+=1; # each iteration in the for each represents a new prey
        }
        #add the last instance to the response object
        $prey = array('prey' => $preyList);
        $responseObject->preyInstances[$observation] = array('prey' => $preyList, 'date' => $lastPrey[5], 'lat' => $lastPrey[1], 'long' => $lastPrey[2], 'alt' => $lastPrey[3], 'ref' => $lastPrey[4]);
    }

    public function addPredatorObservationToResponse($responseObject, $serviceObject)
    {
        #TODO implement this
    }

    public function addFuzzySearchResultToResponse($responseObject, $serviceObject) {
        $i = 0;
        $matchList = array();
        foreach ($serviceObject as $match) {
            $matchList[$i] = $match;
            $i++;
        }
        $responseObject->matches = $matchList;

    }
    public function addTaxonURLLookupToResponse($responseObject, $serviceObject) {
        $responseObject->URL = $serviceObject;
    }
}
/**
* 
*/
class ResponseObject
{
    public $scientificName;
}
class FuzzyResponseObject
{
    public $fuzzyName;
    public $matches = array();
}

?>