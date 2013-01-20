<?php
class matcher{
	public static function groupMatch($userid, $groupArray){
		$matchedGroups = array();
		foreach ($groupArray as $theGroup){
			if ($theGroup->checkMatch(userid)){
				$matchedGroups[] = $theGroup;
			}
		}
		return $matchedGroups;
	}
	

	
	public static function startShaking($userid){
		require('db.php');
		grouper::startGroup($userid);
	}
	
	public static function getResult($userid){
		require('db.php');
		$query0 = "SELECT group_id FROM groups WHERE is_active=1";
		$result0 = mysql_query($query0, $db) or die(mysql_error());
		while ($row=mysql_fetch_array($result0)){
			
		}
	}
	
	public static function stopShaking($userid){
		
	}
	

	
//  // Match for vegetarian options...deprecated	
// 	public static function veggieMatch($value1, $value2){
// 		if ($value1==0 || $value2==0){
// 			return true;
// 		}else if ($value1==$value2){
// 			return true;
// 		}
// 		return false;
// 	}
	

}