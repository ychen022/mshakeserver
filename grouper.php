<?php

require_once("db.php");
class grouper{
	public static function startGroup($initUser){
		//require_once("db.php");
		
// 		$GLOBALS['db'] = mysql_connect("localhost", "root", "") or die(mysql_error());
		
// 		mysql_select_db('mealshake', $GLOBALS['db']);
		
		$query0 = "SELECT group_id FROM group_members WHERE user_id=".$initUser;
		$result0 = mysql_query($query0) or die("grouper_13:".mysql_error());
		if ($row0 = mysql_fetch_array($result0)){
			
		}else{
			$query1 = "SELECT * FROM preferences INNER JOIN foodtype ON preferences.user_id=foodtype.user_id". 
					" WHERE preferences.user_id=$initUser";
			$result1 = mysql_query($query1) or die("grouper_19:".mysql_error());
			$initPref = mysql_fetch_array($result1);
			
			$query2 = "INSERT INTO groups (date_created, capacity, cuisine_1, cuisine_2, ".
					"cuisine_3, cuisine_4, cuisine_5, cuisine_6, cuisine_7, cuisine_8, cuisine_9, ".
					"cuisine_10, cuisine_11, cuisine_12, price_min, price_max) VALUES (CURDATE(),".
					$initPref['groupsize'].",".$initPref['cuisine_1'].",".$initPref['cuisine_2'].
					",".$initPref['cuisine_3'].",".$initPref['cuisine_4'].",".$initPref['cuisine_5'].
					",".$initPref['cuisine_6'].",".$initPref['cuisine_7'].",".$initPref['cuisine_8'].
					",".$initPref['cuisine_9'].",".$initPref['cuisine_10'].",".$initPref['cuisine_11'].
					",".$initPref['cuisine_12']. ",".$initPref['price_min'].",".$initPref['price_max'].
					")"; 
			$result2 = mysql_query($query2) or die("grouper_31:".mysql_error());
			
			$groupidquery = mysql_query("SELECT LAST_INSERT_ID()") or die("grouper_33:".mysql_error());
			$groupidresult = mysql_fetch_assoc($groupidquery);
			$groupid = $groupidresult['LAST_INSERT_ID()'];
			
			$_SESSION['groupid'] = $groupid; // THIS MIGHT GO WRONG
			
			$query3 = "INSERT INTO group_members (group_id, user_id) VALUES (".$groupid.",".$initUser.")";
			$result3 = mysql_query($query3) or die("grouper_40:".mysql_error());
			
			$query4 = "UPDATE preferences SET current_group=".$groupid." WHERE user_id=".$initUser;
			$result4 = mysql_query($query4) or die("grouper_43:".mysql_error());
		}
	}
	
	/**
	 * Add a user to the group. The user should be able to pass the matching checks before being added.
	 * DEPRECATED. Use mergeGroup() instead.
	 * @param unknown_type $userid
	 * @param unknown_type $groupid
	 */
	public static function addUser($userid, $groupid){
		
		// Remove old member relation
		$curRelationQuery = "SELECT current_group FROM preferences WHERE user_id=".$userid;
		$crResult = mysql_query($curRelationQuery, $GLOBALS['db']) or die("grouper_57:".mysql_error());
		$crRow = mysql_fetch_assoc($crResult);
		if ($crRow['current_group']!=null){
			$oldgroup = $crRow['current_group'];
			$cleanGroupQuery = "DELETE FROM groups WHERE group_id=".$oldgroup;
			$cgResult = mysql_query($cleanGroupQuery, $GLOBALS['db']) or die("grouper_62:".mysql_error());
		}
		
		// Update user current group
		$updateUserGroupQuery = "UPDATE preferences SET current_group=".$groupid." WHERE user_id=".$userid;
		$uugResult = mysql_query($updateUserGroupQuery, $GLOBALS['db']) or die("grouper_67:".mysql_error());
		
		// Update group information and add new relation
	}
	
	/**
	 * Merges the two groups. Moves all the group mmebers from group1 to group2 and merges the options.
	 * 
	 * @param unknown_type $group1
	 * @param unknown_type $group2
	 */
	public static function mergeGroup($group1, $group2){
		
		// ---Update the preferences of the moved gorup's members---
		$getG1UserQuery = "SELECT user_id FROM group_members WHERE group_id=".$group1;
		$g1uResult = mysql_query($getG1UserQuery, $GLOBALS['db']) or die("grouper_82:".mysql_error());
		$getG2UserQuery = "SELECT user_id FROM group_members WHERE group_id=".$group2;
		$g2uResult = mysql_query($getG2UserQuery, $GLOBALS['db']) or die("grouper_84:".mysql_error());
		
		while ($g1row = mysql_fetch_assoc($g1uResult)){
			$changeG1UserQuery = "UPDATE preferences SET current_group=".$group2." WHERE user_id=".$g1row['user_id'];
			$c1uResult = mysql_query($changeG1UserQuery, $GLOBALS['db']) or die("grouper_88:".mysql_error());
			$changeG1MemberQuery = "UPDATE group_members SET group_id=".$group2." WHERE user_id=".$g1row['user_id'];
			$c1mResult = mysql_query($changeG1MemberQuery, $GLOBALS['db']) or die("grouper_90:".mysql_error());
		}
		
		// ---Merge the two groups' preferences---
		
		$getG1PrefQuery = "SELECT * FROM groups WHERE group_id=".$group1;
		$g1pResult = mysql_query($getG1PrefQuery, $GLOBALS['db']) or die("grouper_96:".mysql_error());
		$g1pRow = mysql_fetch_assoc($g1pResult);
		$getG2PrefQuery = "SELECT * FROM groups WHERE group_id=".$group1;
		$g2pResult = mysql_query($getG2PrefQuery, $GLOBALS['db']) or die("grouper_99:".mysql_error());
		$g2pRow = mysql_fetch_assoc($g2pResult);
		// Merge cost options
		$gpmax = min($g1pRow['price_max'],$g2pRow['price_max']);
		$gpmin = max($g1pRow['price_min'],$g2pRow['price_min']);
		// Merge cuisine options
		$c1merge = (int)($g1pRow['cuisine_1'] && $g2pRow['cuisine_1']);
		$c2merge = (int)($g1pRow['cuisine_2'] && $g2pRow['cuisine_2']);
		$c3merge = (int)($g1pRow['cuisine_3'] && $g2pRow['cuisine_3']);
		$c4merge = (int)($g1pRow['cuisine_4'] && $g2pRow['cuisine_4']);
		$c5merge = (int)($g1pRow['cuisine_5'] && $g2pRow['cuisine_5']);
		$c6merge = (int)($g1pRow['cuisine_6'] && $g2pRow['cuisine_6']);
		$c7merge = (int)($g1pRow['cuisine_7'] && $g2pRow['cuisine_7']);
		$c8merge = (int)($g1pRow['cuisine_8'] && $g2pRow['cuisine_8']);
		$c9merge = (int)($g1pRow['cuisine_9'] && $g2pRow['cuisine_9']);
		$c10merge = (int)($g1pRow['cuisine_10'] && $g2pRow['cuisine_10']);
		$c11merge = (int)($g1pRow['cuisine_11'] && $g2pRow['cuisine_11']);
		$c12merge = (int)($g1pRow['cuisine_12'] && $g2pRow['cuisine_12']);
		// Query
		$updateGroupQuery = "UPDATE groups SET cuisine_1=".$c1merge.",cuisine_2=".$c2merge.",cuisine_3=".$c3merge.",cuisine_4="
		.$c4merge.",cuisine_5=".$c5merge.",cuisine_6=".$c6merge.",cuisine_7=".$c7merge.",cuisine_8=".$c8merge.",cuisine_9="
		.$c9merge.",cuisine_10=".$c10merge.",cuisine_11=".$c11merge.",cuisine_12=".$c12merge.",price_max=".$gpmax.",price_max=".
		$gpmin." WHERE group_id=".group2;
		$ugResult = mysql_query($updateGroupQuery, $GLOBALS['db']) or die("grouper_122:".mysql_error());
		
		// ---Remove the old group---
		$removeQuery = "DELETE FROM groups WHERE group_id=".$group1;
		$remResult = mysql_query($removeQuery, $GLOBALS['db']) or die("grouper_126:".mysql_error());
		
	}
	
	/**
	 * Removes a user from the group. All entries related to the user should be removed.
	 * @param unknown_type $userid
	 * @param unknown_type $groupid
	 */
	public static function removeUser($userid, $groupid){
		
	}

	
	/**
	 * Function to be called when receiving an active call to invite someone to the user's group.
	 * Starts a vote for everyone already in the group, then echo the result back to the request maker.
	 * If the vote passes, automatically invoke the sequence to request join.
	 * @param unknown_type $userid
	 * @param unknown_type $reqgroupid The group being invited
	 */
	public static function requestToInvite($userid, $reqgroupid){
		
		$gNumberQuery = "SELECT current_group FROM preferences WHERE user_id=".$userid;
		$gnResult = mysql_query($gNumberQuery, $GLOBALS['db']) or die("grouper_150:".mysql_error());
		$gnRow = mysql_fetch_assoc($gnResult);
		$hostgroupid = $gnRow['current_group'];
		
		$numberQuery = "SELECT * FROM group_members WHERE group_id=".$hostgroupid;
		$nqResult = mysql_query($numberQuery, $GLOBALS['db']) or die("grouper_155:".mysql_error());
		$numMember = mysql_num_rows($nqResult);
		$groupArray = array();
		
		if ($numMember==0){
			return -1;
		}
		$newVoteQuery = "INSERT INTO voting_invite (group_id, max_votes, yes_votes, no_votes) VALUES (".$hostgroupid.",".$numMember.",1,0)";
		$nvResult = mysql_query($newVoteQuery, $GLOBALS['db']) or die("grouper_163:".mysql_error());
		
		$setTargetQuery = "UPDATE groups SET invite_group_id=".$reqgroupid.",voting_invite=1 WHERE group_id=".$hostgroupid;
		$stResult = mysql_query($setTargetQuery, $GLOBALS['db']) or die("grouper_166:".mysql_error());
		
		while ($nqRow = mysql_fetch_assoc($nqResult)){
			$ruid = $nqRow['user_id'];
			if ($ruid==$userid){
				$mvQuery = "UPDATE group_members SET invite_vote=3 WHERE user_id=".$ruid;
			}else{
				$mvQuery = "UPDATE group_members SET invite_vote=1 WHERE user_id=".$ruid;
			}
			$mvResult = mysql_query($mvQuery, $GLOBALS['db']) or die("grouper_175:".mysql_error());
			
			$nameQuery = "SELECT user_id, first_name, last_name FROM profiles WHERE user_id=".$ruid;
			$nmResult = mysql_query($nameQuery, $GLOBALS['db']) or die("grouper_178:".mysql_error());
			$nmRow = mysql_fetch_assoc($nmResult);
			$memberArray = array();
			$memberArray['id'] = $nmRow['user_id'];
			$memberArray['firstname'] = $nmRow['first_name'];
			$memberArray['lastname'] = $nmRow['last_name'];
			$groupArray[] = $memberArray;
		}
		
	}
	
	
	/**
	 * Function to be called when a certain group's invite vote passed the success threshold.
	 * Removes all voting related entries from the originating group, then sets up
	 * the invited group's entries so that their members would receive the notification
	 * on the next "get".
	 * @param int $groupid The group that finished the invite vote
	 * @param boolean $success
	 */
	public static function transferInviteToJoin($groupid, $success){
		$decision = 'D';
		if ($success){
			// Add vote to the invited group
			$targetQuery = "SELECT invite_group_id FROM groups WHERE group_id=".$groupid;
			$tResult = mysql_query($targetQuery, $GLOBALS['db']) or die("grouper_203:".mysql_error());
			$tRow = mysql_fetch_assoc($tResult);
			$targetGroup = $tRow['invite_group_id'];
			
			$numberQuery = "SELECT * FROM group_members WHERE group_id=".$targetGroup;
			$nqResult = mysql_query($numberQuery, $GLOBALS['db']) or die("grouper_208:".mysql_error());
			$numMember = mysql_num_rows($nqResult);
			
			$newVoteQuery = "INSERT INTO voting_join (group_id, max_votes, yes_votes, no_votes) VALUES (".$targetGroup.",".$numMember.",0, 0)";
			$nvResult = mysql_query($newVoteQuery, $GLOBALS['db']) or die("grouper_212:".mysql_error());
			
			$setTargetQuery = "UPDATE groups SET join_group_id=".$groupid.", voting_join=1 WHERE group_id=".$targetGroup;
			$stResult = mysql_query($setTargetQuery, $GLOBALS['db']) or die("grouper_215:".mysql_error());
			
			$mvQuery = "UPDATE group_members SET join_vote=1 WHERE group_id=".$targetGroup;
			$mvResult = mysql_query($mvQuery, $GLOBALS['db']) or die("grouper_218:".mysql_error());
			$decision = 'A';
		}
		// Remove/edit vote in the originating group
		$removeVoteQuery = "DELETE FROM voting_invite WHERE group_id=".$groupid;
		$rvResult = mysql_query($removeVoteQuery, $GLOBALS['db']) or die("grouper_233:".mysql_error());
		$giveResultQuery = "UPDATE groups SET voting_result='".$decision."' WHERE group_id=".$groupid;
		$grResult = mysql_query($giveResultQuery);
		$updateGroupQuery = "UPDATE groups SET voting_invite=2 WHERE group_id=".$groupid;
		$ugResult = mysql_query($updateGroupQuery, $GLOBALS['db']) or die("grouper_227:".mysql_error());
		$prepareNotQuery = "UPDATE group_members SET invite_vote=1 WHERE group_id=".$groupid;
		$pnResult = mysql_query($prepareNotQuery, $GLOBALS['db']) or die("grouper_229:".mysql_error());
		
		
	}
	
	/**
	 * Function to be called when a certain group's join vote passed the success threshold.
	 * Removes all voting related entries from the originating group, then sets up
	 * both group's entries so that their members would receive the notification
	 * on the next "get".
	 * @param unknown_type $groupid
	 */
	public static function cleanUpJoin($groupid, $success){
		// Remove/edit vote in the originating group
		$decision = ($success)?"A":"D";
		if ($success){
			$getOtherQuery = "SELECT join_group_id FROM groups WHERE group_id=".$groupid;
			$goResult = mysql_query($getOtherQuery, $GLOBALS['db']) or die("grouper_246:".mysql_error());
			$goRow = mysql_fetch_assoc($goresult);
			$otherGroup = $goRow['join_group_id'];
			$updateOtherGroupQuery = "UPDATE groups SET voting_invite=2 WHERE group_id=".$otherGroup;
			$uogResult = mysql_query($updateOtherGroupQuery, $GLOBALS['db']) or die("grouper_250:".mysql_error());
			$updateOtherMembersQuery = "UPDATE group_members SET join_vote=1 WHERE group_id=".$otherGroup;
			$uomResult = mysql_query($updateOtherMembersQuery, $GLOBALS['db']) or die("grouper_252".mysql_error());
		}
		$removeVoteQuery = "DELETE FROM voting_join WHERE group_id=".$groupid;
		$rvResult = mysql_query($removeVoteQuery, $GLOBALS['db']) or die("grouper_255:".mysql_error());
		$giveResultQuery = "UPDATE groups SET voting_result='".$decision."'";
		$grResult = mysql_query($giveResultQuery);
		$updateGroupQuery = "UPDATE groups SET voting_join=2 WHERE group_id=".$groupid;
		$ugResult = mysql_query($updateGroupQuery, $GLOBALS['db']) or die("grouper_259:".mysql_error());
		$prepareNotQuery = "UPDATE group_members SET join_vote=1 WHERE group_id=".$groupid;
		$pnResult = mysql_query($prepareNotQuery, $GLOBALS['db']) or die("grouper_261:".mysql_error());
	}
	
	/**
	 * Function to be called when the user submits a invite vote.
	 * Process the info, and make changes to the database accordingly.
	 * @param int $userid
	 * @param string $yesvote
	 */
	public static function processInviteVote($userid, $yesvote){
		$groupQuery = "SELECT current_group FROM preferences WHERE user_id=".$userid;
		$gResult = mysql_query($groupQuery, $GLOBALS['db']) or die("grouper_272:".mysql_error());
		$gRow = mysql_fetch_assoc($gResult);
		$groupid = $gRow['current_group'];
		
		if ($yesvote=="A"){
			$membersQuery = "UPDATE group_members SET invite_vote=3 WHERE user_id=".$userid;
			$mResult = mysql_query($membersQuery, $GLOBALS['db']);
			
			$getGroupVoteQuery = "SELECT yes_votes FROM voting_invite WHERE group_id=".$groupid;
			$ggvResult = mysql_query($getGroupVoteQuery, $GLOBALS['db']) or die("grouper_281:".mysql_error());
			$ggvRow = mysql_fetch_assoc($ggvResult);
			$currentVote = $sgvRow['yes_votes'];
			$newVote = $currentVote+1;
			
			$setGroupVoteQuery = "UPDATE voting_invite SET yes_votes=".$newVote." WHERE group_id=".$groupid;
			$sgvResult = mysql_query($setGroupVoteQuery, $GLOBALS['db']) or die("grouper_287:".mysql_error());
			
			responder::respondSimple("good");
		}elseif ($yesvote=="D"){
			$membersQuery = "UPDATE group_members SET invite_vote=3 WHERE user_id=".$userid;
			$mResult = mysql_query($membersQuery, $GLOBALS['db']);
				
			$getGroupVoteQuery = "SELECT yes_votes FROM voting_invite WHERE group_id=".$groupid;
			$ggvResult = mysql_query($getGroupVoteQuery, $GLOBALS['db']) or die("grouper_295:".mysql_error());
			$ggvRow = mysql_fetch_assoc($ggvResult);
			$currentVote = $sgvRow['no_votes'];
			$newVote = $currentVote+1;
				
			$setGroupVoteQuery = "UPDATE voting_invite SET no_votes=".$newVote." WHERE group_id=".$groupid;
			$sgvResult = mysql_query($setGroupVoteQuery, $GLOBALS['db']) or die("grouper_301:".mysql_error());
				
			responder::respondSimple("good");
		}else{
			die("illegal action");
		}
	}
	
	/**
	 * Function to be called when the user submits a join vote.
	 * Process the info, and make changes to the database accordingly.
	 * @param int $userid
	 * @param string $yesvote
	 */
	public static function processJoinVote($userid, $yesvote){
		$groupQuery = "SELECT current_group FROM preferences WHERE user_id=".$userid;
		$gResult = mysql_query($groupQuery, $GLOBALS['db']) or die("grouper_317:".mysql_error());
		$gRow = mysql_fetch_assoc($gResult);
		$groupid = $gRow['current_group'];
		
		if ($yesvote=="A"){
			$membersQuery = "UPDATE group_members SET join_vote=3 WHERE user_id=".$userid;
			$mResult = mysql_query($membersQuery, $GLOBALS['db']);
				
			$getGroupVoteQuery = "SELECT yes_votes FROM voting_join WHERE group_id=".$groupid;
			$ggvResult = mysql_query($getGroupVoteQuery, $GLOBALS['db']) or die("grouper_326:".mysql_error());
			$ggvRow = mysql_fetch_assoc($ggvResult);
			$currentVote = $sgvRow['yes_votes'];
			$newVote = $currentVote+1;
				
			$setGroupVoteQuery = "UPDATE voting_join SET yes_votes=".$newVote." WHERE group_id=".$groupid;
			$sgvResult = mysql_query($setGroupVoteQuery, $GLOBALS['db']) or die("grouper_332:".mysql_error());
				
			responder::respondSimple("good");
		}elseif ($yesvote=="D"){
			$membersQuery = "UPDATE group_members SET join_vote=3 WHERE user_id=".$userid;
			$mResult = mysql_query($membersQuery, $GLOBALS['db']);
		
			$getGroupVoteQuery = "SELECT yes_votes FROM voting_join WHERE group_id=".$groupid;
			$ggvResult = mysql_query($getGroupVoteQuery, $GLOBALS['db']) or die("grouper_340:".mysql_error());
			$ggvRow = mysql_fetch_assoc($ggvResult);
			$currentVote = $sgvRow['no_votes'];
			$newVote = $currentVote+1;
		
			$setGroupVoteQuery = "UPDATE voting_join SET no_votes=".$newVote." WHERE group_id=".$groupid;
			$sgvResult = mysql_query($setGroupVoteQuery, $GLOBALS['db']) or die("grouper_346:".mysql_error());
		
			responder::respondSimple("good");
		}else{
			die("illegal action");
		}
	}
	
	/**
	 * Create an array "notification" according to the parameters.
	 * Types:
	 * -inviteRequest: $groupid is the group being invited
	 * -joinRequest: $groupid is the group being merged with
	 * -inviteDecision: $groupid is is the group being invited
	 * -joinDecision: $groupid is the group being merged with
	 * @param int $groupid The group related with the action.
	 * @param string $type 
	 * @param unknown_type $target
	 */
	public static function makeVoteNotification($groupid, $type, $decision=null, $initiator=null){
		$n0 = array();
		$n0['groupID']=$groupid;
		$n0['type']=$type;
		$n0['decisionType']=$decision;
		$n0['group']=grouper::makeMemberArray($groupid, false);
		if ($initiator!=null){
			$nameQuery = "SELECT user_id, first_name, last_name FROM profiles WHERE user_id=".$initiator;
			$nmResult = mysql_query($nameQuery, $GLOBALS['db']) or die("grouper_373:".mysql_error());
			$nmRow = mysql_fetch_assoc($nmResult);
			$memberArray = array();
			$memberArray['id'] = $nmRow['user_id'];
			$memberArray['firstname'] = $nmRow['first_name'];
			$memberArray['lastname'] = $nmRow['last_name'];
			$n0['initiator'] = $memberArray;
		}
		
		return $n0;
	}
	
	/**
	 * Create an array [group] that will be returned to the frontend.
	 * @param int $groupid
	 */
	public static function makeGroupArray($groupid, $userid){
		$groupArray = array();
		$groupQuery = "SELECT * FROM groups WHERE group_id=".$groupid;
		$groupResult = mysql_query($groupQuery, $GLOBALS['db']) or die("grouper_392:".mysql_error());
		$gRow = mysql_fetch_assoc($groupResult);
		$groupArray['groupID'] = $groupid;
		$groupArray['foodtype'] = matcher::getCuisineList($gRow, 3);
		$groupArray['pricemin'] = $gRow['price_min'];
		$groupArray['pricemax'] = $gRow['price_max'];
		$groupArray['avgdist'] = matcher::getAvgDist($userid, $groupid);  
		$groupArray['capacity'] = (($gRow['capacity']==2)?2:5);
		
		$numberQuery = "SELECT user_id FROM group_members WHERE group_id=".$groupid;
		$nqResult = mysql_query($numberQuery, $GLOBALS['db']) or die("grouper_402:".mysql_error());
		$numMember = mysql_num_rows($nqResult);
		
		$groupArray['nop'] = $numMember;
		return $groupArray;
		
	}
	
	/**
	 * Create an array [member] that will be returned to the frontend.
	 * @param int $groupid
	 * @param boolean $completeinfo
	 */
	public static function makeMemberArray($groupid, $completeinfo=true){
		$membersArray = array();
		$numberQuery = "SELECT * FROM group_members WHERE group_id=".$groupid;
		$nqResult = mysql_query($numberQuery, $GLOBALS['db']) or die("grouper_418:".mysql_error());
		$numMember = mysql_num_rows($nqResult);
		
		if ($completeinfo){
			while ($nqRow = mysql_fetch_assoc($nqResult)){
				$memberid = $nqRow['user_id'];
				$nameQuery = "SELECT first_name, last_name, gender, photolink, thumblink FROM profiles WHERE user_id=".$memberid;
				$nmResult = mysql_query($nameQuery, $GLOBALS['db']) or die("grouper_424:".mysql_error());
				$nmRow = mysql_fetch_array($nmResult);
				$memberArray['id']=$memberid;
				$memberArray['firstname'] = $nmRow['first_name'];
				$memberArray['lastname'] = $nmRow['last_name'];
				$memberArray['photolink'] = $nmRow['photolink'];
				$memberArray['thumblink'] = $nmRow['thumblink'];
				$memberArray['gender'] = $nmRow['gender'];
				$foodTypeQuery = "SELECT * FROM foodtype WHERE user_id=".$memberid;
				$ftResult = mysql_query($foodTypeQuery, $GLOBALS['db']) or die("grouper_419:".mysql_error());
				$ftRow = mysql_fetch_assoc($ftResult);
				$memberArray['foodtype'] = matcher::getCuisineList($ftRow, 12);
				$membersArray[] = $memberArray;
			}
			
		}else{
			while ($nqRow = mysql_fetch_assoc($nqResult)){
				$memberid = $nqRow['user_id'];
				$nameQuery = "SELECT user_id, first_name, last_name FROM profiles WHERE user_id=".$memberid;
				$nmResult = mysql_query($nameQuery, $GLOBALS['db']) or die("grouper_437:".mysql_error());
				$nmRow = mysql_fetch_assoc($nmResult);
				$memberArray = array();
				$memberArray['id'] = $nmRow['user_id'];
				$memberArray['firstname'] = $nmRow['first_name'];
				$memberArray['lastname'] = $nmRow['last_name'];
				$membersArray[] = $memberArray;
			}
		}
		return $membersArray;
	}

}

