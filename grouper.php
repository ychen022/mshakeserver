<?php
class grouper{
	public static function startGroup($initUser){
		//require_once("db.php");
		
		$db = mysql_connect("localhost", "root", "") or die(mysql_error());
		
		mysql_select_db('mealshake', $db);
		
		$query0 = "SELECT group_id FROM group_members WHERE user_id=".$initUser;
		$result0 = mysql_query($query0, $db) or die(mysql_error());
		if ($row0 = mysql_fetch_array($result0)){
			
		}else{
			$query1 = "SELECT * FROM preferences INNER JOIN foodtype ON preferences.user_id=foodtype.user_id". 
					" WHERE preferences.user_id=$initUser";
			$result1 = mysql_query($query1, $db) or die(mysql_error());
			$initPref = mysql_fetch_array($result1);
			
			$query2 = "INSERT INTO groups (is_active, date_created, capacity, cuisine_1, cuisine_2, ".
					"cuisine_3, cuisine_4, cuisine_5, cuisine_6, cuisine_7, cuisine_8, cuisine_9, ".
					"cuisine_10, cuisine_11, cuisine_12, price_min, price_max) VALUES (1, CURDATE(),".
					$initPref['groupsize'].",".$initPref['cuisine_1'].",".$initPref['cuisine_2'].
					",".$initPref['cuisine_3'].",".$initPref['cuisine_4'].",".$initPref['cuisine_5'].
					",".$initPref['cuisine_6'].",".$initPref['cuisine_7'].",".$initPref['cuisine_8'].
					",".$initPref['cuisine_9'].",".$initPref['cuisine_10'].",".$initPref['cuisine_11'].
					",".$initPref['cuisine_12']. ",".$initPref['price_min'].",".$initPref['price_max'].
					")"; 
			$result2 = mysql_query($query2, $db) or die(mysql_error());
			
			$groupidquery = mysql_query("SELECT LAST_INSERT_ID()", $db) or die(mysql_error());
			$groupidresult = mysql_fetch_assoc($groupidquery);
			$groupid = $groupidresult['LAST_INSERT_ID()'];
			
			$query3 = "INSERT INTO group_members (group_id, user_id) VALUES (".$groupid.",".$initUser.")";
			$result3 = mysql_query($query3, $db) or die(mysql_error());
			
			$query4 = "UPDATE preferences SET current_group=".$groupid;
			$result4 = mysql_query($query4, $db) or die(mysql_error());
		}
	}
	
	/**
	 * Add a user to the group. The user should be able to pass the matching checks before being added.
	 * @param unknown_type $userid
	 * @param unknown_type $groupid
	 */
	public static function addUser($userid, $groupid){
		
	}
	
	public static function removeUser($userid, $groupid){
		
	}
	
	public static function requestToJoin($userid, $groupid){
		
	}
	
	public static function startVote($appid, $voterid){
		
	}

}

