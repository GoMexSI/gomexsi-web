<?php

class UnknownSpeciesClassificationTypeException extends Exception {}

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

    public function addObservationToResponse($responseObject, $serviceObject, $predOrPrey)
    {
        # the word instance inside this function is used to denote a 'prey' or 'predator' instanace depending on what is passed into $predOrPrey. 
        # $predOrPrey will contain the string 'prey' or 'predator'
        $observation = 0;
        $oldID = 0;
        $i = 0; #each element in the instanceList list
        $lastInstance = array(); # used in order to save the last instance

        $latitude = -999; #for first run in loop
        $longitude = -999;
        $altitude = -999;
        $contributor = "ERROR"; # this should never be used as the contributor, but have to define varible
        $unixEpoch = null;

        $instanceList = array();
        foreach ($serviceObject as $theInstance) { # theInstance is a single observation
            $lastInstance = $theInstance; # used to add the last observation after the foreach loop
            if((($oldID - $theInstance[6]) != 0) && (($observation + $i) != 0)) { #new observation, but should not occur the first time in the foreach
                switch ($predOrPrey) { #create correct type of object based on classification of species passed into function
                    case 'prey':
                        $responseObject->preyInstances[$observation] = array("$predOrPrey" => $instanceList, 'date' => $unixEpoch, 'lat' => $latitude, 'long' => $longitude, 'alt' => $altitude, 'ref' => $contributor);
                        break;
                    case 'pred':
                        $responseObject->predInstances[$observation] = array("$predOrPrey" => $instanceList, 'date' => $unixEpoch, 'lat' => $latitude, 'long' => $longitude, 'alt' => $altitude, 'ref' => $contributor);
                        break;
                    default:
                        throw new UnknownSpeciesClassificationTypeException('type [' . $predOrPrey . '] not recognized as valid parameter type');
                        break;
                }
                $observation+=1;
                $i = 0;
                unset($instanceList);
            }

            $instanceName = $theInstance[0];
            $latitude = $theInstance[1];
            $longitude = $theInstance[2];
            $altitude = $theInstance[3];
            $contributor = $theInstance[4];
            $unixEpoch = (!empty($theInstance[5])) ? $theInstance[5] : 'Null Value' ;
            $oldID = $theInstance[6];

            $instanceList[$i] = $instanceName;

            $i+=1; # each iteration in the for each represents a new prey or predator observation
        }
        #add the last instance to the response object
        switch ($predOrPrey) {
            case 'prey':
                $responseObject->preyInstances[$observation] = array("$predOrPrey" => $instanceList, 'date' => $lastInstance[5], 'lat' => $lastInstance[1], 'long' => $lastInstance[2], 'alt' => $lastInstance[3], 'ref' => $lastInstance[4]);
                break;
            case 'pred':
                $responseObject->predatorInstances[$observation] = array("$predOrPrey" => $instanceList, 'date' => $lastInstance[5], 'lat' => $lastInstance[1], 'long' => $lastInstance[2], 'alt' => $lastInstance[3], 'ref' => $lastInstance[4]);
                break;
            default:
                throw new UnknownSpeciesClassificationTypeException('type [' . $predOrPrey . '] not recognized as valid parameter type');
                break;
        }
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