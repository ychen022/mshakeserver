<?php

require_once 'responder.php';

require_once("db.php");

class userstate{
	public static function login($un, $pw){
		$query = "SELECT * FROM users WHERE username='" . mysql_real_escape_string($un) . "'";
		$result = mysql_query($query, $GLOBALS['db']) or die(mysql_error());
		$row = mysql_fetch_assoc($result);
		if ($pw == $row["password"]) {
			responder::respondSimple("login_success");  
			//responder::respondJson(makeLoginResponse($row['user_id']));
			return $row["user_id"];
		}
		else {
			responder::respondSimple("login fucking failed");
			return 0;
		}
	}
	
	/**
	 * Function to be called when the user just logged in and sent an init request.
	 * @param unknown_type $userid
	 */
	public static function makeInitResponse($userid){
		$trueResponseArray = array();
		$trueResponseArray['wantsget']=0;
		
		$responseArray = array();
		$query = "SELECT * FROM preferences WHERE user_id=".$userid."";
		$result = mysql_query($query, $GLOBALS['db']) or die(mysql_error());
		$row = mysql_fetch_assoc($result);
	
		$responseArray['address'] = $row['address'];
		$responseArray['city'] = $row['city'];
		$responseArray['state'] = $row['state'];
		$responseArray['zipcode'] = $row['zipcode'];
		
		$responseArray['distance'] = $row['distance'];
		
		$responseArray['gender'] = $row['gender'];
		
		$responseArray['groupsize'] = $row['groupsize'];
		
		$responseArray['pricemax'] = $row['price_max'];
		$responseArray['pricemin'] = $row['price_min'];
		
		$query2 = "SELECT * FROM foodtype WHERE user_id=".$userid."";
		$result2 = mysql_query($query2, $GLOBALS['db']) or die(mysql_error());
		$row2 = mysql_fetch_assoc($result2);
		$responseTypeArray = array();
		$responseTypeArray['cuisine_1'] = $row2['cuisine_1'];
		$responseTypeArray['cuisine_2'] = $row2['cuisine_2'];
		$responseTypeArray['cuisine_3'] = $row2['cuisine_3'];
		$responseTypeArray['cuisine_4'] = $row2['cuisine_4'];
		$responseTypeArray['cuisine_5'] = $row2['cuisine_5'];
		$responseTypeArray['cuisine_6'] = $row2['cuisine_6'];
		$responseTypeArray['cuisine_7'] = $row2['cuisine_7'];
		$responseTypeArray['cuisine_8'] = $row2['cuisine_8'];
		$responseTypeArray['cuisine_9'] = $row2['cuisine_9'];
		$responseTypeArray['cuisine_10'] = $row2['cuisine_10'];
		$responseTypeArray['cuisine_11'] = $row2['cuisine_11'];
		$responseTypeArray['cuisine_12'] = $row2['cuisine_12'];
		$responseArray['type'] = $responseTypeArray;
		
		$trueResponseArray['option']=$responseArray;
		
		return $trueResponseArray;
	}
	
	/**
	 * Function to be called when a user who has already started shaking refreshes the page.
	 * @param unknown_type $userid
	 */
	public static function makeInitAgainResponse($userid){
		$toplayer = array();
		$toplayer['wantsget'] = 1;
		$optionsArray = userstate::makeInitResponse($userid);
		$getArray = array();
		$gNumberQuery = "SELECT current_group FROM preferences WHERE user_id=".$userid;
		$gnResult = mysql_query($gNumberQuery, $GLOBALS['db']) or die(mysql_error());
		$gnRow = mysql_fetch_assoc($gnResult);
		$groupid = $gnRow['current_group'];
		$groupOut = array();
		$groupOut['group'] = grouper::makeGroupArray($groupid, $userid);
		$groupOut['member'] = grouper::makeMemberArray($groupid, true);
		$getArray['group'] = $groupOut;
		$matchArray = matcher::refreshMatch($userid);
		$getArray['match'] = $matchArray['match'];
		
		$notification=array();
		
		$voteCheckQuery = "SELECT voting_invite, voting_join, invite_group_id, join_group_id FROM groups WHERE group_id=".$groupid;
		$vcResult = mysql_query($voteCheckQuery, $GLOBALS['db']) or die(mysql_error());
		$vcRow = mysql_fetch_assoc($vcResult);
		if ($vcRow){
			if ($vcRow['voting_invite']==1){ // Create notification array containing a vote
				$inviteGroupQuery = "SELECT invite_group_id FROM groups WHERE group_id=".$groupid;
				$igResult = mysql_query($inviteGroupQuery, $GLOBALS['db']) or die("getgetresult_55:".mysql_error());
				$igRow = mysql_fetch_assoc($igResult);
				$targetGroup = $igRow['invite_group_id'];
				
				$checkVoteQuery = "SELECT max_votes, yes_votes, no_votes FROM voting_invite WHERE group_id=".$groupid;
				$cvResult = mysql_query($checkVoteQuery, $GLOBALS['db']) or die(mysql_error());
				$cvRow = mysql_fetch_assoc($cvResult);
				if ($cvRow['yes_votes']/$cvRow['max_votes']>0.6){
					$notification[]=grouper::makeVoteNotification($targetGroup, "inviteDecision", 'A');
					// Setup a vote for the invited group
					grouper::transferInviteToJoin($groupid, true);
				}else if ($cvRow['no_votes']/$cvRow['max_votes']>0.6){
					$notification[]=grouper::makeVoteNotification($targetGroup, "inviteDecision", 'D');
					grouper::transferInviteToJoin($groupid, false);
				}else{
					$checkVoteStateQuery = "SELECT invite_vote FROM group_members WHERE user_id=".$userid;
					$cvsResult = mysql_query($checkVoteStateQuery, $GLOBALS['db']) or die(mysql_error());
					$cvsRow = mysql_fetch_assoc($cvsResult);
					$voteStatus = $cvsRow['invite_vote'];
					
					if ($voteStatus==2){
						$notification[] = grouper::makeVoteNotification($targetGroup, "inviteRequest", null, $userid);
					}elseif ($voteStatus==1){ //TODO debug
						$notification[] = grouper::makeVoteNotification($targetGroup, "inviteRequest", null, $userid);
					}
				}
			}
			if ($vcRow['voting_invite']==2){ // Create notification array containing a result
				$checkVoteStateQuery = "SELECT invite_vote FROM group_members WHERE user_id=".$userid;
				$cvsResult = mysql_query($checkVoteStateQuery, $GLOBALS['db']) or die(mysql_error());
				$cvsRow = mysql_fetch_assoc($cvsResult);
				$voteStatus = $cvsRow['invite_vote'];
				
				$inviteGroupQuery = "SELECT invite_group_id FROM groups WHERE group_id=".$groupid;
				$igResult = mysql_query($inviteGroupQuery, $GLOBALS['db']) or die("getgetresult_55:".mysql_error());
				$igRow = mysql_fetch_assoc($igResult);
				$targetGroup = $igRow['invite_group_id'];
			
				if ($voteStatus==1){
					$getDecisionQuery = "SELECT voting_result FROM groups WHERE group_id=".$groupid;
					$gdResult = mysql_query($getDecisionQuery, $GLOBALS['db']) or die(mysql_error());
					$gdRow = mysql_fetch_assoc($gdResult);
					$vdecision = $gdRow['voting_result'];
					$notification[] = grouper::makeVoteNotification($targetGroup, "inviteDecision", $vdecision, $userid);
					$changeVoteStateQuery = "UPDATE group_members SET invite_vote=2 WHERE user_id=".$userid;
					$cgvResult = mysql_query($changeVoteStateQuery, $GLOBALS['db']) or die(mysql_error());
				}
			}
			if ($vcRow['voting_join']==1){ // Create notification array containing a vote
				$checkVoteQuery = "SELECT max_votes, yes_votes, no_votes FROM voting_join WHERE group_id=".$groupid;
				$cvResult = mysql_query($checkVoteQuery, $GLOBALS['db']) or die(mysql_error());
				$cvRow = mysql_fetch_assoc($cvResult);
				
				$joinGroupQuery = "SELECT join_group_id FROM groups WHERE group_id=".$groupid;
				$jgResult = mysql_query($joinGroupQuery, $GLOBALS['db']) or die("getgetresult_55:".mysql_error());
				$jgRow = mysql_fetch_assoc($jgResult);
				$targetGroup = $jgRow['join_group_id'];
				if ($cvRow['yes_votes']/$cvRow['max_votes']>0.6){
					$notification[]=grouper::makeVoteNotification($targetGroup, "joinDecision", 'A');
					// Setup a vote for the invited group
					grouper::cleanUpJoin($groupid, true);
				}else if ($cvRow['no_votes']/$cvRow['max_votes']>0.6){
					$notification[]=grouper::makeVoteNotification($targetGroup, "joinDecision", 'D');
					grouper::cleanUpJoin($groupid, false);
				}else{
					$checkVoteStateQuery = "SELECT join_vote FROM group_members WHERE user_id=".$userid;
					$cvsResult = mysql_query($checkVoteStateQuery, $GLOBALS['db']) or die(mysql_error());
					$cvsRow = mysql_fetch_assoc($cvsResult);
					$voteStatus = $cvsRow['join_vote'];
						
					if ($voteStatus==2){
						$notification[] = grouper::makeVoteNotification($targetGroup, "joinRequest", null);
					}elseif ($voteStatus==1){ //TODO debug
						$notification[] = grouper::makeVoteNotification($targetGroup, "joinRequest", null);
					}
				}
			}else if ($vcRow['voting_join']==2){ // Create notification array containing a result
				$checkVoteStateQuery = "SELECT join_vote FROM group_members WHERE user_id=".$userid;
				$cvsResult = mysql_query($checkVoteStateQuery, $GLOBALS['db']) or die(mysql_error());
				$cvsRow = mysql_fetch_assoc($cvsResult);
				$voteStatus = $cvsRow['join_vote'];
				
				$joinGroupQuery = "SELECT join_group_id FROM groups WHERE group_id=".$groupid;
				$jgResult = mysql_query($joinGroupQuery, $GLOBALS['db']) or die("getgetresult_55:".mysql_error());
				$jgRow = mysql_fetch_assoc($jgResult);
				$targetGroup = $jgRow['join_group_id'];
			
				if ($voteStatus==1){
					$getDecisionQuery = "SELECT join_group_id, voting_result FROM groups WHERE group_id=".$groupid;
					$gdResult = mysql_query($getDecisionQuery, $GLOBALS['db']) or die(mysql_error());
					$gdRow = mysql_fetch_assoc($gdResult);
					$vdecision = $gdRow['voting_result'];
					$otherGroup = $gdRow['join_group_id'];
					$notification[] = grouper::makeVoteNotification($otherGroup, "joinDecision", $vdecision);
					$changeVoteStateQuery = "UPDATE group_members SET join_vote=2 WHERE user_id=".$userid;
					$cgvResult = mysql_query($changeVoteStateQuery, $GLOBALS['db']) or die(mysql_error());
				}
			}
		}
		$getArray['notification'] = $notification;
		$toplayer['get'] = $getArray;
		return $toplayer;
	}
	
	public static function checkUserName($username){
		$query = "SELECT * FROM users WHERE username='".$username."'";
		$result = mysql_query($query, $GLOBALS['db']);
		$numcheck = mysql_num_rows($result);
		if ($numcheck==0){
			responder::respondSimple("good");
		}else{
			responder::respondSimple("bad");
		}
	}
	
	public static function signup($request){
		$un = $request['username'];
		$pw = $request['password'];
		$fn = $request['firstname'];
		$ln = $request['lastname'];
		$gender = $request['gender'];
		$email = $request['email'];
		$phone = $request['phone'];
		$address = $request['address'];
		$city = $request['city'];
		$state = $request['state'];
		$zipcode = $request['zipcode'];
		
		require_once("db.php");
		$query1 = "INSERT INTO users (username, password) VALUES ('".mysql_real_escape_string($un)."','".mysql_real_escape_string($pw)."')";
		$result1 = mysql_query($query1, $GLOBALS['db']) or die("q1".mysql_error());
		if (!$result1){  // NOT ACTUALLY REACHABLE ATM
			responder::respondSimple("Error inserting into database");
			return -1;
		}else{
			$useridquery = mysql_query("SELECT LAST_INSERT_ID()", $GLOBALS['db']) or die(mysql_error());
			$useridresult = mysql_fetch_assoc($useridquery);
			$userid = $useridresult['LAST_INSERT_ID()'];
			
			$query2 = "INSERT INTO profiles (user_id, first_name, last_name, gender, email, phone, address, city, state, zipcode) VALUES (".
			$userid.",'".mysql_real_escape_string($fn)."','".mysql_real_escape_string($ln)."','".mysql_real_escape_string($gender)."','".mysql_real_escape_string($email)."','".
			mysql_real_escape_string($phone)."','".mysql_real_escape_string($address)."','".mysql_real_escape_string($city)."','".mysql_real_escape_string($state)."',".
			mysql_real_escape_string($zipcode).")";
			$result2 = mysql_query($query2, $GLOBALS['db']) or die("q2".mysql_error());
			
			$query3 = "INSERT INTO preferences (user_id, address, city, state, zipcode) VALUES (".$userid.",'".
			mysql_real_escape_string($address)."','".mysql_real_escape_string($city)."','".mysql_real_escape_string($state)."','".
			mysql_real_escape_string($zipcode)."')";
			$result3 = mysql_query($query3, $GLOBALS['db']) or die("q3".mysql_error());
			
			$query4 = "INSERT INTO foodtype (user_id) VALUES (".$userid.")";
			$result4 = mysql_query($query4, $GLOBALS['db']) or die("q4".mysql_error());
			
			if (!$result2){ // NOT ACTUALLY REACHABLE ATM
				responder::respondSimple("signup_failure");
				return -1;
			}else{
				responder::respondSimple("signup_success");
			}
		}
	}
	
	public static function logout($userid){
		responder::respondSimple("logout_success");
	}
}