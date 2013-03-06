<?php

class CorruptSearchTypeParameterException extends Exception {}

class RequestParser
{
	private $serviceType;
	private $searchType;
	private $predatorName;
	private $preyName;

	public function parse($toParse)
	{
		$this->determineServiceType($toParse);
		$this->determineSearchType($toParse);
		return "";
	}
	public function determineSearchType($toParse)
	{
		if(!empty($toParse['predName']) && empty($toParse['preyName'])) {
			$this->searchType = 'findPreyForPredator'; #usecase number one
			$this->predatorName = $toParse['predName'];
		} elseif(!empty($toParse['preyName']) && empty($toParse['predName'])) {
			$this->searchType = 'findPredatorForPrey'; #usecase number two
			$this->preyName = $toParse['preyName'];
		}else{
			throw new CorruptSearchTypeParameterException('Search Type could not be determined based on parameters given');
		}

		return $this->searchType;
	}
	public function determineServiceType($toParse)
	{
		if(!empty($toParse['serviceType'])){
			$this->serviceType = $toParse['serviceType'];
		}else{
			$this->serviceType = 'live';
		}

		return $this->serviceType;
	}

	/*Place mutators below*/

	public function getServiceType()
	{
		return $this->serviceType;
	}
	public function getSearchType()
	{
		return $this->searchType;
	}
	public function getPredatorName()
	{
		return $this->predatorName;
	}
	public function getPreyName()
	{
		return $this->preyName;
	}
}

?>