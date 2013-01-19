<?php
class matcher{
	public static function groupMatch($userid, $groupArray){
		$matchedGroups = array();
		foreach ($groupArray as $theGroup){
			
		}
	}
	
	public static function singleMatch($userid){
		require("db.php");
		// Get the preferences of the shaker
		$query0 = "SELECT * FROM preferences WHERE user_id=".$userid;
		$result0 = mysql_query($query0, $db) or die(mysql_error());
		$origin = mysql_fetch_array($result0);
		
		
		// Get all the currently shaking users
		$query1 = "SELECT user_id, latitude, longitude, distance, vegetarian FROM currently_shaking INNER JOIN preferences WHERE currently_shaking.user_id = preferences.user_id";
		$result1 = mysql_query($query1, $db) or die(mysql_error());
		$matchedSet = array();
		
		
		$query2 = "SELECT type FROM foodtype WHERE user_id=".$userid;
		$result2 = mysql_query($query2, $db) or die(mysql_error());
		$origin_foodtype = array();
		while ($ofrow = mysql_fetch_array($result2)){
			$origin_foodtype[] = $ofrow['type'];
		}
		// Iterate through the result and filter the possible matches
		while ($row = mysql_fetch_array($result1)){
			if ($origin['distance']!=null){
				$rangematch = inRange($origin['latitude'], $origin['longitude'],
						$row['latitude'], $row['longitude'], 
						min(array($origin['distance'], $row['distance'])));
				if (!$rangematch){
					continue;
				}
			}
			if (!veggieMatch($origin['vegetarian'], $row['vegetarian'])){
				continue;
			}
			// INSERT GROUP SIZE/ AGE RANGE HERE
			
			$query3 = "SELECT type FROM foodtype WHERE user_id=".$row['user_id'];
			$result3 = mysql_query($query3, $db) or die(mysql_error());
			$row_foodtype = array();
			while ($ofrow = mysql_fetch_array($result3)){
				$row_foodtype[] = $ofrow['type'];
			}
			if (!typeMatch($origin_foodtype, $row_foodtype)){
				continue;
			}
			
			// If the row passes all conditions and reaches here, add it into the matched set
			$matchedSet[]=$row;
		}
		
		
		// Process the matchedset to return to the frontend
		
	}
	
	public static function startShaking($userid){
		
	}
	
	public static function stopShaking($userid){
		
	}
	
	public static function inRange($la1, $lo1, $la2, $lo2, $maxdist){
		$a1 = deg2rad($la1);
		$a2 = deg2rad($lo1);
		$b1 = deg2rad($la2);
		$b2 = deg2rad($lo2);
		$r = 3958.761;
		$dist = acos(cos($a1)*cos($b1)*cos($a2)*cos($b2) + cos($a1)*sin($b1)*cos($a2)*sin($b2) + sin($a1)*sin($a2)) * $r;
		return ($dist<=$maxdist);
	}
	
	public static function veggieMatch($value1, $value2){
		if ($value1==0 || $value2==0){
			return true;
		}else if ($value1==$value2){
			return true;
		}
		return false;
	}
	
	public static function typeMatch($array1, $array2){
		foreach ($array1 as $value1){   //might need to add & before $value
			foreach ($array2 as $value2){
				if ($value1==$value2){
					return true;
				}else{
					continue;
				}
			}
		}
	}
}