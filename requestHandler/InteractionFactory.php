<?php

require_once 'InteractionPreyedUponBy.php';
require_once 'InteractionPreysOn.php';

class UnknownInteractionTypeException extends Exception {}

class InteractionFactory 
{
    public static function createInteractionOfType($type) 
    {
        $interaction;
        switch ($type) {
            case 'preysOn':
                $interaction = new InteractionPreysOn;
                break;
            case 'preyedUponBy':
                $interaction = new InteractionPreyedUponBy;
                break;

            default: 
                throw new UnknownInteractionTypeException('Interaction type [' . $type . '] not supported');
                break;
                
        }
        return $interaction;
    }
}

?>