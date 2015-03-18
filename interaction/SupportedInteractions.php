<?php
require_once __DIR__.'/../service/TrophicServiceFactory.php';

class NonSupportedInteractionType extends Exception {}

class SupportedInteractions {

	private $interactionObject;
	private $trophicService;
	private $interactionTitleList;

	public function __construct()
	{
		$serviceFactory = new TrophicServiceFactory();
    	$this->trophicService = $serviceFactory->createServiceOfType("REST");
    	$this->populateInteractionValues();
	}

	public function interactionToTarget($interaction)
	{
		if(in_array($interaction, $this->interactionTitleList)) {
			if($this->interactionObject[$interaction]['target'] == 'predator'){return 'pred';}
			if($this->interactionObject[$interaction]['target'] == 'consumer'){return 'pred';}
			if($this->interactionObject[$interaction]['target'] == 'food'){return 'prey';}
			return $this->interactionObject[$interaction]['target'];
		}else {
			throw new NonSupportedInteractionType('Interaction type ' . $interaction . ' is not yet supported!');
		}
	}

	public function interactionToSource($interaction)
	{
		if(in_array($interaction, $this->interactionTitleList)) {
			if($this->interactionObject[$interaction]['source'] == 'predator'){return 'pred';}
			if($this->interactionObject[$interaction]['source'] == 'consumer'){return 'pred';}
			if($this->interactionObject[$interaction]['source'] == 'food'){return 'prey';}
			return $this->interactionObject[$interaction]['source'];
		}else {
			throw new NonSupportedInteractionType('Interaction type ' . $interaction . ' is not yet supported!');
		}
	}

	public function getSupportedInteractions()
	{
		return $this->interactionTitleList;
	}
	private function populateInteractionValues()
	{
		$this->interactionObject = $this->trophicService->findSupportedInteractions();
		$this->interactionTitleList = array();

		foreach ($this->interactionObject as $interactionTitle => $interaction) {
			array_push($this->interactionTitleList, $interactionTitle);
		}
	}

}
?>
