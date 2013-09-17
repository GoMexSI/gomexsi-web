<?php
require_once 'Interaction.php';

class InteractionPreyedUponBy implements Interaction
{
	public function getSourceTitle() 
	{
		return 'prey';
	}
	public function getTargetTitle()
	{
		return 'pred';
	}
	public function getInteractionTitle()
	{
		return 'preyedUponBy';
	}
}

?>