<?php
require_once 'MimeResponse.php';
require_once __DIR__.'/../requestHandler/ServiceObjectProcessor.php';

class MimeResponseJSON implements MimeResponse 
{
	public function cleanObject($phpObject)
    {
		$JSON = json_encode($phpObject);
        $JSON = str_replace("\\/","/", $JSON); #this line previously had the conditional if($searchType == 'taxonURLLookup'), should not cause any issues with out it
        return $JSON;
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
    public function addObservationToResponse($responseObject, $serviceObject, $interaction)
    {
        $objectProcessor = new ServiceObjectProcessor();
        $objectProcessor->populateResponseObject($responseObject, $serviceObject, $interaction);
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


?>
