<?php
require_once 'MimeResponse.php';


# a reponseObject is what will be sen to the UI, a serviceObject is what is send from the REST service. 
#
# This class just concatinates the csv returns together..
class MimeResponseCSV implements MimeResponse 
{
	public function cleanObject($phpObject)
	{
        $csv = $phpObject[0]->csv;
		return $csv;
	}
    public function addPreyListToResponse($responseObject, $serviceObject)
    {
        $responseObject->csv = (empty($responseObject->csv)) ? $serviceObject : $responseObject->csv . $serviceObject;
    }
    public function addPredatorListToResponse($responseObject, $serviceObject)
    {
        $responseObject->csv = (empty($responseObject->csv)) ? $serviceObject : $responseObject->csv . $serviceObject;
    }
    public function addObservationToResponse($responseObject, $serviceObject, $predOrPrey)
    {
        $responseObject->csv = (empty($responseObject->csv)) ? $serviceObject : $responseObject->csv . preg_replace('/^.+\n/', '', $serviceObject);
    }
}