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

        $speciesSubject = $this->parser->getSubjectName();

        if ($searchType == 'fuzzySearch') {
            $fuzzyResponseObject = new FuzzyResponseObject();
            $phpServiceObject = $this->trophicService->findCloseTaxonNameMatches($speciesSubject);
            $jsonConverter->addFuzzySearchResultToResponse($fuzzyResponseObject, $phpServiceObject);
            $fuzzyResponseObject->fuzzyName = $speciesSubject;
            $responseObjectContainer[0] = $fuzzyResponseObject;
        } else {
            $responseObject = new ResponseObject();
            
            if ($this->parser->shouldIncludePrey()) {
                $phpServiceObject = $this->trophicService->findPreyForPredator($speciesSubject);
                $jsonConverter->addPreyListToResponse($responseObject, $phpServiceObject);
            } 
            if ($this->parser->shouldIncludePredators()) {
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