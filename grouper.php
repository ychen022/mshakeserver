<?php
class grouper{
	public static function startGroup($initUser){
		require_once("db.php");
		
// 		$db = mysql_connect("localhost", "root", "") or die(mysql_error());
		
// 		mysql_select_db('mealshake', $db);
		
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
			
			$query4 = "UPDATE preferences SET current_group=".$groupid." WHERE user_id=".$initUser;
			$result4 = mysql_query($query4, $db) or die(mysql_error());
		}
	}
	
	/**
	 * Add a user to the group. The user should be able to pass the matching checks before being added.
	 * DEPRECATED. Use mergeGroup() instead.
	 * @param unknown_type $userid
	 * @param unknown_type $groupid
	 */
	public static function addUser($userid, $groupid){
		require_once("db.php");
		
		// Remove old member relation
		$curRelationQuery = "SELECT current_group FROM preferences WHERE user_id=".$userid;
		$crResult = mysql_query($curRelationQuery, $db) or die(mysql_error());
		$crRow = mysql_fetch_assoc($crResult);
		if ($crRow['current_group']!=null){
			$oldgroup = $crRow['current_group'];
			$cleanGroupQuery = "DELETE FROM groups WHERE group_id=".$oldgroup;
			$cgResult = mysql_query($cleanGroupQuery, $db) or die(mysql_error());
		}
		
		// Update user current group
		$updateUserGroupQuery = "UPDATE preferences SET current_group=".$groupid." WHERE user_id=".$userid;
		$uugResult = mysql_query($updateUserGroupQuery, $db) or die(mysql_error());
		
		// Update group information and add new relation
	}
	
	/**
	 * Merges the two groups. Moves all the group mmebers from group1 to group2 and merges the options.
	 * 
	 * @param unknown_type $group1
	 * @param unknown_type $group2
	 */
	public static function mergeGroup($group1, $group2){
		require_once ('db.php');
		
		// ---Update the preferences of the moved gorup's members---
		$getG1UserQuery = "SELECT user_id FROM group_members WHERE group_id=".$group1;
		$g1uResult = mysql_query($getG1UserQuery, $db) or die(mysql_error());
		$getG2UserQuery = "SELECT user_id FROM group_members WHERE group_id=".$group2;
		$g2uResult = mysql_query($getG2UserQuery, $db) or die(mysql_error());
		
		while ($g1row = mysql_fetch_assoc($g1uResult)){
			$changeG1UserQuery = "UPDATE preferences SET current_group=".$group2." WHERE user_id=".$g1row['user_id'];
			$c1uResult = mysql_query($changeG1UserQuery, $db) or die(mysql_error());
			$changeG1MemberQuery = "UPDATE group_members SET group_id=".$group2." WHERE user_id=".$g1row['user_id'];
			$c1mResult = mysql_query($changeG1MemberQuery, $db) or die(mysql_error());
		}
		
		// ---Merge the two groups' preferences---
		
		$getG1PrefQuery = "SELECT * FROM groups WHERE group_id=".$group1;
		$g1pResult = mysql_query($getG1PrefQuery, $db) or die(mysql_error());
		$g1pRow = mysql_fetch_assoc($g1pResult);
		$getG2PrefQuery = "SELECT * FROM groups WHERE group_id=".$group1;
		$g2pResult = mysql_query($getG2PrefQuery, $db) or die(mysql_error());
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
		$ugResult = mysql_query($updateGroupQuery, $db) or die(mysql_error());
		
		// ---Remove the old group---
		$removeQuery = "DELETE FROM groups WHERE group_id=".$group1;
		$remResult = mysql_query($removeQuery, $db) or die(mysql_error());
		
	}
	
	/**
	 * Removes a user from the group. All entries related to the user should be removed.
	 * @param unknown_type $userid
	 * @param unknown_type $groupid
	 */
	public static function removeUser($userid, $groupid){
		
	}
	
	/**
	 * Function to be called when receiving an active call to join a group.
	 * Starts a vote for everyone already in the group, then echo the result back to the request maker.
	 * @param unknown_type $userid
	 * @param unknown_type $groupid
	 */
	public static function requestToJoin($reqgroupid, $hostgroupid){
		require_once('db.php');
		$numberQuery = "SELECT * FROM group_members WHERE group_id=".$hostgroupid;
		$nqResult = mysql_query($numberQuery, $db) or die(mysql_error());
		$numMember = mysql_num_rows($nqResult);
		$groupArray = array();
		
		if ($numMember==0){
			return -1;
		}
		$newVoteQuery = "INSERT INTO voting_join (group_id, max_votes, yes_votes, no_votes) VALUES (".$hostgroupid.",".$numMember.",0, 0)";
		$nvResult = mysql_query($newVoteQueryQuery, $db) or die(mysql_error());
		
		$setTargetQuery = "UPDATE groups SET join_group_id=".$reqgroupid;
		$stResult = mysql_query($setTargetQuery, $db) or die(mysql_error());
		
		while ($nqRow = mysql_fetch_assoc($nqResult)){
			$ruid = $nqResult['user_id'];
			$mvQuery = "UPDATE group_member SET join_vote=1 WHERE user_id=".$ruid;
			$mvResult = mysql_query($mvQuery, $db) or die(mysql_error());
			
			$nameQuery = "SELECT user_id, first_name, last_name FROM profiles WHERE user_id=".$ruid;
			$nmResult = mysql_query($nameQuery, $db) or die(mysql_error());
			$nmRow = mysql_fetch_assoc($nmResult);
			$memberArray = array();
			$memberArray['id'] = $nmRow['user_id'];
			$memberArray['firstname'] = $nmRow['first_name'];
			$memberArray['lastname'] = $nmRow['last_name'];
			$groupArray[] = $memberArray;
		}
		
		$checkVoteQuery = "SELECT max_votes, yes_votes, no_votes FROM voting_join WHERE group_id=".$hostgroupid;
		while(true){
			$cvResult = mysql_query($checkVoteQuery, $db) or die(mysql_error());
			$cvRow = mysql_fetch_assoc($cvResult);
			if ($cvRow['yes_votes']/$cvRow['max_votes']>0.6){
				grouper::mergeGroup($reqgroupid, $hostgroupid);
				$nArray = array();
				$n0 = array();
				$n0['type'] = 'joinDecision';
				$n0['decisionType'] = 'A';
				$n0['group'] = $groupArray;
				$n0['groupID'] = $hostgroupid;
				$nArray[] = $n0;
				matcher::makeGetResponse($nArray, matcher::getMatchedGroups($hostgroupid));
			}else if ($cvRow['no_votes']/$cvRow['max_votes']>0.6){
				$nArray = array();
				$n0 = array();
				$n0['type'] = 'joinDecision';
				$n0['decisionType'] = 'D';
				$n0['group'] = $groupArray;
				$n0['groupID'] = $hostgroupid;
				$nArray[] = $n0;
				matcher::makeGetResponse($nArray, matcher::getMatchedGroups($reqgroupid));
			}
			sleep(1);
		}
	}
	
	/**
	 * Function to be called when receiving an active call to invite someone to the user's group.
	 * Starts a vote for everyone already in the group, then echo the result back to the request maker.
	 * If the vote passes, automatically invoke the sequence to request join.
	 * @param unknown_type $userid
	 * @param unknown_type $groupid
	 */
	public static function requestToInvite($userid, $reqgroupid, $hostgroupid){
		require_once('db.php');
		$numberQuery = "SELECT * FROM group_members WHERE group_id=".$hostgroupid;
		$nqResult = mysql_query($numberQuery, $db) or die(mysql_error());
		$numMember = mysql_num_rows($nqResult);
		$groupArray = array();
		
		if ($numMember==0){
			return -1;
		}
		$newVoteQuery = "INSERT INTO voting_invite (group_id, max_votes, yes_votes, no_votes) VALUES (".$hostgroupid.",".$numMember.",1,0)";
		$nvResult = mysql_query($newVoteQueryQuery, $db) or die(mysql_error());
		
		$setTargetQuery = "UPDATE groups SET invite_group_id=".$reqgroupid;
		$stResult = mysql_query($setTargetQuery, $db) or die(mysql_error());
		
		while ($nqRow = mysql_fetch_assoc($nqResult)){
			$ruid = $nqResult['user_id'];
			if ($ruid==$userid){
				$mvQuery = "UPDATE group_member SET invite_vote=2 WHERE user_id=".$ruid;
			}else{
				$mvQuery = "UPDATE group_member SET invite_vote=1 WHERE user_id=".$ruid;
			}
			$mvResult = mysql_query($mvQuery, $db) or die(mysql_error());
			
			$nameQuery = "SELECT user_id, first_name, last_name FROM profiles WHERE user_id=".$ruid;
			$nmResult = mysql_query($nameQuery, $db) or die(mysql_error());
			$nmRow = mysql_fetch_assoc($nmResult);
			$memberArray = array();
			$memberArray['id'] = $nmRow['user_id'];
			$memberArray['firstname'] = $nmRow['first_name'];
			$memberArray['lastname'] = $nmRow['last_name'];
			$groupArray[] = $memberArray;
		}
		
		$checkVoteQuery = "SELECT max_votes, yes_votes, no_votes FROM voting_invite WHERE group_id=".$hostgroupid;
		while(true){
			$cvResult = mysql_query($checkVoteQuery, $db) or die(mysql_error());
			$cvRow = mysql_fetch_assoc($cvResult);
			if ($cvRow['yes_votes']/$cvRow['max_votes']>0.6){
				grouper::mergeGroup($reqgroupid, $hostgroupid);
				$nArray = array();
				$n0 = array();
				$n0['type'] = 'inviteDecision';
				$n0['decisionType'] = 'A';
				$n0['group'] = $groupArray;
				$nArray[] = $n0;
				matcher::makeGetResponse($nArray, matcher::getMatchedGroups($hostgroupid));
			}else if ($cvRow['no_votes']/$cvRow['max_votes']>0.6){
				$nArray = array();
				$n0 = array();
				$n0['type'] = 'inviteDecision';
				$n0['decisionType'] = 'D';
				$n0['group'] = $groupArray;
				$nArray[] = $n0;
				matcher::makeGetResponse($nArray, matcher::getMatchedGroups($reqgroupid));
			}
			sleep(1);
		}
	}
	
	/**
	 * Function to be called when a vote should be started for the frontend.
	 * @param unknown_type $appid
	 * @param unknown_type $voterid
	 */
	public static function createVote($appid, $voterid){
		
	}
	
	/**
	 * Function to be called when the user submits a vote.
	 * Process the info, and make changes to the database accordingly.
	 * @param unknown_type $userid
	 * @param unknown_type $type
	 * @param unknown_type $value
	 */
	public static function processVote($userid, $type, $value){
		
	}
	
	/**
	 * Create an array "notification" according to the parameters.
	 * Types:
	 * -inviteRequest: $groupid is the group being invited
	 * -joinRequest: $groupid is the group being merged with
	 * -inviteDecision: $groupid is the initiator's group
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
			$nmResult = mysql_query($nameQuery, $db) or die(mysql_error());
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
	public static function makeGroupArray($groupid){
		$groupArray = array();
		$groupQuery = "SELECT * FROM groups WHERE group_id=".$groupid;
		$groupResult = mysql_query($groupQuery, $db) or die(mysql_error());
		$gRow = mysql_fetch_assoc($groupResult);
		$groupArray['id'] = $groupid;
		$groupArray['foodtype'] = matcher::getCuisineList($gRow, 3);
		$groupArray['pricemin'] = $gRow['price_min'];
		$groupArray['pricemax'] = $gRow['price_max'];
		$groupArray['avgdist'] = 10;  //TODO fill in average distance
		$groupArray['capacity'] = (($gsrow['capacity']==2)?2:5);
		
		$numberQuery = "SELECT user_id FROM group_members WHERE group_id=".$hostgroupid;
		$nqResult = mysql_query($numberQuery, $db) or die(mysql_error());
		$numMember = mysql_num_rows($nqResult);
		
		$groupArray['nop'] = $numMember;
		return $groupArray;
		
	}
	
	/**
	 * Create an array [member] that will be returned to the frontend.
	 * @param int $groupid
	 * @param boolean $completeinfo
	 */
	public static function makeMemberArray($groupid, $completeinfo){
		$membersArray = array();
		$numberQuery = "SELECT * FROM group_members WHERE group_id=".$hostgroupid;
		$nqResult = mysql_query($numberQuery, $db) or die(mysql_error());
		$numMember = mysql_num_rows($nqResult);
		
		if ($completeinfo){
			while ($nqRow = mysql_fetch_assoc($nqResult)){
				$nameQuery = "SELECT (first_name, last_name, gender, photolink) FROM profiles WHERE user_id=".$memberid;
				$nmResult = mysql_query($namequery, $db) or die(mysql_error());
				$nmRow = mysql_fetch_array($nmResult);
				$memberArray['id']=$memberid;
				$memberArray['firstname'] = $iprow['first_name'];
				$memberArray['lastname'] = $iprow['last_name'];
				$memberArray['photolink'] = $iprow['photolink'];
				$memberArray['gender'] = $iprow['gender'];
				$membersArray[] = $memberArray;
			}
			
		}else{
			while ($nqRow = mysql_fetch_assoc($nqResult)){
				$nameQuery = "SELECT user_id, first_name, last_name FROM profiles WHERE user_id=".$ruid;
				$nmResult = mysql_query($nameQuery, $db) or die(mysql_error());
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

