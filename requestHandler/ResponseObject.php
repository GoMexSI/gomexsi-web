<?php

/*
		FILE: ResponseObject.php
		Date: Spring 2013
		Author: Mike C

		Purpose: This is the datastructre that will be populated based on what the user queries, converted into a JSON object, and returned to the UI

*/


# CLASS: ResponseObject
#
# PURPOSE: Holds the compleated object that will be converted into a json object, and returned to the UI
#
class ResponseObject{

	// this should be an array of aSubjects
	public $subjects = array(); # one element per unique species. aSubject will hold an array of the specific instance objects
}

# CLASS: basicTraits
#
# PURPOSE: Used to hold basic properties that subjecs, predator and prey all have in common
# 
# one basic trait per subject
class basicTraits{ 
	//public $commonName = array(); // There can be more than one common name for a species. needle fish for example has 2 common names in our db
	public $scientificName;
	//public $kingdom;
	//public $phylum;
	//public $class;
	//public $order;
	//public $family;
	//public $genus;
	//public $species;
}

# CLASS: aSubject
#
# PURPOSE: this is and instance of a singular subject, ResponseObject will have an array of these.
# 		   For example if I seach kingfish in the predator box, there will be one 'aSubject' item in the subject array 
#		   called kingfish(but really it will be Scomberomorus cavalla, the scientfic name is used as the key). 
#		   aSubject will hold an array of instances of the kingfish.
#
class aSubject extends basicTraits{

	public $subjectInstances = array(); # this is an array of each database instance of the subject

	/*
	This an array of the aPredator object, this will be populated if the 'aSubjecct' is catagorized as a prey,
	else it will be an empty list
	*/
}

# CLASS: aSubjectInstance
#
# PURPOSE: For each instance of the specific species subject, there will be one insntace of aSubjectInstance
#		   this will hold all information specific to that instance of the species. IE if the user enters kingfish
#		   the subjects array will contain one elelment of 'aSubject'. This 'aSubject' an array of all of instnaces of kingfish
#		   that were found in the DB. Depending on if the kingfish is being searched as a predator, or prey, aSubjectInstance will
#	       will be populated differntly. This class will hold all data specfic only to that instance.
#
class aSubjectInstance{

	# put all of the instance specific stuff here
/*	size
	weigh
	color
	etc..*/

	/*
	This an array of the aPrey object, this will be populated only if the subject is catagorized as a predator, else will be empty.
	A kingfish instance as a predator will have prey, IE things it ate that were in the specific instance's stomach. For each thing in its
	stomach one element of prey will exist, all the details of that eaten thing will reside in the aPrey object.
	*/
	public $prey = array();

	/*
	This an array of the aPredator object, this will be populated if the subject is catagorized as a prey, else will be empty.
	One or more kingfish instnaces as a prey will exist in the prey table, what fish ate that instance will become an aPredator and 
	will become an element of the predators array. The specific details of the fish that ate our original subjectInstance will be stored in the aPredator object
	inside of the predators arry.
	*/
	public $predators = array(); # this might not actully need to be an array becuase really a single subjectInstance can only be eaten by a single predator
	
	// this is the location that the aSubjectInstance was found, whether that be caught (pole, net, whatever) by human, or location of the fish that ate it was found at.
	// IE catch fish with net, write down location X. OR found fish in stomach of fish caught at location X, write down location X.
	//public $location = array();
}

# CLASS: aPredator
#
# PURPOSE: a single instance of a predator, aSubject will have an array of these
#
class aPredator extends basicTraits{
	// if there is any pred specific traits they should go here
	//size, weight, color, whatever
}

# CLASS: aPrey
#
# PURPOSE: a single instance of a prey, aSubject will have an array of these
#
class aPrey extends basicTraits{
	// any prey specific traits go in here
	//size, weight, color, whatever ** pred and prey will likley have some overlapping traits, and some specfic ones.
}


?>