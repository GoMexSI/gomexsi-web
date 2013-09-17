<?php

class CorruptSearchTypeParameterException extends Exception {}
class CorruptServiceTypeParameterException extends Exception {}


class RequestParser
{
	private $serviceType;
	private $searchType;
	private $subjectName;
	private $shouldIncludePrey = false;
	private $shouldIncludePredators = false;
	private $locationConstraints = null;
	private $interactionFilters = null;
	private $responseType;

	public function parse($toParse)
	{
		$this->determineServiceType($toParse);
		$this->determineSearchType($toParse);
		$this->determineResponseType($toParse);
		$this->addInteractionFilters($toParse);
		$this->addLocationConstraints($toParse);
	}
	#someday refactor this to a factory
	private function determineSearchType($toParse)
	{
		if (!empty($toParse['suggestion'])) {
			$this->searchType = 'fuzzySearch';
			$this->subjectName = $toParse['suggestion'];
		} elseif (!empty($toParse['listStyle'])) {
			$this->searchType = 'exactMatch'; 
			$this->subjectName = $toParse['subjectName'];
			$this->shouldIncludePrey = !empty($toParse['findPrey']);
			$this->shouldIncludePredators = !empty($toParse['findPredators']);
			if (!$this->shouldIncludePrey && !$this->shouldIncludePredators) {
				throw new CorruptSearchTypeParameterException('Search Type could not be determined based on parameters given');	
			}
		} elseif (!empty($toParse['deepLinks'])) { # URL lookup 
			$this->searchType = 'taxonURLLookup';
			$this->subjectName = $toParse['deepLinks'];
		} 
		#TODO this is location where interaction type needs to be fixed to remove hardcoding
		else { # default behavior
			$this->searchType = 'exactMatchObservation'; 
			$this->subjectName = $toParse['subjectName'];
			$this->shouldIncludePrey = !empty($toParse['findPrey']);
			$this->shouldIncludePredators = !empty($toParse['findPredators']);
			if (!$this->shouldIncludePrey && !$this->shouldIncludePredators) {
				throw new CorruptSearchTypeParameterException('Search Type could not be determined based on parameters given');	
			}
		}

		return $this->searchType;
	}
	private function determineResponseType($toParse)
	{
			if(!empty($toParse['rawData'])) {
				$this->responseType = "CSV";
			}else {
				$this->responseType = "JSON";
			}
	}
	private function determineServiceType($toParse)
	{
		if(!empty($toParse['serviceType'])){
			if($toParse['serviceType'] == 'rest' || $toParse['serviceType'] == 'REST') {
				$this->serviceType = 'REST';
			} else {
				$this->serviceType = $toParse['serviceType'];
			}

			if(!($this->serviceType == 'REST' || $this->serviceType == 'mock')) {
				throw new CorruptServiceTypeParameterException('Service type given: ' . $toParse['serviceType'] . ' is not known');
			}
		} else {
			throw new CorruptServiceTypeParameterException('No service type was provided! Post must provide service type');
		}
		return $this->serviceType;
	}
	private function addLocationConstraints($toParse)
	{
		$this->locationConstraints['nw_lat'] = (!empty($toParse['boundNorth']))? $toParse['boundNorth'] : false;
		$this->locationConstraints['nw_lng'] = (!empty($toParse['boundWest']))? $toParse['boundWest'] : false;
		$this->locationConstraints['se_lat'] = (!empty($toParse['boundSouth']))? $toParse['boundSouth'] : false;
		$this->locationConstraints['se_lng'] = (!empty($toParse['boundEast']))? $toParse['boundEast'] : false;

		if ($this->locationConstraints['nw_lat'] == false) {
			$this->locationConstraints = null; # locationConstraints is now set to null. used as a condition later in the code
		}
	}
	private function addInteractionFilters($toParse)
	{
		$this->interactionFilters['pred'] = (!empty($toParse['filterPredators']))? $toParse['filterPredators'] : false;
		$this->interactionFilters['prey'] = (!empty($toParse['filterPrey']))? $toParse['filterPrey'] : false;
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
	public function getLocationConstraints()
	{
		return $this->locationConstraints;
	}
	public function getInteractionFilters()
	{
		return $this->interactionFilters;
	}
	public function getResponseType()
	{
		return $this->responseType;
	}
}

?>