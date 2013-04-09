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
        $i = 0; #each element in the preyList list

        $latitude = -999; #for first run in loop
        $longitude = -999;
        $altitude = -999;
        $contributor = "ERROR"; # this should never be used as the contributor, but have to define varible
        $unixEpoch = null;

        $preyList = array();
        foreach ($serviceObject as $thePrey) { # thePrey is a single observation
            #after one round, locationCheckSum will contain an actul value. (($observation + $i) != 0) prevents entering this condition on the first run
            if((($locationCheckSum - ($thePrey[1] + $thePrey[2] + $thePrey[3])) != 0) && (($observation + $i) != 0)) { #new observation, but should not occur the first time in the foreach
                $prey = array('prey' => $preyList); # should work, and not just make referance to old prey referance.. 
                $responseObject->preyInstances[$observation] = array($prey, array('date' => $unixEpoch), array('lat' => $latitude), array('long' => $longitude), array('alt' => $altitude), array('ref' => $contributor));
                $observation+=1;
                $i = 0;
            }

            $preyName = $thePrey[0];
            $latitude = $thePrey[1];
            $longitude = $thePrey[2];
            $altitude = $thePrey[3];
            $contributor = $thePrey[4];
            $unixEpoch = $thePrey[5];

            $preyList[$i] = $preyName;
            $locationCheckSum = $latitude + $longitude + $altitude;

            $i+=1; # each iteration in the for each represents a new prey
        }
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