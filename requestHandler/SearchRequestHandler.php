<?php 

require_once 'RequestParser.php';
require_once 'RequestJSONResponse.php';

class SearchRequestHandler
{
    private $trophicService;
    private $parser;

    public function __construct()
    {

    }
    public function requestHandlerDriver($urlPost)
    {
    	$this->parsePOST($urlPost); 
    	$this->getTrophicService();
        return $this->createJSONResponse();
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

    public function createJSONResponse()
    {
        $jsonConverter = new RequestJSONResponse();
        $searchType = $this->parser->getSearchType();
        $responseObjectContainer = array();

        if ($searchType == 'fuzzySearch') {
            $fuzzyResponseObject = new FuzzyResponseObject();
            $speciesSubject = $this->parser->getFuzzyValue();
            $phpServiceObject = $this->trophicService->findCloseTaxonNameMatches($speciesSubject);
            $jsonConverter->addFuzzySearchResultToResponse($fuzzyResponseObject, $phpServiceObject);
            $fuzzyResponseObject->fuzzyName = $speciesSubject;
            $responseObjectContainer[0] = $fuzzyResponseObject;
        } else {
            $responseObject = new ResponseObject();
            $speciesSubject;
            if ($this->parser->shouldIncludePrey()) {
                $speciesSubject = $this->parser->getPredatorName();
                $phpServiceObject = $this->trophicService->findPreyForPredator($speciesSubject);
                $jsonConverter->addPreyListToResponse($responseObject, $phpServiceObject);
            } 
            if ($this->parser->shouldIncludePredators()) {
                $speciesSubject = $this->parser->getPreyName();
                $phpServiceObject = $this->trophicService->findPredatorForPrey($speciesSubject);
                $jsonConverter->addPredatorListToResponse($responseObject, $phpServiceObject);
            } 
            $responseObject->scientificName = $speciesSubject;
            $responseObjectContainer[0] = $responseObject;
        }
        

        return $jsonConverter->convertToJSONObject($responseObjectContainer);
    }


}
?>