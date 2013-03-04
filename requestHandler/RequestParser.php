<?php


class RequestParser
{
	public function parse($toParse)
	{
		return "";
	}
	public function determineSearchType($toParse)
	{
		#usecase number one - findPreyForPredator
		if(!empty($toParse['predName']) && empty($toParse['preyName'])){
			return 'findPreyForPredator';
		}
	}
}

?>