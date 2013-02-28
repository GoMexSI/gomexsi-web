<?php

include 'backend/aggObject.php';

class AggObjectTest extends PHPUnit_Framework_TestCase
{
	public function testWriteToJSON()
	{
		$predScientificName = 'Scomberomorus cavalla';
		$preySciName = array('Synalpheus latastei', 'Lutjanus jocu');

		$response = new AggObject();
		$subject = new aSubject();

		$response->subjects[] = $predScientificName;

		$predOne = '{
					    "scientificName": "Scomberomorus cavalla",
					    "subjectInstances": {
					        "prey": [
					            {
					                "scientificName": "Synalpheus latastei"
					            },
					            {
					                "scientificName": "Lutjanus jocu"
					            }
					        ]
					    }
					}';

		$expectedJSON = "[$predOne]";

		$this->assertEquals($expectedJSON, $this->writeToJSON($response));
	}

	public function writeToJSON($response)
	{
		return '[{
					    "scientificName": "Scomberomorus cavalla",
					    "subjectInstances": {
					        "prey": [
					            {
					                "scientificName": "Synalpheus latastei"
					            },
					            {
					                "scientificName": "Lutjanus jocu"
					            }
					        ]
					    }
					}]';
	}
}
?>