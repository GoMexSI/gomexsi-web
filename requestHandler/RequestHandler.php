<?php

require_once 'RequestParser.php';
require_once 'RequestJSONResponse.php';
require_once 'service/TrophicServiceFactory.php'; 

$request = $_POST;

$handler = new RequestHandler();
$handler->requestHandlerDriver($request);

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
        $this->creatJSONResponse();
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
    	return $trophicService;
    }
    public function creatJSONResponse()
    {
        $jsonConverter = new RequestJSONResponse();

        switch ($searchType) {
            case 'findPreyForPredator':
                $phpServiceObject = $this->trophicService->findPreyForPredator($this->predatorName);
                break;

            case 'findPredatorForPrey':
                $phpServiceObject = $this->trophicService->findPredatorForPrey($this->preyName);
                break;

            default:
                throw new CorruptSearchTypeParameterException('Search Type [' . $searchType . '] not supported, JSON object abandoned');
                break;
        }

        $jsonString = $jsonConverter->convertToJSONObject($phpServiceObject);
        return $jsonString;
        #send post to Reeds code here
    }
}

?>