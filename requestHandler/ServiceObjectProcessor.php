<?php
class UnknownSpeciesClassificationTypeException extends Exception {}

/*Two parallel arrays are used in the class to keep track of and store observation instances.
One array instanceDictionary holds only the tmp_and_unique_specimen_id at its corresponding index. preyInstances/predInstances 
holds all the instance data related to the observation. instanceDictionary is used to index 
preyInstances/predInstances. As the return from the rest service is parsed, instanceDictionary 
is updated, allowing all of the unique observations to be grouped properly in preyInstances/predInstances*/
class ServiceObjectProcessor
{
	public function populateResponseObject($responseObject, $serviceObject, $predOrPrey) 
	{
		$instanceDictionary = array(); #dictionary that holds the tmp_and_unique_specimen_id
		$instanceElement = 0; # element where the tmp_and_unique_specimen_id will be stored
		$instanceList = array();
		$instanceName = "nullName";
		foreach ($serviceObject as $instance) { 
			$foundElement = array_search($instance[6], $instanceDictionary); # $instance[6] is the unique_specimen_id

			if($foundElement === false) { #if tmp_and_unique_specimen_id does not already exist in the list, add to the list
				$instanceName = $instance[0];
	            $latitude     = $instance[1];
	            $longitude    = $instance[2];
	            $altitude     = $instance[3];
	            $contributor  = $instance[4];
	            $unixEpoch    = (!empty($instance[5])) ? $instance[5] : 'Null Value' ;
	            $uniqueID     = $instance[6];

				$instanceList[0] = $instanceName;

				$instanceDictionary[$instanceElement] = $uniqueID;

				switch ($predOrPrey) {
                    case 'prey':
                        $responseObject->preyInstances[$instanceElement] = array("$predOrPrey" => $instanceList, 'date' => $unixEpoch, 'lat' => $latitude, 'long' => $longitude, 'alt' => $altitude, 'ref' => $contributor);
                        break;
                    case 'pred':
                        $responseObject->predInstances[$instanceElement] = array("$predOrPrey" => $instanceList, 'date' => $unixEpoch, 'lat' => $latitude, 'long' => $longitude, 'alt' => $altitude, 'ref' => $contributor);
                        break;
                    default:
                        throw new UnknownSpeciesClassificationTypeException('type [' . $predOrPrey . '] not recognized as valid parameter type');
                    	break;
				}
				$instanceElement++;
				unset($instanceList);
			}else { # if the ID already exists in the instanceDictionary, then just add the instance to the "$predOrPrey" => $instanceList
				$instanceName = $instance[0];
				switch ($predOrPrey) {
					case 'prey':
						array_push($responseObject->preyInstances[$foundElement]["$predOrPrey"], $instanceName);
						break;
					case 'pred':
						array_push($responseObject->predInstances[$foundElement]["$predOrPrey"], $instanceName);
						break;
					default:
                        throw new UnknownSpeciesClassificationTypeException('type [' . $predOrPrey . '] not recognized as valid parameter type');
						break;
				}
			}
		}
	}
}

?>