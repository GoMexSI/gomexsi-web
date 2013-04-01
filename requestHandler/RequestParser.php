<?php

class CorruptSearchTypeParameterException extends Exception {}

class RequestParser
{
	private $serviceType;
	private $searchType;
	private $subjectName;
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
			$this->subjectName = $toParse['suggestion'];
		} else {
			$this->searchType = 'exactMatch'; 
			$this->subjectName = $toParse['subjectName'];
			$this->subjectName = $toParse['subjectName'];
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
	
	public function getSubjectName()
	{
		return $this->subjectName;
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