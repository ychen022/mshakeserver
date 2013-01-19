<?php
class grouper{
	
}

class group{
	public $users;
	public $foodtype;
	
	public function __construct($initUser){
		require ("db.php");
		$this->users = array();
		$this->users[] = $initUser;
		
		$query = "SELECT type FROM foodtype WHERE user_id=".$initUser;
		$result = mysql_query($query, $db) or die(mysql_error());
		$foodtype = array();
		while ($ofrow = mysql_fetch_array($result)){
			$foodtype[] = $ofrow['type'];
		}
	}
	
	/**
	 * Add a user to the group. The user should be able to pass the matching checks before being added.
	 * @param unknown_type $userid The id of the user being added
	 */
	public function addUser($userid){
		$this->users[] = $user;
		
		$query = "SELECT type FROM foodtype WHERE user_id=".$userid;
		$result = mysql_query($query, $db) or die(mysql_error());
		$newtype = array();
		while ($frow = mysql_fetch_array($result)){
			$newtype[] = $ofrow['type'];
		}
		foreach ($newtype as $newfood){
			if (!in_array($newfood, $this->foodtype)){
				if(($key = array_search($newfood, $this->foodtype)) !== false) {
					unset($this->foodtype[$key]);
				}
			}
		}
	}
	
	/**
	 * Removes a user from the group.
	 * @param unknown_type $userid
	 */
	public function removeUser($userid){
		if(($key = array_search($userid, $this->users)) !== false) {
	    	unset($messages[$key]);
		}
		//TODO reset common food interest
	}
	
	/**
	 * Checks if a user meets the preferences of all the group members.
	 * @param unknown_type $userid
	 */
	public function checkMatch($userid){
		$gendermatch = false;
		$query0 = "SELECT gender FROM preferences WHERE user_id=".$userid;
		$result0 = mysql_query($query0, $db) or die(mysql_error());
		$row0 = mysql_fetch_array($result0);
		$genpref1 = $row0['gender'];
		
		$query1 = "SELECT gender FROM profiles WHERE user_id=".$userid;
		$result1 = mysql_query($query1, $db) or die(mysql_error());
		$row1 = mysql_fetch_array($result1);
		$gen1 = $row1['gender'];
		
		foreach ($this->users as $olduser){
			if (!singleMatch($userid, olduser)){
				return false;
			}
			if (!$gendermatch){
				$query2 = "SELECT gender FROM preferences WHERE user_id=".$olduser;
				$result2 = mysql_query($query2, $db) or die(mysql_error());
				$row2 = mysql_fetch_array($result2);
				$genpref2 = $row2['gender'];
				
				$query3 = "SELECT gender FROM profiles WHERE user_id=".$userid;
				$result3 = mysql_query($query3, $db) or die(mysql_error());
				$row3 = mysql_fetch_array($result3);
				$gen2 = $row3['gender'];
				
				$gendermatch = genderMatch($genpref1, $gen1, $genpref2, $gen2);
			}
		}
		return $gendermatch;
	}
	
	/**
	 * Checks if the preferences of the two users can match.
	 * @param unknown_type $userid1
	 * @param unknown_type $userid2
	 */
	public static function singleMatch($userid1, $userid2){
		require("db.php");
		// Get the preferences of the shaker
		$query0 = "SELECT user_id, latitude, longitude, distance FROM preferences WHERE user_id=".$userid1;
		$result0 = mysql_query($query0, $db) or die(mysql_error());
		$row0 = mysql_fetch_array($result0);
		
		
		// Get all the currently shaking users
		$query1 = "SELECT user_id, latitude, longitude, distance FROM preferences WHERE user_id =".$userid2;
		$result1 = mysql_query($query1, $db) or die(mysql_error());
		$row1 = mysql_fetch_array($result1);
		
		if ($origin['distance']!=null){
			$rangematch = inRange($origin['latitude'], $origin['longitude'],
					$row['latitude'], $row['longitude'], 
					min(array($origin['distance'], $row['distance'])));
			if (!$rangematch){
				return false;
			}
			return true;
		}else{
			return true;
		}
			// INSERT GROUP SIZE/ AGE RANGE CHECK HERE	
	}
	
	/**
	 * Determines if the two points defined by latitute and longitude are within the given range.
	 * @param unknown_type $la1
	 * @param unknown_type $lo1
	 * @param unknown_type $la2
	 * @param unknown_type $lo2
	 * @param unknown_type $maxdist
	 */
	public static function inRange($la1, $lo1, $la2, $lo2, $maxdist){
		$a1 = deg2rad($la1);
		$a2 = deg2rad($lo1);
		$b1 = deg2rad($la2);
		$b2 = deg2rad($lo2);
		$r = 3958.761;
		$dist = acos(cos($a1)*cos($b1)*cos($a2)*cos($b2) + cos($a1)*sin($b1)*cos($a2)*sin($b2) + sin($a1)*sin($a2)) * $r;
		return ($dist<=$maxdist);
	}
	
	/**
	 * Determines if the two users can be matched based on their gender preferences
	 * @param unknown_type $genpref1
	 * @param unknown_type $gen1
	 * @param unknown_type $genpref2
	 * @param unknown_type $gen2
	 */
	public static function genderMatch($genpref1, $gen1, $genpref2, $gen2){
		if ($genpref1=="any"){
			if ($genpref2=="any"){
				return true;
			}else if ($genpref2==$gen1){
				return true;
			}else{
				return false;
			}
		}else if ($genpref1==$gen2){
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * Outputs the group info as a JSON string to be transmitted to the frontend.
	 */
	public function packToJson(){
		
	}
}