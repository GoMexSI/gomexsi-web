<?php
require_once 'Interaction.php';

class InteractionContainer implements Interaction
{
	private $source;
	private $target;

	public function __construct($title)
	{
		$interaction = new SupportedInteractions();
		$this->interactionTitle = $title;
		$this->source = $interaction->interactionToSource($title);
		$this->target = $interaction->interactionToTarget($title);
	}
	public function getSourceTitle() 
	{
		return $this->source;
	}
	public function getTargetTitle()
	{
		return $this->target;
	}
	public function getInteractionTitle()
	{
		return $this->interactionTitle;
	}
}
?>
