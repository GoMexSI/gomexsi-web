<?php

class NonSupportedInteractionType extends Exception {}

class SupportedInteractions {
	// at some point here soon, need to get list of GloBI supported interactions from the rest service. 
	// Cross this list with php supported interactions for usable supported interaction list.

	public function interactionToTarget($interaction)
	{
		switch ($interaction) {
			case 'PreyedUponBy':
					return 'pred'
				break;
			case 'PreysOn':
					return 'prey'
				break;
			default:
				throw new NonSupportedInteractionType('Interaction type ' . $interaction . ' is not yet supported!');
				break;
		}
	}

	public function interactionToSource($interaction)
	{
		switch ($interaction) {
			case 'PreyedUponBy':
					return 'prey'
				break; 
			case 'PreysOn':
					return 'pred'
				break;
			default:
				throw new NonSupportedInteractionType('Interaction type ' . $interaction . ' is not yet supported!');
				break;
		}
	}
}
?>