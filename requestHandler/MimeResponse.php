<?php

interface MimeResponse 
{
	public function cleanObject($phpObject);
	
    public function addPreyListToResponse($responseObject, $serviceObject);

    public function addPredatorListToResponse($responseObject, $serviceObject);

    public function addObservationToResponse($responseObject, $serviceObject, $predOrPrey);
}

?>
