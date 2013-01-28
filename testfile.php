<?php

$curl = curl_init();
curl_setopt_array($curl, array(
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_URL => 'http://maps.google.com/maps/api/geocode/json?address='.urlencode("77 mass ave, cambridge, ma 02139").'&sensor=false'
));
$geocode = curl_exec($curl);
curl_close($curl);
// convert from json to usable info
$output= json_decode($geocode);
$lat = $output->results[0]->geometry->location->lat;
$long = $output->results[0]->geometry->location->lng;
echo ($lat.",".$long);