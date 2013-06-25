<?php

require_once 'ServiceObjectProcessor.php';

class RequestJSONResponse
{
	public function convertToJSONObject($phpObject)
    {
		return(json_encode($phpObject));
	}

    public function addPreyListToResponse($responseObject, $serviceObject)
    {
        $i = 0;
        $preyList = array();
        foreach ($serviceObject as $thePrey) {
            $preyList[$i] = $thePrey;
            $i++;
        }
        $prey = array('prey' => $preyList);

        $responseObject->preyInstances[0] = $prey;
    }

    public function addPredatorListToResponse($responseObject, $serviceObject)
    {
        $i = 0;
        $predList = array();
        foreach ($serviceObject as $thePred) {
            $predList[$i] = $thePred;
            $i++;
        }
        $pred = array('pred' => $predList);

        $responseObject->predInstances[0] = $pred;
    }
    public function addObservationToResponse($responseObject, $serviceObject, $predOrPrey)
    {
        $objectProcessor = new ServiceObjectProcessor();
        $objectProcessor->populateResponseObject($responseObject, $serviceObject, $predOrPrey);
    }
    
    public function addFuzzySearchResultToResponse($responseObject, $serviceObject)
    {
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