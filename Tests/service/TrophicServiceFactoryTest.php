<?php

require_once 'service/TrophicServiceFactory.php';

class TrophicServiceFactoryTest extends PHPUnit_Framework_TestCase 
{

    public function testValidTypes() 
    {
        TrophicServiceFactory::createServiceOfType('REST');
        TrophicServiceFactory::createServiceOfType('mock');
    }

    /**
     * @expectedException UnknownTrophicServiceTypeException
     */
    public function testFindPreyForPredatorUnknownType() 
    {
        TrophicServiceFactory::createServiceOfType('thisTypeIsNotSupported');
    }

}

?>
