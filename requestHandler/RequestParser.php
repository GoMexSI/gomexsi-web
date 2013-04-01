<?php

class CorruptSearchTypeParameterException extends Exception {}

class RequestParser
{
	private $serviceType;
	private $searchType;
	private $predatorName;
	private $preyName;
	private $fuzzyValue;
	private $shouldIncludePrey = false;
	private $shouldIncludePredators = false;

	public function parse($toParse)
	{
		$typeValues = array();
		$typeValues['serviceType'] = $this->determineServiceType($toParse);
		$typeValues['searchType'] = $this->determineSearchType($toParse);
		return $typeValues;
	}
	private function determineSearchType($toParse)
	{
		if (!empty($toParse['suggestion'])) {
			$this->searchType = 'fuzzySearch';
			$this->fuzzyValue = $toParse['suggestion'];
		} else {
			$this->searchType = 'exactMatch'; 
			$this->predatorName = $toParse['subjectName'];
			$this->preyName = $toParse['subjectName'];
			$this->shouldIncludePrey = !empty($toParse['findPrey']);
			$this->shouldIncludePredators = !empty($toParse['findPredators']);
			if (!$this->shouldIncludePrey && !$this->shouldIncludePredators) {
				throw new CorruptSearchTypeParameterException('Search Type could not be determined based on parameters given');	
			}
		} 

		return $this->searchType;
	}
	private function determineServiceType($toParse)
	{
		if(!empty($toParse['serviceType'])){
			if($toParse['serviceType'] == 'rest' || $toParse['serviceType'] == 'REST') {
				$this->serviceType = 'REST';
			} else {
				$this->serviceType = $toParse['serviceType'];
			}
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

	public function getFuzzyValue()
	{
		return $this->fuzzyValue;
	}

	public function shouldIncludePrey() 
	{
		return $this->shouldIncludePrey;
	}

	public function shouldIncludePredators() 
	{
		return $this->shouldIncludePredators;
	}
}

?>