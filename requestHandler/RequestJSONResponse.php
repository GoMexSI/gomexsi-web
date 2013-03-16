<?php

class RequestJSONResponse
{
	public function convertToJSONObject($phpObject) # tis late, so I am leaving this for now, will change to actully convery an object thursday or friday
	{
		return(json_encode($phpObject));
	}

	public function populateReturnObject($serviceObject, $searchType, $speciesSubject)
	{
        $responseObject = new ResponseObject();
        $responseObjectContainer = array();

		switch ($searchType) {
            case 'findPreyForPredator':
                $i = 0;
                $preyList = array();
                foreach ($serviceObject as $thePrey) {
                    $preyList[$i] = $thePrey;
                    $i++;
                }
                $prey = array('prey' => $preyList);

                $responseObject->scientificName = $speciesSubject;
                $responseObject->subjectInstances[0] = $prey;

                $responseObjectContainer[0] = $responseObject;
                break;

            case 'findPredatorForPrey':
                $i = 0;
                $predList = array();
                foreach ($serviceObject as $thePred) {
                    $predList[$i] = $thePred;
                    $i++;
                }
                $pred = array('pred' => $predList);

                $responseObject->scientificName = $speciesSubject;
                $responseObject->subjectInstances[0] = $pred;

                $responseObjectContainer[0] = $responseObject;
                break;

            default:
                throw new CorruptSearchTypeParameterException('Search Type [' . $this->searchType . '] not supported, JSON object abandoned');
                break;
        }
		return $responseObjectContainer;
	}
}
/**
* 
*/
class ResponseObject
{
    public $scientificName;
    public $subjectInstances = array();
}

?>