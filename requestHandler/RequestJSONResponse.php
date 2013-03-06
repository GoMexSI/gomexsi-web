<?php


class RequestJSONResponse
{
	public function convertToJSONObject($phpObject) # tis late, so I am leaving this for now, will change to actully convery an object thursday or friday
	{
		//return(json_encode($phpObject));
		return '[{"scientificName": "Scomberomorus cavalla", "subjectInstances": {"prey": [{"scientificName": "Synalpheus latastei"}, {"scientificName": "Lutjanus jocu"}]}}]';
	}
}

?>