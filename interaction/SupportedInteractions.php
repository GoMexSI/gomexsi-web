<?php
require_once '/../service/TrophicServiceFactory.php';

class NonSupportedInteractionType extends Exception {}

class SupportedInteractions {

	private $interactionObject;
	private $trophicService;
	private $interaactionTitleList;

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
			return $this->interactionObject[$interaction]['target'];
		}else {
			throw new NonSupportedInteractionType('Interaction type ' . $interaction . ' is not yet supported!');
		}
	}

	public function interactionToSource($interaction)
	{
		if(in_array($interaction, $this->interactionTitleList)) {
			if($this->interactionObject[$interaction]['source'] == 'predator'){return 'pred';}
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