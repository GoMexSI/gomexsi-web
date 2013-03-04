<?php

class CorruptSearchTypeParameterException extends Exception {}

class RequestParser
{
	public function parse($toParse)
	{
		return "";
	}
	public function determineSearchType($toParse)
	{
		if(!empty($toParse['predName']) && empty($toParse['preyName'])) {
			return 'findPreyForPredator'; #usecase number one
		} elseif(!empty($toParse['preyName']) && empty($toParse['predName'])) {
			return 'findPredatorForPrey'; #usecase number two
		}else{
			throw new CorruptSearchTypeParameterException('Search Type could not be determined based on parameters given');
		}
	}
}

?>