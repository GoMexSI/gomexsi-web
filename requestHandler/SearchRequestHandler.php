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
        } elseif ($searchType == 'exactMatchObservation') {
            $responseObject = new ResponseObject();
            
            if ($this->parser->shouldIncludePrey()) {
                $phpServiceObject = $this->trophicService->findObservedPreyForPredator($speciesSubject, null); # null will include option for other taxon later
                $jsonConverter->addPreyObservationToResponse($responseObject, $phpServiceObject);
            } 
            if ($this->parser->shouldIncludePredators()) { # not fully implemented yet
                $phpServiceObject = $this->trophicService->findObservedPredatorForPrey($speciesSubject, null);
                $jsonConverter->addPredatorObservationToResponse($responseObject, $phpServiceObject);
            }
            $responseObject->scientificName = $speciesSubject;
            $responseObjectContainer[0] = $responseObject;
        } elseif ($searchType == 'taxonURLLookup') {
            $responseObject = new ResponseObject();
            
            $phpServiceObject = $this->trophicService->findExternalTaxonURL($speciesSubject);
            $jsonConverter->addTaxonURLLookupToResponse($responseObject, $phpServiceObject);
            $responseObject->scientificName = $speciesSubject;
            $responseObjectContainer = $responseObject;
        }else {
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
        
        $JSON = $jsonConverter->convertToJSONObject($responseObjectContainer);

        if($searchType == 'taxonURLLookup') {
            $JSON = str_replace("\\/","/", $JSON); 
        }

        return $JSON;
    }


}
?>