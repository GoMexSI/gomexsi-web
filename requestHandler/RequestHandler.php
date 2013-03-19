<?php

require_once 'RequestParser.php';
require_once 'RequestJSONResponse.php';
require_once '../service/TrophicServiceFactory.php';

/*$request = $_POST;

$handler = new RequestHandler();
$handler->requestHandlerDriver($request);*/

class RequestHandler
{
    private $serviceType; # 'mock', 'REST'
    private $searchType; # 'findPreyForPredator', 'findPredatorForPrey'
    private $trophicService;
    private $predatorName;
    private $preyName;

	public function __construct()
    {

    }
    public function requestHandlerDriver($urlPost)
    {
    	$this->parsePOST($urlPost); #Parse Post
    	$this->getTrophicService();
        return $this->creatJSONResponse();
    }
    public function parsePOST($urlPost)
    {

    	$parser = new RequestParser();

        $parser->parse($urlPost);
        $this->serviceType  = $parser->getServiceType();
        $this->searchType   = $parser->getSearchType();
        $this->predatorName = $parser->getPredatorName();
        $this->preyName     = $parser->getPreyName();

    	
    }
    public function getTrophicService()
    {
    	$serviceFactory = new TrophicServiceFactory();
    	$this->trophicService = $serviceFactory->createServiceOfType($this->serviceType);
    	return $this->trophicService;
    }
    public function creatJSONResponse()
    {
        $jsonConverter = new RequestJSONResponse();
        switch ($this->searchType) {
            case 'findPreyForPredator':
                $phpServiceObject = $this->trophicService->findPreyForPredator($this->predatorName);
                $speciesSubject = $this->predatorName;
                break;

            case 'findPredatorForPrey':
                $phpServiceObject = $this->trophicService->findPredatorForPrey($this->preyName);
                $speciesSubject = $this->preyName;
                break;

            default:
                throw new CorruptSearchTypeParameterException('Search Type [' . $this->searchType . '] not supported, JSON object abandoned');
                break;
        }
        $phpObject = $jsonConverter->populateReturnObject($phpServiceObject, $this->searchType, $speciesSubject);
        $jsonString = $jsonConverter->convertToJSONObject($phpObject);
        echo $jsonString;
        echo $this->searchType;
        return $jsonString;
    }
}

?>