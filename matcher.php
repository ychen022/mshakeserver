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