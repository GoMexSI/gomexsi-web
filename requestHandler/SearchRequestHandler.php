<?php 

require_once 'RequestParser.php';
// see http://stackoverflow.com/questions/2253625/php-require-once-not-working-the-way-i-want-it-to-relative-path-issue#2253685
require_once __DIR__.'/../mimeResponse/MimeResponseFactory.php';
require_once __DIR__.'/../interaction/InteractionFactory.php';

class MissingInteractionTypeException extends Exception {}

class SearchRequestHandler
{
    private $trophicService;
    private $parser;
    private $mimeResponse;
    private $interactionLisit = array();

    public function __construct()
    {

    }
    public function requestHandlerDriver($urlPost)
    {
    	$this->parsePOST($urlPost); 
    	$this->getTrophicService();
        return $this->createMimeResponse(); #mime is a reference to 'Internet media type'
    }
    public function parsePOST($urlPost)
    {
    	$this->parser = new RequestParser();
        $this->parser->parse($urlPost);
    }

    public function getTrophicService()
    {
    	$serviceFactory = new TrophicServiceFactory();
    	$this->trophicService = $serviceFactory->createServiceOfType($this->parser->getServiceType());
    	return $this->trophicService;
    }
    // this fucntion will use all of the properties from the RequestParser to populate
    // interactions.
    public function createInteractionsList()
    {
        $interactionFactory = new InteractionFactory();
        $interactionExists = false;
        if($this->parser->shouldIncludePrey()) {
            array_push($this->interactionLisit, $interactionFactory->createInteractionOfType('eats'));
            $interactionExists = true;
        }
        if($this->parser->shouldIncludePredators()) {
            array_push($this->interactionLisit, $interactionFactory->createInteractionOfType('eatenBy'));
            $interactionExists = true;
        }
        if(!$interactionExists) {
            throw new MissingInteractionTypeException('There must be an interaction given! RequestParser must define interaction');
        }
    }
    public function getMimeResponse()
    {
        $responseFactory = new MimeResponseFactory();
        $this->mimeResponse = $responseFactory->createMimeOfType($this->parser->getResponseType());
        return $this->mimeResponse;
    }
    public function createMimeResponse()
    {
        $responseFactory = new MimeResponseFactory();
        $this->mimeResponse = $responseFactory->createMimeOfType($this->parser->getResponseType());

        $searchType = $this->parser->getSearchType();
        $responseObjectContainer = array();

        $speciesSubject = $this->parser->getSubjectName();

        if ($searchType == 'fuzzySearch') {
            $fuzzyResponseObject = new FuzzyResponseObject();
            $phpServiceObject = $this->trophicService->findCloseTaxonNameMatches($speciesSubject);
            $this->mimeResponse->addFuzzySearchResultToResponse($fuzzyResponseObject, $phpServiceObject);
            $fuzzyResponseObject->fuzzyName = $speciesSubject;
            $responseObjectContainer[0] = $fuzzyResponseObject;
        } elseif ($searchType == 'exactMatchObservation') {
            $responseObject = new ResponseObject();
            $this->createInteractionsList();
            
            foreach ($this->interactionLisit as $interaction) {
                $phpServiceObject = $this->trophicService->findObservedTargetForSource($speciesSubject, $this->parser->getInteractionFilters(), $this->parser->getLocationConstraints(),$this->parser->getResponseType(), $interaction);
                $this->mimeResponse->addObservationToResponse($responseObject, $phpServiceObject, $interaction);
            }
            $responseObject->scientificName = $speciesSubject;
            $responseObjectContainer[0] = $responseObject;

        } elseif ($searchType == 'taxonURLLookup') {
            $responseObject = new ResponseObject();
            
            $phpServiceObject = $this->trophicService->findExternalTaxonURL($speciesSubject);
            $this->mimeResponse->addTaxonURLLookupToResponse($responseObject, $phpServiceObject);
            $responseObject->scientificName = $speciesSubject;
            $responseObjectContainer = $responseObject;
        }else {
            $responseObject = new ResponseObject();
            
            if ($this->parser->shouldIncludePrey()) {
                $phpServiceObject = $this->trophicService->findPreyForPredator($speciesSubject);
                $this->mimeResponse->addPreyListToResponse($responseObject, $phpServiceObject);
            } 
            if ($this->parser->shouldIncludePredators()) {
                $phpServiceObject = $this->trophicService->findPredatorForPrey($speciesSubject);
                $this->mimeResponse->addPredatorListToResponse($responseObject, $phpServiceObject);
            } 
            $responseObject->scientificName = $speciesSubject;
            $responseObjectContainer[0] = $responseObject;
        }
        
        $mimeResponseFinalObject = $this->mimeResponse->cleanObject($responseObjectContainer);

        return $mimeResponseFinalObject;
    }


}
class ResponseObject
{
    public $scientificName;
}
class FuzzyResponseObject
{
    public $fuzzyName;
    public $matches = array();
}
?>
