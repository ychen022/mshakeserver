<?php
require_once("db.php");

class preferences{
	public static function editPreferences($userid, $request){
		
		// Pretranslate data for storage
		$gsize = $request['number'];
		
		if ($request['address']!=""){
			//$latlng = preferences::addressToLatLng($request['address'], $request['city'],
			//		$request['state'], $request['zipcode']);
			$latlng = preferences::addressToLatLngCurl($request['address'], $request['city'],
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
				$gsize.", gender='".strtolower($request['people'])."', price_min=".
				$request['pricemin'].", price_max=".$request['pricemax'].", latitude=".
				$latlng['lat'].", longitude=".$latlng['lng']." WHERE user_id=".
				$userid;
		$result1 = mysql_query($query1, $GLOBALS['db']) or die(mysql_error());;
		
		// Update row contents in foodtype table
		$type = $request['type'];
		$query2 = "UPDATE foodtype SET cuisine_1=".$type['cuisine_1'].", cuisine_2=".
				$type['cuisine_2'].", cuisine_3=".$type['cuisine_3'].", cuisine_4=".
				$type['cuisine_4'].", cuisine_5=".$type['cuisine_5'].", cuisine_6=".
				$type['cuisine_6'].", cuisine_7=".$type['cuisine_7'].", cuisine_8=".
				$type['cuisine_8'].", cuisine_9=".$type['cuisine_9'].", cuisine_10=".
				$type['cuisine_10'].", cuisine_11=".$type['cuisine_11'].", cuisine_12=".
				$type['cuisine_12']." WHERE user_id=".$userid;
		$result2 = mysql_query($query2, $GLOBALS['db']) or die(mysql_error());;
		
	}
	
	public static function getPreferences($userid){
		
	}
	
	public static function addressToLatLngCurl($address, $city, $state, $zipcode){
		$comp_address = $address.",".$city.",".$state." ".$zipcode;
		$curl = curl_init();
		curl_setopt_array($curl, array(
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_URL => 'http://maps.google.com/maps/api/geocode/json?address='.urlencode($comp_address).'&sensor=false'
		));
		$geocode = curl_exec($curl);
		if ($geocode){
			$output= json_decode($geocode);
			$lat = $output->results[0]->geometry->location->lat;
			$long = $output->results[0]->geometry->location->lng;
			
			// return just the latitude and longitude
			return array('lat'=>$lat,'lng'=>$long);
		}else{
			return null;
		}
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
	
	public static function uploadProfilePic(){
		$allowedExts = array("jpg", "jpeg", "gif", "png");
		$extension = end(explode(".", $_FILES["file"]["name"]));
		if ((($_FILES["file"]["type"] == "image/gif")
				|| ($_FILES["file"]["type"] == "image/jpeg")
				|| ($_FILES["file"]["type"] == "image/png")
				|| ($_FILES["file"]["type"] == "image/pjpeg"))
				&& ($_FILES["file"]["size"] < 20000)
				&& in_array($extension, $allowedExts))
		{
			if ($_FILES["file"]["error"] > 0)
			{
				echo "Error: " . $_FILES["file"]["error"] . "<br>";
			}
			else
			{
				$filename = $_FILES["file"]["name"];
				
				$filename = strtolower($filename) ;
				$exts = split("[/\\.]", $filename) ;
				$n = count($exts)-1;
				$ext = $exts[$n];
				
				$new_file_name=$_SESSION['user']."profile".$ext;
				$currentLoc = $_FILES["file"]["tmp_name"];
				move_uploaded_file($currentLoc,"profilepics/profile/" . $new_file_name);
				$addPicQuery = "UPDATE profiles SET photolink='profilepics/profile/".$new_file_name."' WHERE user_id=".$_SESSION['user'];
				$apResult = mysql_query($addPicQuery, $GLOBALS['db']) or die("pref_82: ".mysql_error());
				responder::respondSimple('good');
			}
		}
		else
		{
			responder::respondSimple('bad');
		}
	}
	
	public static function uploadThumbnail(){
		$allowedExts = array("jpg", "jpeg", "gif", "png");
		$extension = end(explode(".", $_FILES["file"]["name"]));
		if ((($_FILES["file"]["type"] == "image/gif")
				|| ($_FILES["file"]["type"] == "image/jpeg")
				|| ($_FILES["file"]["type"] == "image/png")
				|| ($_FILES["file"]["type"] == "image/pjpeg"))
				&& ($_FILES["file"]["size"] < 20000)
				&& in_array($extension, $allowedExts))
		{
			if ($_FILES["file"]["error"] > 0)
			{
				echo "Error: " . $_FILES["file"]["error"] . "<br>";
			}
			else
			{
				$filename = $_FILES["file"]["name"];
		
				$filename = strtolower($filename) ;
				$exts = split("[/\\.]", $filename) ;
				$n = count($exts)-1;
				$ext = $exts[$n];
		
				$new_file_name=$_SESSION['user']."thumb".$ext;
				$currentLoc = $_FILES["file"]["tmp_name"];
				move_uploaded_file($currentLoc,"profilepics/thumbnail/" . $new_file_name);
				$addPicQuery = "UPDATE profiles SET thumblink='profilepics/thumbnail/".$new_file_name."' WHERE user_id=".$_SESSION['user'];
				$apResult = mysql_query($addPicQuery, $GLOBALS['db']) or die("pref_82: ".mysql_error());
				responder::respondSimple('good');
			}
		}
		else
		{
			responder::respondSimple('bad');
		}
	}
}