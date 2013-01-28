<?php

require_once('grouper.php');
require_once('responder.php');
require_once('db.php');


class matcher{
	
	public static function startShaking($userid){
		grouper::startGroup($userid);
	}
	
	/**
	 * Function to be called when receiving a "get" request.
	 * Finds the user's group, checks if the group has any special activity going on.
	 * @param unknown_type $userid
	 */
	public static function getGetResult($userid){
		$returnArray = array();
		
		// -------Get the user's group------
		$query1 = "SELECT current_group FROM preferences WHERE user_id=".$userid;
		$result1 = mysql_query($query1, $GLOBALS['db']) or die("getgetresult_r1:".mysql_error());
		$grouprow = mysql_fetch_array($result1);
		$owngroup = $grouprow['current_group'];
		$groupArray = grouper::makeGroupArray($owngroup, $userid);
		$group = array();
		$group['group'] = $groupArray;
		$memberArray = grouper::makeMemberArray($owngroup, $userid);
		$group['member'] = $memberArray;
		$returnArray['group'] = $group;
		// -------Check the group's voting info------
		
		$notification=array();
		
		$voteCheckQuery = "SELECT voting_invite, voting_join, invite_group_id, join_group_id FROM groups WHERE group_id=".$owngroup;
		$vcResult = mysql_query($voteCheckQuery, $GLOBALS['db']) or die("getgetresult_33:".mysql_error());
		$vcRow = mysql_fetch_assoc($vcResult);
		if ($vcRow){
			if ($vcRow['voting_invite']==1){ // Create notification array containing a vote
				$checkVoteQuery = "SELECT max_votes, yes_votes, no_votes FROM voting_invite WHERE group_id=".$owngroup;
				$cvResult = mysql_query($checkVoteQuery, $GLOBALS['db']) or die("getgetresult_cv:".mysql_error());
				$cvRow = mysql_fetch_assoc($cvResult);
				
				$inviteGroupQuery = "SELECT invite_group_id FROM groups WHERE group_id=".$owngroup;
				$igResult = mysql_query($inviteGroupQuery, $GLOBALS['db']) or die("getgetresult_55:".mysql_error());
				$igRow = mysql_fetch_assoc($igResult);
				$targetGroup = $igRow['invite_group_id'];
				if ($cvRow['yes_votes']/$cvRow['max_votes']>0.6){
					$notification[]=grouper::makeVoteNotification($targetGroup, "inviteDecision", 'A');
					// Setup a vote for the invited group
					grouper::transferInviteToJoin($owngroup, true);
				}else if ($cvRow['no_votes']/$cvRow['max_votes']>0.6){
					$notification[]=grouper::makeVoteNotification($targetGroup, "inviteDecision", 'D');
					grouper::transferInviteToJoin($owngroup, false);
				}else{
					$checkVoteStateQuery = "SELECT invite_vote FROM group_members WHERE user_id=".$userid;
					$cvsResult = mysql_query($checkVoteStateQuery, $GLOBALS['db']) or die("getgetresult_49:".mysql_error());
					$cvsRow = mysql_fetch_assoc($cvsResult);
					$voteStatus = $cvsRow['invite_vote'];
				
					if ($voteStatus==1){
						$inviteGroupQuery = "SELECT invite_group_id FROM groups WHERE group_id=".$owngroup;
						$igResult = mysql_query($inviteGroupQuery, $GLOBALS['db']) or die("getgetresult_55:".mysql_error());
						$igRow = mysql_fetch_assoc($igResult);
						$targetGroup = $igRow['invite_group_id'];
						$notification[] = grouper::makeVoteNotification($targetGroup, "inviteRequest", null, $userid);
						$changeVoteStateQuery = "UPDATE group_members SET invite_vote=2 WHERE user_id=".$userid;
						$cgvResult = mysql_query($changeVoteStateQuery, $GLOBALS['db']) or die("getgetresult_60:".mysql_error());
					}
				}
			}
			if ($vcRow['voting_invite']==2){ // Create notification array containing a result
				$checkVoteStateQuery = "SELECT invite_vote FROM group_members WHERE user_id=".$userid;
				$cvsResult = mysql_query($checkVoteStateQuery, $GLOBALS['db']) or die("getgetresult_66:".mysql_error());
				$cvsRow = mysql_fetch_assoc($cvsResult);
				$voteStatus = $cvsRow['invite_vote'];
				
				if ($voteStatus==1){
					$inviteGroupQuery = "SELECT invite_group_id FROM groups WHERE group_id=".$owngroup;
					$igResult = mysql_query($inviteGroupQuery, $GLOBALS['db']) or die("getgetresult_55:".mysql_error());
					$igRow = mysql_fetch_assoc($igResult);
					$targetGroup = $igRow['invite_group_id'];
					$getDecisionQuery = "SELECT voting_result FROM groups WHERE group_id=".$owngroup;
					$gdResult = mysql_query($getDecisionQuery, $GLOBALS['db']) or die("getgetresult_72:".mysql_error());
					$gdRow = mysql_fetch_assoc($gdResult);
					$vdecision = $gdRow['voting_result'];
					$notification[] = grouper::makeVoteNotification($targetGroup, "inviteDecision", $vdecision, $userid);
					$changeVoteStateQuery = "UPDATE group_members SET invite_vote=2 WHERE user_id=".$userid;
					$cgvResult = mysql_query($changeVoteStateQuery, $GLOBALS['db']) or die("getgetresult_77:".mysql_error());
				}
			}
			if ($vcRow['voting_join']==1){ // Create notification array containing a vote
				$checkVoteQuery = "SELECT max_votes, yes_votes, no_votes FROM voting_join WHERE group_id=".$owngroup;
				$cvResult = mysql_query($checkVoteQuery, $GLOBALS['db']) or die("getgetresult_82:".mysql_error());
				$cvRow = mysql_fetch_assoc($cvResult);
				
				$joinGroupQuery = "SELECT join_group_id FROM groups WHERE group_id=".$owngroup;
				$jgResult = mysql_query($joinGroupQuery, $GLOBALS['db']) or die("getgetresult_55:".mysql_error());
				$jgRow = mysql_fetch_assoc($jgResult);
				$targetGroup = $jgRow['join_group_id'];
				
				if ($cvRow['yes_votes']/$cvRow['max_votes']>0.6){
					$notification[]=grouper::makeVoteNotification($targetGroup, "joinDecision", 'A');
					// Setup a vote for the invited group
					grouper::cleanUpJoin($owngroup, true);
				}else if ($cvRow['no_votes']/$cvRow['max_votes']>0.6){
					$notification[]=grouper::makeVoteNotification($targetGroup, "joinDecision", 'D');
					grouper::cleanUpJoin($owngroup, false);
				}else{
					$checkVoteStateQuery = "SELECT join_vote FROM group_members WHERE user_id=".$userid;
					$cvsResult = mysql_query($checkVoteStateQuery, $GLOBALS['db']) or die("getgetresult_93:".mysql_error());
					$cvsRow = mysql_fetch_assoc($cvsResult);
					$voteStatus = $cvsRow['join_vote'];
					
					if ($voteStatus==1){
						$joinGroupQuery = "SELECT join_group_id FROM groups WHERE group_id=".$owngroup;
						$jgResult = mysql_query($joinGroupQuery, $GLOBALS['db']) or die("getgetresult_99:".mysql_error());
						$jgRow = mysql_fetch_assoc($jgResult);
						$targetGroup = $jgRow['join_group_id'];
						$notification[] = grouper::makeVoteNotification($targetGroup, "joinRequest", null);
						$changeVoteStateQuery = "UPDATE group_members SET join_vote=2 WHERE user_id=".$userid;
						$cgvResult = mysql_query($changeVoteStateQuery, $GLOBALS['db']) or die("getgetresult_124:".mysql_error());
					}
				}
			}else if ($vcRow['voting_join']==2){ // Create notification array containing a result
				$checkVoteStateQuery = "SELECT join_vote FROM group_members WHERE user_id=".$userid;
				$cvsResult = mysql_query($checkVoteStateQuery, $GLOBALS['db']) or die("getgetresult_107:".mysql_error());
				$cvsRow = mysql_fetch_assoc($cvsResult);
				$voteStatus = $cvsRow['join_vote'];
				
				if ($voteStatus==1){
					$getDecisionQuery = "SELECT join_group_id, voting_result FROM groups WHERE group_id=".$owngroup;
					$gdResult = mysql_query($getDecisionQuery, $GLOBALS['db']) or die("getgetresult_113:".mysql_error());
					$gdRow = mysql_fetch_assoc($gdResult);
					$vdecision = $gdRow['voting_result'];
					$otherGroup = $gdRow['join_group_id'];
					$notification[] = grouper::makeVoteNotification($otherGroup, "joinDecision", $vdecision);
					$changeVoteStateQuery = "UPDATE group_members SET join_vote=2 WHERE user_id=".$userid;
					$cgvResult = mysql_query($changeVoteStateQuery, $GLOBALS['db']) or die("getgetresult_119:".mysql_error());
				}
			}
		}
		$returnArray['notification'] = $notification;
		return $returnArray;
		
	}
	
	/**
	 * Function to be called when the user presses the refresh match result button.
	 * @param unknown_type $userid
	 */
	public static function refreshMatch($userid){
		
		
		// -------Get the user's group------
		$query1 = "SELECT current_group FROM preferences WHERE user_id=".$userid;
		$result1 = mysql_query($query1, $GLOBALS['db']) or die("matcher_137:".mysql_error());
		$grouprow = mysql_fetch_array($result1);
		$owngroup = $grouprow['current_group'];
		
		// -------Group matching portion------
		
		//$query0 = "SELECT group_id FROM groups WHERE is_active=1";
		$query0 = "SELECT group_id FROM groups";
		$result0 = mysql_query($query0, $GLOBALS['db']) or die("matcher_145:".mysql_error());
		
		$matchedList = array();
		while ($row=mysql_fetch_assoc($result0)){
			if ($row['group_id']==$owngroup){
				continue;
			}elseif (matcher::groupMatchGroup($owngroup, $row['group_id'])){
				$matchedList[] = $row['group_id'];
			}
		}
		
		$toplayer = matcher::makeMatchResponse($matchedList, $userid);
		return $toplayer;
	}
	
	/**
	 * Returns an array of matching groups given a group id. Used to get a quick update.
	 * @param unknown_type $groupid
	 */
	public static function getMatchedGroups($groupid){
		$query0 = "SELECT group_id FROM groups";
		$result0 = mysql_query($query0, $GLOBALS['db']) or die("matcher_166:".mysql_error());
		
		$matchedList = array();
		while ($row=mysql_fetch_assoc($result0)){
			if ($row['group_id']==$groupid){
				continue;
			}elseif (matcher::groupMatchGroup($owngroup, $row['group_id'])){
				$matchedList[] = $row['group_id'];
			}
		}
		return $matchedList;
	}
	
	/**
	 * Checks if two groups are "compatible"--that is, any group member
	 * from any side can be matched with anyone on the other side.
	 */
	public static function groupMatchGroup($group1id, $group2id){
		
		$getMemberQuery = "SELECT user_id FROM group_members WHERE group_id=".$group1id;
		$gmResult = mysql_query($getMemberQuery, $GLOBALS['db']) or die("matcher_185:".mysql_error());
		while ($gmRow = mysql_fetch_assoc($gmResult)){
			if (!matcher::groupMatch($gmRow['user_id'], $group2id)){
				return false;
			}
		}
		return true;
	}
	
	
	/**
	 * Forms the array that will be echoed to the frontend.
	 * @param array $notification The already constructed notification
	 * @param array $groupArray
	 */
	public static function makeMatchResponse($groupArray, $userid){
		if (count($groupArray)==0){
			$toplayer = array();
			$notification = array();
			$match = array();
			$toplayer['match'] = $match;
		}
		$toplayer = array();
		$match = array();
		foreach ($groupArray as $groupid){
			$overtop = array();
			$group = array();
			$member = array();
			
			$overtop['group'] = grouper::makeGroupArray($groupid, $userid);
			$overtop['member'] = grouper::makeMemberArray($groupid, true);
			
			$match[] = $overtop;
		}
		
		$toplayer['match'] = $match;
		return $toplayer;
	}
	
	
	/**
	 * Function to be called when the user stops matching.
	 * Removes the user from any group it belongs to, and removes the group if necessary
	 * @param unknown_type $userid
	 */
	public static function stopShaking($userid){
		$query0 = "SELECT current_group FROM preferences WHERE user_id=".$userid;
		$result0 = mysql_query($query0, $GLOBALS['db']) or die("matcher_232:".mysql_error());
		$row0 = mysql_fetch_array($result0);
		$groupid = $row0['current_group'];
		
		$nopQuery = "SELECT user_id FROM group_members WHERE group_id=".$groupid;
		$nopResult = mysql_query($nopQuery, $GLOBALS['db']) or die("matcher_237:".mysql_error());
		$nop = mysql_num_rows($nopResult);
		
		if ($nop==1){
			$deleteGroupQuery = "DELETE FROM groups WHERE group_id=".$groupid;
			$dgResult = mysql_query($deleteGroupQuery, $GLOBALS['db']) or die("matcher_242:".mysql_error());
		}else{
			$voteCheckQuery = "SELECT voting_invite, voting_join, invite_user_id, join_user_id FROM groups WHERE group_id=".$groupid;
			$vcResult = mysql_query($voteCheckQuery, $GLOBALS['db']) or die("matcher_245:".mysql_error());
			$vcRow = mysql_fetch_assoc($vcResult);
			if ($vsRow){
				if ($vcRow['voting_invite']==1){ // Create notification array containing a vote
					$checkVoteStateQuery = "SELECT invite_vote FROM group_members WHERE user_id=".$userid;
					$cvsResult = mysql_query($checkVoteStateQuery, $GLOBALS['db']) or die("matcher_250:".mysql_error());
					$cvsRow = mysql_fetch_assoc($cvsResult);
					$voteStatus = $cvsRow['invite_vote'];
						
					if (voteStatus==1 or voteStatus==2){
						$checkVoteQuery = "SELECT max_votes FROM voting_invite WHERE group_id=".$groupid;
						$cvResult = mysql_query($checkVoteQuery, $GLOBALS['db']) or die("matcher_256:".mysql_error());
						$cvRow = mysql_fetch_assoc($cvResult);
						$oldMax = $cvRow['max_votes'];
						$newMax = $oldMax-1;
						$updateVoteQuery = "UPDATE voting_invite SET max_votes=".$newMax." WHERE group_id=".$groupid;
						$uvResult = mysql_query($updateVoteQuery, $GLOBALS['db']) or die ("matcher_261:".mysql_error());
					}
				}elseif ($vcRow['voting_join']==1){ // Create notification array containing a vote
					$checkVoteStateQuery = "SELECT join_vote FROM group_members WHERE user_id=".$userid;
					$cvsResult = mysql_query($checkVoteStateQuery, $GLOBALS['db']) or die("matcher_265:".mysql_error());
					$cvsRow = mysql_fetch_assoc($cvsResult);
					$voteStatus = $cvsRow['join_vote'];
						
					if (voteStatus==1 or voteStatus==2){
						$checkVoteQuery = "SELECT max_votes FROM voting_join WHERE group_id=".$groupid;
						$cvResult = mysql_query($checkVoteQuery, $GLOBALS['db']) or die("matcher_271:".mysql_error());
						$cvRow = mysql_fetch_assoc($cvResult);
						$oldMax = $cvRow['max_votes'];
						$newMax = $oldMax-1;
						$updateVoteQuery = "UPDATE voting_join SET max_votes=".$newMax." WHERE group_id=".$groupid;
						$uvResult = mysql_query($updateVoteQuery, $GLOBALS['db']) or die ("matcher_276:".mysql_error());
					}
				}
			}
			$updatePrefQuery = "UPDATE preferences SET current_group=NULL WHERE user_id=".$userid;
			$upResult = mysql_query($updatePrefQuery, $GLOBALS['db']) or die(mysql_error());
			$updateGMQuery = "DELETE FROM group_members WHERE user_id=".$userid;
			$ugmResult = mysql_query($updateGMQuery, $GLOBALS['db']) or die(mysql_error()); 
		}
	}
	
	
	/**
	 * Checks if a user meets the preferences of all the group members.
	 * @param int $userid
	 * @param int $groupid
	 */
	public static function groupMatch($userid, $groupid){
		
		$gendermatch = false;
		$query0 = "SELECT gender FROM preferences WHERE user_id=".$userid;
		$result0 = mysql_query($query0, $GLOBALS['db']) or die("matcher_297:".mysql_error());
		$row0 = mysql_fetch_array($result0);
		$genpref1 = $row0['gender'];
	
		$query1 = "SELECT gender FROM profiles WHERE user_id=".$userid;
		$result1 = mysql_query($query1, $GLOBALS['db']) or die("matcher_302:".mysql_error());
		$row1 = mysql_fetch_array($result1);
		$gen1 = $row1['gender'];
	
		$gquery = "SELECT user_id FROM group_members WHERE group_id=".$groupid;
		$gresult = mysql_query($gquery, $GLOBALS['db']) or die("matcher_307:".mysql_error());
		while ($grow = mysql_fetch_array($gresult)){
			$olduser = $grow['user_id'];
			if (!matcher::singleMatch($userid, $olduser)){
				return false;
			}
			if (!$gendermatch){
				$query2 = "SELECT gender FROM preferences WHERE user_id=".$olduser;
				$result2 = mysql_query($query2, $GLOBALS['db']) or die("matcher_315:".mysql_error());
				$row2 = mysql_fetch_array($result2);
				$genpref2 = $row2['gender'];
	
				$query3 = "SELECT gender FROM profiles WHERE user_id=".$olduser;
				$result3 = mysql_query($query3, $GLOBALS['db']) or die("matcher_320:".mysql_error());
				$row3 = mysql_fetch_array($result3);
				$gen2 = $row3['gender'];
	
				$gendermatch = matcher::genderMatch($genpref1, $gen1, $genpref2, $gen2);
			}
		}
	
		if (!$gendermatch){
			return false;
		}else{
			$gcquery = "SELECT cuisine_1, cuisine_2, cuisine_3, cuisine_4, cuisine_5".
					", cuisine_6, cuisine_7, cuisine_8, cuisine_9, cuisine_10, cuisine_11".
					", cuisine_12 FROM groups WHERE group_id=".$groupid;
			$gcresult = mysql_query($gcquery, $GLOBALS['db']) or die("matcher_334:".mysql_error());
			$gcrow = mysql_fetch_array($gcresult);
			$gcvrow = array_values($gcrow);
	
			$scquery = "SELECT cuisine_1, cuisine_2, cuisine_3, cuisine_4, cuisine_5".
					", cuisine_6, cuisine_7, cuisine_8, cuisine_9, cuisine_10, cuisine_11".
					", cuisine_12 FROM foodtype WHERE user_id=".$userid;
			$scresult = mysql_query($scquery, $GLOBALS['db']) or die("matcher_341:".mysql_error());
			$scrow = mysql_fetch_array($scresult);
			$scvrow = array_values($scrow);
	
			$cuisinematch=0;
			for ($i=1;$i<12; $i++){
				if ($gcvrow[$i]==1 && $scvrow[$i]==1){
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
		$result0 = mysql_query($query0, $GLOBALS['db']) or die("matcher_369:".mysql_error());
		$origin = mysql_fetch_array($result0);
	
		$query1 = "SELECT user_id, latitude, longitude, distance FROM preferences WHERE user_id =".$userid2;
		$result1 = mysql_query($query1, $GLOBALS['db']) or die("matcher_375:".mysql_error());
		$row1 = mysql_fetch_array($result1);
	
		if ($origin['distance']!=null){
			$rangematch = matcher::inRange($origin['latitude'], $origin['longitude'],
					$row1['latitude'], $row1['longitude'],
					min(array($origin['distance'], $row1['distance'])));
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
	 * Calculates the average distance of the specified group from the specified user.
	 * @param int $userid
	 * @param int $groupid
	 */
	public static function getAvgDist($userid, $groupid){
		$getLLQuery = "SELECT latitude, longitude FROM preferences WHERE user_id=".$userid;
		$gllResult = mysql_query($getLLQuery, $GLOBALS['db']) or die("matcher_399:".mysql_error());
		$gllRow = mysql_fetch_assoc($gllResult);
		$selflat = $gllRow['latitude'];
		$selflng = $gllRow['longitude'];
		$r = 3958.761;
		$a1 = deg2rad($selflat);
		$b1 = deg2rad($selflng);
		
		$getMembersQuery = "SELECT user_id FROM group_members WHERE group_id=".$groupid;
		$gmResult = mysql_query($getMembersQuery, $GLOBALS['db']) or die("matcher_408:".mysql_error());
		$memberArray = array();
		$totalDist = 0;
		while ($gmRow = mysql_fetch_assoc($gmResult)){
			$memberArray[] = $gmRow['user_id'];
		}
		foreach ($memberArray as $memberid){
			$getMLLQuery = "SELECT latitude, longitude FROM preferences WHERE user_id=".$memberid;
			$gmllResult = mysql_query($getMLLQuery, $GLOBALS['db']) or die("matcher_416:".mysql_error());
			$gmllRow = mysql_fetch_assoc($gmllResult);
			$gmlat = $gmllRow['latitude'];
			$gmlng = $gmllRow['longitude'];
			$a2 = deg2rad($gmlat);
			$b2 = deg2rad($gmlng);
			$dist = acos(cos($a1)*cos($b1)*cos($a2)*cos($b2) + cos($a1)*sin($b1)*cos($a2)*sin($b2) + sin($a1)*sin($a2)) * $r;
			$totalDist+=$dist;
		}
		return round($totalDist/count($memberArray), 4);
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
		$b1 = deg2rad($lo1);
		$a2 = deg2rad($la2);
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
	
	/**
	 * Takes a row of data and generates an array of selected cuisine list for response;
	 * @param unknown_type $row
	 * @param unknown_type $size
	 */
	public static function getCuisineList($row, $size){
		$clist = array();
		if ($row['cuisine_1']==1){
			$clist[]='cuisine_1';
		}
		if ($row['cuisine_2']==1){
			$clist[]='cuisine_2';
		}
		if ($row['cuisine_3']==1){
			$clist[]='cuisine_3';
		}
		if ($row['cuisine_4']==1 && count($clist)<$size){
			$clist[]='cuisine_4';
		}
		if ($row['cuisine_5']==1 && count($clist)<$size){
			$clist[]='cuisine_5';
		}
		if ($row['cuisine_6']==1 && count($clist)<$size){
			$clist[]='cuisine_6';
		}
		if ($row['cuisine_7']==1 && count($clist)<$size){
			$clist[]='cuisine_7';
		}
		if ($row['cuisine_8']==1 && count($clist)<$size){
			$clist[]='cuisine_8';
		}
		if ($row['cuisine_9']==1 && count($clist)<$size){
			$clist[]='cuisine_9';
		}
		if ($row['cuisine_10']==1 && count($clist)<$size){
			$clist[]='cuisine_10';
		}
		if ($row['cuisine_11']==1 && count($clist)<$size){
			$clist[]='cuisine_11';
		}
		if ($row['cuisine_12']==1 && count($clist)<$size){
			$clist[]='cuisine_12';
		}
		while(count($clist)<$size){
			$clist[]='';
		}
		return $clist;
	}


}