<?php


include 'aggObject.php';


#	public class LogicalObjectDirector

#	This class should call the DataBaseInvoker class. It will hold objects that represent an(almost)
#	one to one object to what is in the database. No aggregation will go down in this class.

class LogicalObjectDirector{

	private $dbInvoke;
	private $aggObject;
	public function __construct(){
		echo ": inside LogicalObjectDirector __constructer";
		try
		{
			$this->dbInvoke = new DataBaseInvoker();
		}
		catch(Exception $e)
		{
			echo "Exception caught: " . $e . "\n" ;
		}

		$this->aggObject = new aggregateObject();
	}


	public function standAlonePredator($params) # @todo pick a better, more readable name than params
	{
		$theSubject = new aSubject(); #create a new subject that will be part of aggObject

		if($params['predNameType'] == "scientific") # if the name is scientific
		{
			$theSubject->$scientificName = $params['name'];
			$theSubject->$commonName = $this->dbInvoke->getPredCommonWithSci($commonName);

		}elseif($params['predNameType'] == "common") # else the name is common
		{
			$theSubject->$commonName = $params['name'];
			$theSubject->$scientificName = $this->dbInvoke->getPredSciWithCommon($params['name']);
		}
		else # name must be scientific or common, therfore throw excepton
		{
			throw new Exception("standAlonePredator must have scientific or common name! ERROR thrown in backendComposer");
		}

		//populate the taxonmic level of whatever is given in the orginal query
		$tax_level_name = $this->dbInvoke->getLevel_nameWithSci($theSubject->$scientificName);
		populateTaxLevelHelper($tax_level_name, $theSubject, $theSubject->$scientificName); // this puts the name under the proper biolgical classification variable

		$taxLevelNumber = translateBiologicalClassificationNameToLevelNumber($tax_level_name);
		populateBiologicalClassification($taxLevelNumber, $aSubject);// start the recursive call to populate the rest of the appropriate biological level variables	

		findPredators($theSubject); // pupulate theSubjects list of Predators
		findPrey($theSubject);		// pupulate theSujbects list of Prey
		#....
		# at the very end of this, after theSubject has be fully populated, you should add theSubject to $this->aggObject
		$name = $params['name'];
		$this->aggObject->subjects['name'] = $name; // the key for theSubject will simply be the name the user provided in the orginal query search, be it scienctific or a common name
	}

	# populateBiologicalClassification
	#
	# Each species is recoded in the DB with a Biological Classification, and its level
	# kingdom, phylum, class, order, family, genus or species could be a fun place to use recursion 
	# 0		   1	   2	  3      4		 5        6
	# You can only work backwords, you cannot work forword, IE if you know the kingdom you cannot then learn the phylum
	public function populateBiologicalClassification($level, &$aSubject){ # kickoff recursive functon
	
		if($level > 0)//start recursion
		{
			return stepDownTheBiologicalLine($level, $aSubject, $aSubject->$scientificName);
		}
		//else we are done, all of the classification names(that can be) have been filled in
		return true;
	}
	public function stepDownTheBiologicalLine($level, &$aSubject, $currentName) # recursive funtion, pass the subject in by referance
	{
		if($level > 0)//continue recursion
		{
			// do work in here
			$parentName = $this->dbInvoke->getNextHigerLevelTaxonomicName($currentName); # gets the next level up's name IE if you pass in a name at the species level you now have the name at the Genus level
			$tax_level_name = $this->dbInvoke->getLevel_nameWithSci($parentName); # you now know what biological level you have
			$taxLevelNumber = translateBiologicalClassificationNameToLevelNumber($tax_level_name); # translate the level name to a level number
			populateTaxLevelHelper($tax_level_name, $aSubject, $parentName); // save the new parent name into its respective variables
			stepDownTheBiologicalLine($taxLevelNumber, $aSubject, $parentName); //parent name now will become the current name
		}//else we are done
		return true;
	}
	public function populateTaxLevelHelper($taxLevelName, &$theSubject, $currentName) // this simply takes a tax_level and a subject, and saves the name under the appropriate variable
	{
		switch ($taxLevelName) {							
			case 'Kingdom':						
				$theSubject->kingdom = $currentName;
				break;
			case 'Phylum':
				$theSubject->phylum = $currentName;
				break;
			case 'Class':
				$theSubject->class = $currentName;
				break;
			case 'Order':
				$theSubject->order = $currentName;
				break;
			case 'Family':
				$theSubject->family = $currentName;
				break;
			case 'Genus':
				$theSubject->genus = $currentName;
				break;
			case 'Species':
				$theSubject->species = $currentName;
				break;
			default:
				throw new Exception("populateTaxLevelHelper has received an invalid tax_level. Thrown in backendComposer");
				break;
		}
	}

	# Simple functon to translate string name into level name, normally I would not want
	# to have a function call inside of a recursive funion(adds to much crapola to the stack potentially)
	# but, since this will really only at a max only go 6 level deep, its not a bid deal.
	public function translateBiologicalClassificationNameToLevelNumber($levelName){ 
		switch ($levelName) {							
			case 'Kingdom':						
				return 0;
				break;
			case 'Phylum':
				return 1;
				break;
			case 'Class':
				return 2;
				break;
			case 'Order':
				return 3;
				break;
			case 'Family':
				return 4;
				break;
			case 'Genus':
				return 5;
				break;
			case 'Species':
				return 6;
				break;
			default:
				throw new Exception("translateBiologicalClassificationNameToLevelNumber has received an invalid Biological classification. Thrown in backendComposer");
				break;
		}
		return $level;
	}
	public function standAlonePrey($params)
	{

	}

	// this function will populate the predarors array inside of the subject
	public function findPredators(&$subjec) # subject is a single instance of aSubject
	{
		## SOME OF THIS LOGIC IS GOING TO LIKLEY CHANGE AFTER REVIEWING THE DB
		$listOfPredators; # this will containe a list with the scientific name of each predator of the subjec, other function calls will be made to populate the rest of the traits
		$listOfPredators = $this->dbInvoke->getListOfSubjectsPred($subjec->scientificName);

		while($predNameArray = pg_fetch_array($listOfPredators, NULL, PGSQL_ASSOC))
		{
			# populate list of predators, will waite untill I can see the DB to dig to deep, its it makes more sense to pull more stuff out fo the db with a single call some 
			# functionality will need to change
		}

		//	later down the line, all the basic traits of the predators should be filled in via some fution calls, this should probably happen inside this function that way
		//	a fully populated subject is returned. The same functions can probably be used to populate the prey

	}
	// this function will populate the prey array inside of the subject
	public function findPrey(&$subjec)
	{

	}
}

#	publis class DataBaseInvoker

#	This call will act as an interface between the database and the Locgical object class. It was have 
#	all of the sql calls in it. And return objects that represent objects in the database to the loicalObject class

class DataBaseInvoker{

	public $dbconnect;

	/*
		@todo Need to chagne this, need to remove sensitive information from the opensource code base
	*/
	public function __construct()
	{
		$this->dbconnect = pg_connect('host=owl1.tamucc.edu port=5432 dbname=gomexsi user=gomexsi password=6870Geek');
		if(!$this->dbconnect){
			throw new Exception("Unable to connect");
			exit;
		}
	}


	public function getPredatorByScientific($name) # OLD NOT USED ANY MORE
	{
		$result = pg_query($this->dbconnect, "select * from dy.predator where CAST (sci_name as integer) = (select tax_id from dy.taxonomy where sci_name = '$name')");
		/*$result = pg_query(dbconnect, "select * from dy.predator");*/
		if(!$result){
			echo "<h2>Sorry your DB query did not return anything, try again</h2>";
			return NULL;
		}
		return $result;
	}
	// pass in a scientific(tax_name) name, and you get out a level_name
	public function getLevel_nameWithSci($scientificName)
	{
		$result = pg_query($this->dbconnect, "select level_name from dy.taxonomy where tax_name = '$scientificName'");
		if(!$result){
			throw new Excepton("getLevel_nameWithSci Failed!, ERROR in backendComposer");
		return NULL;
		}else
		{
			$tax_level_name = pg_fetch_array($result, NULL, PGSQL_ASSOC);
		}
		return $tax_level_name;
	}
	public function getListOfSubjectsPred($scientificName)
	{

	}

	#	FUNCTION: getPredSciWithCommon($commonName) This needs to be exapnded to work with both prey and pred or include some more stuff
	#												it makes not sense to have two things that do the exact same thing for pred and prey
	#	PURPOSE: If you have a common name and you would like a scientific name, this function returns a single scientific name
	#
	public function getPredSciWithCommon($commonName)
	{
		$result = pg_query($this->dbconnect, "Select sci_name from dy.taxonomy where tax_id = (Select tax_id from dy.alternate_name where alt_name = '$commonName')");
		if(!$result){
			throw new Excepton("getPredSciWithCommon did not return anything, ERROR in backendComposer"); // I think the string "NULL" should be returned from the database, not nothing
		}																							      // if no scientific name exisits. If not, I am wronge, and you should fix this..
		else
		{
			$scientificName = pg_fetch_array($result, NULL, PGSQL_ASSOC);
			if($scientificName[sci_name]=='NULL'){
				return 'No Scientific Name';
			}
			else
			{
				return $scientificName[sci_name];
			}
		}
	}
	#	FUNCTION: getPredCommonWithSci($scientificName)
	#
	#	PURPOSE: If you hace a Scientific name and you would like the common names associated with the sci name. This function returns an array of common names
	#
	public function getPredCommonWithSci($scientificName)
	{
		$result = pg_connect($this->dbconnect, "Select alt_name from dy.alternate_name where tax_id = (Select tax_id from dy.taxonomy where sci_name ='$scientificName')");
		if(!$result){
			throw new Excepton("getPredCommonWithSci did not return anything, ERROR in backendComposer"); // I think the string "NULL" should be returned from the database, not nothing
		}																							      // if no scientific name exisits. If not, I am wronge, and you should fix this..
		else
		{
			$commonName = pg_fetch_array($result, NULL, PGSQL_ASSOC);
			return $commonName[alt_name]; // there can be multiple common names for a species, so this could be an array 
		}
	}
	# FUNCTION: getNextHigerLevelTaxonomicName($scientificName)
	#
	# PURPOSE: using a scientific name to get the next level up's tax name, IE kingfish is a species, This would return the Genus name for kingfish (although it must be a scientific name passed in)
	#
	public function getNextHigerLevelTaxonomicName($scientificName)
	{
		// sql call: select tax_name from dy.taxonomy where tax_id = (select parent_id from dy.taxonomy where tax_name = '$scientificName'); # or something like that
	}
}

?>