<?php
require_once 'Interaction.php';

class InteractionPreysOn implements Interaction
{
	public function getSourceTitle() 
	{
		return 'pred';
	}
	public function getTargetTitle()
	{
		return 'prey';
	}
	public function getInteractionTitle()
	{
		return 'preysOn';
	}
}

?>