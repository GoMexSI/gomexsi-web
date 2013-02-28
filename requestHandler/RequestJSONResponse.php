<?php


class RequestJSONResponse
{
	public function convertToJSONObject($phpObject)
	{
		return '[
				    {
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
				    }
				]';
	}
}

?>