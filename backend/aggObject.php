<?php

# CLASS: aggregateObject
#
# PURPOSE: Holds the compleated object that will be converted into a json object, and returned to the UI
#
class aggregateObject{

	// this should be an array of aSubjects
	public $subjects = array();
}

# CLASS: basicTraits
#
# PURPOSE: Used to hold basic properties that subjecs, predator and prey all have in common
# 
class basicTraits{
	public $scientificName;
	public $commonName = array(); // There can be more than one common name for a species. needle fish for example has 2 common names in our db
	public $kingdom;
	public $phylum;
	public $class;
	public $order;
	public $family;
	public $genus;
	public $species;
	//$arrayName = array('' => , );

/*	location can be used for subject, predator and prey. 
	1) aSubject->location will be where the subject was collected
	2) aPredator->location will be where the pred ate the subject, 
	3) aPrey->location, the location where the subject was collected, and where it's prey was found in it's stomace are obviously going to be in the same location but...
	   it might be easier/more efficent to acess this propery from within the prey itself then having to chase down its parents location. We can take this out and
	   move the locatoin array to aSubject and aPredator if this end up not being the case.
	
*/
	public $location = array();
}

# CLASS: aSubject
#
# PURPOSE: this is and instance of a singular subject, aggregateObject will have an array of these
# 
class aSubject extends basicTraits{

	// if there is any subject specific traits they shoud go here

	// this an array of the aPredator object
	public $predators = array();
	// this an array of the aPrey object
	public $prey = array();
}

# CLASS: aPredator
#
# PURPOSE: a single instance of a predator, aSubject will have an array of these
#
class aPredator extends basicTraits{
	// if there is any pred specific traits they should go here
}

# CLASS: aPrey
#
# PURPOSE: a single instance of a prey, aSubject will have an array of these
#
class aPrey extends basicTraits{
	// any prey specific traits go in here
}


?>