<?php

require_once('grouper.php');
require_once('responder.php');

class matcher{
	
	public static function startShaking($userid){
		require_once('db.php');
		grouper::startGroup($userid);
	}
	
	public static function getResult($userid){
		require_once('db.php');
		$query0 = "SELECT group_id FROM groups WHERE is_active=1";
		$result0 = mysql_query($query0, $db) or die(mysql_error());
		if (mysql_num_rows($result0)==0){
			
		}
		
		$query1 = "SELECT current_group FROM preferences WHERE user_id=".$userid;
		$result1 = mysql_query($query1, $db) or die(mysql_error());
		$grouprow = mysql_fetch_array($result1);
		$owngroup = $grouprow['current_group'];
		
		
		$matchedList = array();
		while ($row=mysql_fetch_array($result0)){
			if ($row['group_id']==$owngroup){
				continue;
			}elseif (matcher::groupMatch($userid, $row['group_id'])){
				$matchedList[] = $row['group_id'];
			}
		}
		matcher::makeGetResponse($matchedList);
		
	}
	
	public static function makeGetResponse($groupArray){
		if (count($groupArray)==0){
			$toplayer = array();
			$notification = array();
			$match = array();
			$toplayer['notification'] = $notification;
			$toplayer['match'] = $match;
		}
		$toplayer = array();
		$notification = array();
		$match = array();
		foreach ($groupArray as $groupid){
			$overtop = array();
			$group = array();
			$member = array();
			
			// Get number of current groupmembers
			$nopquery = "SELECT user_id FROM group_members WHERE group_id=".$groupid;
			$nopresult = mysql_query($nopquery) or die(mysql_error());
			$nop = mysql_num_rows($groupresult);
			// Get and fill in group info
			$gsquery = "SELECT * FROM groups WHERE group_id=".$groupid;
			$gsresult = mysql_query($gsquery) or die(mysql_error());
			$gsrow = mysql_fetch_array($gsresult);
			$pricemin = $gsrow['price_min'];
			$pricemax = $gsrow['price_max'];
			$avgdist = 10;  //TODO: implement average distance calculation
			$capacity = (($gsrow['capacity']==2)?2:5);
			$foodtype = matcher::getCuisineList($gsrow, 3);
			$group['nop'] = $nop;
			$group['pricemin'] = $pricemin;
			$group['pricemax'] = $pricemax;
			$group['avgdist'] = $avgdist;
			$group['capacity'] = $capacity;
			$group['foodtype'] = $foodtype;
			
			$overtop['group'] = $group;
			
			
			// Get info of each groupmember
			while ($memberrow = mysql_fetch_array($nopresult)){
				$memberarray = array();
				$memberid = $memberrow['user_id'];
				$ipquery = "SELECT (first_name, last_name, gender, photolink) FROM profiles WHERE user_id=".$memberid;
				$ipresult = mysql_query($ipquery) or die(mysql_error());
				$iprow = mysql_fetch_array($ipresult);
				$memberarray['id']=$memberid;
				$memberarray['firstname'] = $iprow['first_name'];
				$memberarray['lastname'] = $iprow['last_name'];
				$memberarray['photolink'] = $iprow['photolink'];
				$memberarray['gender'] = $iprow['gender'];
				
				$ftquery = "SELECT * FROM foodtype WHERE user_id=".$memberid;
				$ftresult = mysql_query($ftquery) or die(mysql_error());
				$ftrow = mysql_fetch_array($ftresult);
				$pfoodtype = matcher::getCuisineList($ftrow, 12);
				$memberarray['foodtype'] = $pfoodtype;
				$member[] = $memberarray;
				$overtop['member'] = $member;
			}
			$match[] = $overtop;
		}
		
		$toplayer['notification'] = $notification;
		$toplayer['match'] = $match;
		responder::respondJson($toplayer);
		
		
	}
	
	public static function stopShaking($userid){
		
	}
	
	
	/**
	 * Checks if a user meets the preferences of all the group members.
	 * @param int $userid
	 * @param int $groupid
	 */
	public static function groupMatch($userid, $groupid){
		
		$gendermatch = false;
		$query0 = "SELECT gender FROM preferences WHERE user_id=".$userid;
		$result0 = mysql_query($query0, $db) or die(mysql_error());
		$row0 = mysql_fetch_array($result0);
		$genpref1 = $row0['gender'];
	
		$query1 = "SELECT gender FROM profiles WHERE user_id=".$userid;
		$result1 = mysql_query($query1, $db) or die(mysql_error());
		$row1 = mysql_fetch_array($result1);
		$gen1 = $row1['gender'];
	
		$gquery = "SELECT user_id FROM group_members WHERE group_id=".$groupid;
		$gresult = mysql_query($gquery, $db) or die(mysql_error());
		while ($grow = mysql_fetch_array($gresult)){
			$olduser = $grow['user_id'];
			if (!grouper::singleMatch($userid, $olduser)){
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
	
				$gendermatch = grouper::genderMatch($genpref1, $gen1, $genpref2, $gen2);
			}
		}
	
		if (!$gendermatch){
			return false;
		}else{
			$gcquery = "SELECT (cuisine_1, cuisine_2, cuisine_3, cuisine_4, cuisine_5".
					", cuisine_6, cuisine_7, cuisine_8, cuisine_9, cuisine_10, cuisine_11".
					", cuisine_12) FROM groups WHERE group_id=".$groupid;
			$gcresult = mysql_query($gcquery, $db) or die(mysql_error());
			$gcrow = mysql_fetch_array($gcresult);
			$gcvrow = array_values($gcrow);
	
			$scquery = "SELECT (cuisine_1, cuisine_2, cuisine_3, cuisine_4, cuisine_5".
					", cuisine_6, cuisine_7, cuisine_8, cuisine_9, cuisine_10, cuisine_11".
					", cuisine_12) FROM foodtype WHERE user_id=".$userid;
			$scresult = mysql_query($scquery, $db) or die(mysql_error());
			$scrow = mysql_fetch_array($scresult);
			$scvrow = array_values($scrow);
	
			$cuisinematch=0;
			for ($i=1;$i<12; $i++){
				if ($gcvrow[i]==1 && $scvrow[i]==1){
					$cuisinematch++;
				}else{
				}
			}
			if ($cuisinematch==0){
				return false;
			}else{
				return true;
			}
		}
	}
	
	/**
	 * Checks if the preferences of the two users can match.
	 * @param unknown_type $userid1
	 * @param unknown_type $userid2
	 */
	public static function singleMatch($userid1, $userid2){
		require_once("db.php");
		// Get the preferences of the shaker
		$query0 = "SELECT user_id, latitude, longitude, distance FROM preferences WHERE user_id=".$userid1;
		$result0 = mysql_query($query0, $db) or die(mysql_error());
		$row0 = mysql_fetch_array($result0);
	
	
		// Get all the currently shaking users
		$query1 = "SELECT user_id, latitude, longitude, distance FROM preferences WHERE user_id =".$userid2;
		$result1 = mysql_query($query1, $db) or die(mysql_error());
		$row1 = mysql_fetch_array($result1);
	
		if ($origin['distance']!=null){
			$rangematch = grouper::inRange($origin['latitude'], $origin['longitude'],
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
		}else if ($genpref1==$gen2 || $genpref2=="any"){
			return true;
		}else{
			return false;
		}
	}
	
	public static function getCuisineList($row, $size){
		$clist = array();
		if ($row[cuisine_1]==1){
			$clist[]='cuisine_1';
		}
		if ($row[cuisine_2]==1){
			$clist[]='cuisine_2';
		}
		if ($row[cuisine_3]==1){
			$clist[]='cuisine_3';
		}
		if ($row[cuisine_4]==1 && count($clist)<$size){
			$clist[]='cuisine_4';
		}
		if ($row[cuisine_5]==1 && count($clist)<$size){
			$clist[]='cuisine_5';
		}
		if ($row[cuisine_6]==1 && count($clist)<$size){
			$clist[]='cuisine_6';
		}
		if ($row[cuisine_7]==1 && count($clist)<$size){
			$clist[]='cuisine_7';
		}
		if ($row[cuisine_8]==1 && count($clist)<$size){
			$clist[]='cuisine_8';
		}
		if ($row[cuisine_9]==1 && count($clist)<$size){
			$clist[]='cuisine_9';
		}
		if ($row[cuisine_10]==1 && count($clist)<$size){
			$clist[]='cuisine_10';
		}
		if ($row[cuisine_11]==1 && count($clist)<$size){
			$clist[]='cuisine_11';
		}
		if ($row[cuisine_12]==1 && count($clist)<$size){
			$clist[]='cuisine_12';
		}
		while(count($clist)<$size){
			$clist[]='';
		}
	}


}