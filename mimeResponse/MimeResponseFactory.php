<?php

require_once 'MimeResponseCSV.php';
require_once 'MimeResponseJSON.php';

class UnknownMimeResponseTypeException extends Exception {}

class MimeResponseFactory 
{
    public static function createMimeOfType($type) 
    {
        $mimeResponse;
        switch ($type) {
            case 'JSON':
                $mimeResponse = new MimeResponseJSON;
                break;
            case 'CSV':
                $mimeResponse = new MimeResponseCSV;
                break;

            default: 
                throw new UnknownMimeResponseTypeException('Internet Media Type [' . $type . '] not supported');
                break;
                
        }
        return $mimeResponse;
    }
}

?>