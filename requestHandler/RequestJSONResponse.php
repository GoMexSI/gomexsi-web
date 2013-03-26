<?php

class RequestJSONResponse
{
	public function convertToJSONObject($phpObject)
	{
		return(json_encode($phpObject));
	}

	public function populateReturnObject($serviceObject, $searchType, $speciesSubject)
	{
        $responseObject = new ResponseObject();
        $fuzzyResponseObject = new FuzzyResponseObject();
        $responseObjectContainer = array();

		switch ($searchType) {
            case 'fuzzySearch':
                $i = 0;
                $matchList = array();
                foreach ($serviceObject as $match) {
                    $matchList[$i] = $match;
                    $i++;
                }
                $fuzzyResponseObject->fuzzyName = $speciesSubject;
                $fuzzyResponseObject->matches = $matchList;

                $responseObjectContainer[0] = $fuzzyResponseObject;
                break;
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
class FuzzyResponseObject
{
    public $fuzzyName;
    public $matches = array();
}

?>