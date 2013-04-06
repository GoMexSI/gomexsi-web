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
        $rowCount = count($serviceObject);
        $columnCount  = count($serviceObject);
        // do more later

        // use nested for's to convert into reeds format
    }

    public function addPredatorObservationToResponse($responseObject, $serviceObject)
    {
        #TODO also fill in this crapola
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