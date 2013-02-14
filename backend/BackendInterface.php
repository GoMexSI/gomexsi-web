<?php

include 'backendComposer.php';

#	BackendInterface.php
#	
#	Purpose: The font end will send a post to this file, with all of the url parameters in it. This file will do validation
#			 checks on the parameters and format of the parameters and then send them to other functions and classes eventully
#			 resulting in the returned sql query and JSON objet.
#
#	Terminology: 'query parameters' basically something that the user seaches for, IE kingfish would be a predator name query parameter
#				 'refinement parameters' if the user added in something like size limitations, also the style of name(scienctfic or common) are called refinement paramerts in this code 
#
# 	Author: Michael Casavecchia
#	Date: spring 2013


	$predName = $_POST['pred'];
	$predNameType = $_POST['predNameStyle']; // scienctific or common
	$preyName = $_POSt['prey'];
	$preyNameType = $_POST['preyNameStyle'];

	$cleanString = array(); // basically this is used to make sure there are no NULL valed be passed into the backend

	if(!empty($predName))
	{
		$cleanString['predName'] = $predName;
	}
	if(!empty($predNameType))
	{
		$cleanString['predNameType'] = $predNameType;
	}
	if(!empty($preyName))
	{
		$cleanString['preyName'] = $preyName;
	}
	if(!empty($preyNameType))
	{
		$cleanString['preyNameType'] = $preyNameType;
	}

	validateURL($cleanString);


	#	FUNCTION: validateURL
	#
	#	PURPOSE: This function will take the associative arry and all of its filled out parameters, make sure they are in the proper format
	#			 and set some flags that will be used to sort out the exact query that needs to take place
	function validateURL($cleanString){

		$predNameFlag = false;
		//$predNameTypeFlag = "common"; // refinment
		$preyNameFlag = false;
		//$preyNameTypeFlag = "common"; // refinment


		//set all of the flags, used to make sure URL was sent in correct format, as well as provide flags to determine query type
		while (current($cleanString))
		{
			if (key($cleanString) == 'predName')
			{
				$predNameFlag = True;
				next($cleanString);

				if(key($cleanString) != 'predNameType') #the next value in the string must be the name type, if it's not thrown an exception
				{
					throw new Exception("The URL string is missing a name type! ERROR thrown in backendInterface");
				}
				//$predNameTypeFlag = current($cleanString);
				next($cleanString);
			}
			elseif (key($cleanString) == 'preyName')
			{
				$preyNameFlag = True;
				next($cleanString);

				if (key($URLvalue) != 'preyNameType') #the next value in the string must be the name type, if it's not thrown an exception
				{
					throw new Exception("The URL string is missing a name type! ERROR thrown in backendInterface");
				}
				//$preyNameTypeFlag = current($cleanString);
				next($cleanString);
			}
			else # as new options and functionality are added this must be explanded. But for now there are all that we have
			{
				throw new Exception("The URL string contains an unknown key! ERROR thrown in backendInterface");
			}
		} // end clean string while
		echo "I am in validateURL";
		# this will hold all of the flags, evetully there will be many more, as of now there are only a few
		$flagArray = array('predNameFlag' => $predNameFlag, 'preyNameFlag' => $preyNameFlag);
		determineQueryType($cleanString, $flagArray);
	}

	#	FUNCTION: determineQueryType
	#
	#	PURPOSE: point the parameters to the correct query
	#
	function determineQueryType(){
	echo ": Inside determineQuery";
		// create a new logicalObject
		$logicalObject = new LogicalObjectDirector();
		
		// exhaustive combination of query options
		if($predNameFlag && !$preyNameFlag) // predator name only
		{
			echo "predator name only";
			$params = array('name' => $cleanString['predName'], 'nameType' => $cleanString['predNameType']);
			$logicalObject->standAlonePredator($params);

		}
		elseif($preyNameFlag && !$predNameFlag) // prey name only
		{
			echo "prey name only";
			$params = array('name' => $cleanString['preyName'], 'nameType' => $cleanString['preyNameType']);
			$logicalObject->standAlonePrey($params);
		}
		elseif($preyNameFlag && $predNameFlag) // both pred and prey
		{
			echo "both pred and prey";
		}else # else nothing.. this should throw an exception. There must always be at least one query parameter filled in.. As of now theses are the only 'query parameters' we are dealing with, more will be added later
		{
			
			throw new Exception("corrupt URL string parameters, must include vaid query parameter! ERROR thrown in backendInterface");
		}
	}
?>