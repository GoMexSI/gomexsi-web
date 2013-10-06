<?php
class UnknownSpeciesClassificationTypeException extends Exception {}

/*
Used Only for JSON response right now

Two parallel arrays are used in the class to keep track of and store observation instances.
One array instanceDictionary holds only the tmp_and_unique_specimen_id at its corresponding index. preyInstances/predInstances 
holds all the instance data related to the observation. instanceDictionary is used to index 
preyInstances/predInstances. As the return from the rest service is parsed, instanceDictionary 
is updated, allowing all of the unique observations to be grouped properly in preyInstances/predInstances*/
class ServiceObjectProcessor
{
	# $instance is populated in the service based on the header sent not the order of the data. Just
	# using an indexed array here for lack of better object. 
	public function populateResponseObject($responseObject, $serviceObject, $interaction) 
	{
		$instanceDictionary = array(); #dictionary that holds the tmp_and_unique_specimen_id
		$instanceElement = 0; # element where the tmp_and_unique_specimen_id will be stored
		$instanceName = "nullName";
		$interactionHelper = new SupportedInteractions();
        $target = $interactionHelper->interactionToTarget($interaction->getInteractionTitle());
        $source = $interactionHelper->interactionToSource($interaction->getInteractionTitle());

		if (empty($serviceObject)) { # if the service object is empty, dont do anything to the response object.
			return;
		}

		foreach ($serviceObject as $instance) { 
			$foundElement = array_search($instance[6], $instanceDictionary); # $instance[6] is the unique_specimen_id
			#this allows variable names to use noun in place of source/target
			$targetLifeStage 		   = $target . 'LifeStage';
			$sourceLifeStage 		   = $source . 'LifeStage';
			$targetBodyPart			   = $target . 'BodyPart';
			$sourceBodyPart 		   = $source . 'BodyPart';
			$targetPhysiologicalState  = $target . 'PhysiologicalState';
			$sourcePhysiologicalState  = $source . 'PhysiologicalState';
			$targetInstances 		   = $target . 'Instances';

			if($foundElement === false) { #if tmp_and_unique_specimen_id does not already exist in the list, add to the list
				$instanceName           = $instance[0];
	            $latitude               = $instance[1];
	            $longitude              = $instance[2];
	            $altitude               = $instance[3];
	            $contributor            = $instance[4];
	            $unixEpoch              = (!empty($instance[5])) ? $instance[5] : null;
	            $uniqueID               = $instance[6];
	            $$targetLifeStage       = (!empty($instance[$target . 'LS']))  ? $instance[$target . 'LS']  : null; # $$ causes variable to be name whatever the value of the variable is
	            $$sourceLifeStage       = (!empty($instance[$source . 'LS']))  ? $instance[$source . 'LS']  : null;
	            $$targetBodyPart	    = (!empty($instance[$target . 'BP'])) ? $instance[$target . 'BP'] : null;
	            $$sourceBodyPart  		= (!empty($instance[$source . 'BP'])) ? $instance[$source . 'BP'] : null;
	            $$targetPhysiologicalState = (!empty($instance[$target . 'PS'])) ? $instance[$target . 'PS'] : null;
	            $$sourcePhysiologicalState = (!empty($instance[$source . 'PS'])) ? $instance[$source . 'PS'] : null; 

				$instanceDictionary[$instanceElement] = $uniqueID;
/*				switch ($interaction->getTargetTitle()) {
                    case 'prey':
                    	$instanceList = array($interaction->getTargetTitle() => $instanceName, 'preyLifeStage' => $preyLifeStage, 'preyBodyPart' => $preyBodyPart, 'preyPhysiologicalState' => $preyPhysiologicalState);
                        $responseObject->preyInstances[$instanceElement] = array('preyData' => array($instanceList), 'date' => $unixEpoch, 'lat' => $latitude, 'long' => $longitude, 'alt' => $altitude, 'ref' => $contributor);
                        break;
                    case 'pred':
                    	$instanceList = array($interaction->getTargetTitle() => $instanceName, 'predLifeStage' => $predLifeStage);
                        $responseObject->predInstances[$instanceElement] = array('predData' => array($instanceList), 'date' => $unixEpoch, 'lat' => $latitude, 'long' => $longitude, 'alt' => $altitude, 'ref' => $contributor);
                        break;
                    default:
                        throw new UnknownSpeciesClassificationTypeException('type [' . $interaction->getTargetTitle() . '] not recognized as valid parameter type');
                    	break;
				}*/
				# new code
            	$instanceList = array($interaction->getTargetTitle() => $instanceName,
            						  $targetLifeStage => $$targetLifeStage,
            						  $sourceLifeStage => $$sourceLifeStage,
            						  $targetBodyPart => $$targetBodyPart,
            						  $sourceBodyPart => $$sourceBodyPart,
            						  $targetPhysiologicalState => $$targetPhysiologicalState,
            						  $sourcePhysiologicalState => $$sourcePhysiologicalState);
                $responseObject->{$targetInstances}[$instanceElement] = array($interaction->getTargetTitle() . 'Data' => array($instanceList), 'date' => $unixEpoch, 'lat' => $latitude, 'long' => $longitude, 'alt' => $altitude, 'ref' => $contributor);
				$instanceElement++;
			}else { # if the ID already exists in the instanceDictionary, then just add the instance properties to the [$foundElement][0]
/*				$instanceName           = $instance[0];
	            $predLifeStage          = (!empty($instance[7]))  ? $instance[7]  : null;
	            $preyLifeStage          = (!empty($instance[8]))  ? $instance[8]  : null;
	            $preyBodyPart           = (!empty($instance[9])) ? $instance[9] : null;
	            $preyPhysiologicalState = (!empty($instance[10])) ? $instance[10] : null;

				switch ($interaction->getTargetTitle()) {
					case 'prey':
                    	$instanceList = array($interaction->getTargetTitle() => $instanceName, 'preyLifeStage' => $preyLifeStage, 'preyBodyPart' => $preyBodyPart, 'preyPhysiologicalState' => $preyPhysiologicalState);
						array_push($responseObject->preyInstances[$foundElement]['preyData'], $instanceList);
						break;
					case 'pred':
                    	$instanceList = array($interaction->getTargetTitle() => $instanceName, 'predLifeStage' => $predLifeStage);
						array_push($responseObject->predInstances[$foundElement]['predData'], $instanceList);
						break;
					default:
                        throw new UnknownSpeciesClassificationTypeException('type [' . $interaction->getTargetTitle() . '] not recognized as valid parameter type');
						break;
				}*/

				$instanceName           = $instance[0];
				$$targetLifeStage       = (!empty($instance[$target . 'LS']))  ? $instance[$target . 'LS']  : null; # $$ causes variable to be name whatever the value of the variable is
	            $$sourceLifeStage       = (!empty($instance[$source . 'LS']))  ? $instance[$source . 'LS']  : null;
	            $$targetBodyPart	    = (!empty($instance[$target . 'BP'])) ? $instance[$target . 'BP'] : null;
	            $$sourceBodyPart  		= (!empty($instance[$source . 'BP'])) ? $instance[$source . 'BP'] : null;
	            $$targetPhysiologicalState = (!empty($instance[$target . 'PS'])) ? $instance[$target . 'PS'] : null;
	            $$sourcePhysiologicalState = (!empty($instance[$source . 'PS'])) ? $instance[$source . 'PS'] : null;

            	$instanceList = array($interaction->getTargetTitle() => $instanceName,
					  $targetLifeStage => $$targetLifeStage,
					  $sourceLifeStage => $$sourceLifeStage,
					  $targetBodyPart => $$targetBodyPart,
					  $sourceBodyPart => $$sourceBodyPart,
					  $targetPhysiologicalState => $$targetPhysiologicalState,
					  $sourcePhysiologicalState => $$sourcePhysiologicalState);

				array_push($responseObject->{$targetInstances}[$foundElement][$interaction->getTargetTitle() . 'Data'], $instanceList);
			}
		}
	}
}

?>