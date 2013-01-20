<?php

class preferences{
	public static function editPreferences($userid, $request){
		require_once("db.php");
		
		// Pretranslate data for storage
		$gsize = ($request['number']==2 ? 0 : 1);
		
		if ($request['address']!=""){
			$latlng = preferences::addressToLatLng($request['address'], $request['city'],
					$request['state'], $request['zipcode']);
			$distance = $request['distance'];
		}else{
			$latlng = array('lat'=>0,'lng'=>0);
			$distance = 0; //If the user doesn't provide address, distance is disabled
		}
		
		// Update row contents in preferences table
		$query1 = "UPDATE preferences SET address='".$request['address']."', city='".
				$request['city']."', state='".$request['state']."', zipcode=".
				$request['zipcode'].", distance=".$distance.", groupsize=".
				$gsize.", gender='".strtolower($request['gender'])."', price_min=".
				$request['pricemin'].", price_max=".$request['pricemax'].", latitude=".
				$latlng['lat'].", longitude=".$latlng['lng']." WHERE user_id=".
				$userid;
		$result1 = mysql_query($query1, $db) or die(mysql_error());;
		
		// Update row contents in foodtype table
		$type = $request['type'];
		$query2 = "UPDATE foodtype SET cuisine_1=".$type['cuisine_1'].", cuisine_2=".
				$type['cuisine_2'].", cuisine_3=".$type['cuisine_3'].", cuisine_4=".
				$type['cuisine_4'].", cuisine_5=".$type['cuisine_5'].", cuisine_6=".
				$type['cuisine_6'].", cuisine_7=".$type['cuisine_7'].", cuisine_8=".
				$type['cuisine_8'].", cuisine_9=".$type['cuisine_9'].", cuisine_10=".
				$type['cuisine_10'].", cuisine_11=".$type['cuisine_11'].", cuisine_12=".
				$type['cuisine_12']." WHERE user_id=".$userid;
		$result2 = mysql_query($query2, $db) or die(mysql_error());;
		
	}
	
	public static function getPreferences($userid){
		
	}
	
	public static function addressToLatLng($address, $city, $state, $zipcode){
		$comp_address = $address.",".$city.",".$state." ".$zipcode;
		
		// connect with Google and grab the information
		$geocode=file_get_contents('http://maps.google.com/maps/api/geocode/json?address='.urlencode($comp_address).'&sensor=false');
		
		// convert from json to usable info
		$output= json_decode($geocode);
		$lat = $output->results[0]->geometry->location->lat;
		$long = $output->results[0]->geometry->location->lng;
		
		// return just the latitude and longitude
		return array('lat'=>$lat,'lng'=>$long);
	}
}