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
		$instanceName = "nullName";
		foreach ($serviceObject as $instance) { 
			$foundElement = array_search($instance[6], $instanceDictionary); # $instance[6] is the unique_specimen_id

			if($foundElement === false) { #if tmp_and_unique_specimen_id does not already exist in the list, add to the list
				$instanceName           = $instance[0];
	            $latitude               = $instance[1];
	            $longitude              = $instance[2];
	            $altitude               = $instance[3];
	            $contributor            = $instance[4];
	            $unixEpoch              = (!empty($instance[5])) ? $instance[5] : null;
	            $uniqueID               = $instance[6];
	            $predLifeStage          = (!empty($instance[7]))  ? $instance[7]  : null;
	            $preyLifeStage          = (!empty($instance[8]))  ? $instance[8]  : null;
	            $predBodyPart           = (!empty($instance[9]))  ? $instance[9]  : null;
	            $preyBodyPart           = (!empty($instance[10])) ? $instance[10] : null;
	            $predPhysiologicalState = (!empty($instance[11])) ? $instance[11] : null;
	            $preyPhysiologicalState = (!empty($instance[12])) ? $instance[12] : null;

				$instanceDictionary[$instanceElement] = $uniqueID;
				switch ($predOrPrey) {
                    case 'prey':
                    	$instanceList = array("$predOrPrey" => $instanceName, 'preyLifeStage' => $preyLifeStage, 'preyBodyPart' => $preyBodyPart, 'preyPhysiologicalState' => $preyPhysiologicalState);
                        $responseObject->preyInstances[$instanceElement] = array('preyData' => array($instanceList), 'date' => $unixEpoch, 'lat' => $latitude, 'long' => $longitude, 'alt' => $altitude, 'ref' => $contributor);
                        break;
                    case 'pred':
                    	$instanceList = array("$predOrPrey" => $instanceName, 'predLifeStage' => $predLifeStage, 'predBodyPart' => $predBodyPart, 'predPhysiologicalState' => $predPhysiologicalState);
                        $responseObject->predInstances[$instanceElement] = array('predData' => array($instanceList), 'date' => $unixEpoch, 'lat' => $latitude, 'long' => $longitude, 'alt' => $altitude, 'ref' => $contributor);
                        break;
                    default:
                        throw new UnknownSpeciesClassificationTypeException('type [' . $predOrPrey . '] not recognized as valid parameter type');
                    	break;
				}
				$instanceElement++;
			}else { # if the ID already exists in the instanceDictionary, then just add the instance properties to the [$foundElement][0]
				$instanceName           = $instance[0];
	            $predLifeStage          = (!empty($instance[7]))  ? $instance[7]  : null;
	            $preyLifeStage          = (!empty($instance[8]))  ? $instance[8]  : null;
	            $predBodyPart           = (!empty($instance[9]))  ? $instance[9]  : null;
	            $preyBodyPart           = (!empty($instance[10])) ? $instance[10] : null;
	            $predPhysiologicalState = (!empty($instance[11])) ? $instance[11] : null;
	            $preyPhysiologicalState = (!empty($instance[12])) ? $instance[12] : null;

				switch ($predOrPrey) {
					case 'prey':
                    	$instanceList = array("$predOrPrey" => $instanceName, 'preyLifeStage' => $preyLifeStage, 'preyBodyPart' => $preyBodyPart, 'preyPhysiologicalState' => $preyPhysiologicalState);
						array_push($responseObject->preyInstances[$foundElement]['preyData'], $instanceList);
						break;
					case 'pred':
                    	$instanceList = array("$predOrPrey" => $instanceName, 'predLifeStage' => $predLifeStage, 'predBodyPart' => $predBodyPart, 'predPhysiologicalState' => $predPhysiologicalState);
						array_push($responseObject->predInstances[$foundElement]['predData'], $instanceList);
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