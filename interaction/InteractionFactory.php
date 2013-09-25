<?php


require_once 'InteractionContainer.php';

class UnknownInteractionTypeException extends Exception {}

class InteractionFactory 
{
    public static function createInteractionOfType($type) 
    {
        return new InteractionContainer($type);
    }
}

?>