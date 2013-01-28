<?php
session_start();

require_once 'userstate.php';
require_once 'matcher.php';
require_once 'grouper.php';
require_once 'preferences.php';
require_once 'responder.php';
require_once('db.php');


if (!isset($_SESSION['log'])){
	$_SESSION['log'] = 0;  //0 for not logged in, 1 for logged in
}

if (!isset($_SESSION['shaking'])){
	$_SESSION['shaking'] = 0;  //0 for not logged in, 1 for logged in
}

if (isset($_POST['action'])){
	$action = $_POST["action"];
	if ($action=='login'){
		if ($_SESSION['log']==0){
			$result = userstate::login($_POST['username'],$_POST['password']);
			$_SESSION['user']=$result;
			$_SESSION['log']=($result==0)?0:1;  
		}else{
			//echo $_SESSION['log'];
			// notify the user that it's an illegal action
	// 		$result = userstate::login($_POST['username'],$_POST['password']);
	// 		$_SESSION['user']=$result;
	// 		$_SESSION['log']==1;
		}
	}elseif ($action=='checkusername'){
		userstate::checkUserName($_POST['username']);
	}elseif ($action=='signup'){
		if ($_SESSION['log']==0){
			$result = userstate::signup($_POST);
		}
	}elseif ($action=='logout'){
		//echo $_SESSION['log'];
		if ($_SESSION['log']==1){
			if ($_SESSION['shaking']==1){
				matcher::stopShaking($_SESSION['user']);
			}
			userstate::logout($_SESSION['user']);
			session_destroy();
		}
	}elseif ($action=='init' && $_SESSION['log']==1){
		if ($_SESSION['shaking']==1){
			$initres = userstate::makeInitAgainResponse($_SESSION['user']);
		}else{
			//$initres = userstate::makeInitAgainResponse($_SESSION['user']);  //TODO debug
			$initres = userstate::makeInitResponse($_SESSION['user']);
		}
		responder::respondJson($initres);
	}elseif ($action=='editpref' && $_SESSION['log']==1){
		preferences::editPreferences($_SESSION['user'], $_POST);
	}elseif ($action=='getpref' && $_SESSION['log']==1){
		preferences::getPreferences($_SESSION['user']);
	}elseif ($action=='startmatch' && $_SESSION['log']==1){
		if ($_SESSION['shaking']==0){
			$_SESSION['shaking']=1;
			preferences::editPreferences($_SESSION['user'], $_POST);
			matcher::startShaking($_SESSION['user']);
			$matchArray = matcher::refreshMatch($_SESSION['user']);
			$returnArray=matcher::getGetResult($_SESSION['user']);
			$returnArray['match'] = $matchArray['match'];
			responder::respondJson($returnArray);
		}else{
			// notify the user that it's an illegal action
// 			preferences::editPreferences($_SESSION['user'], $_POST);
// 			matcher::startShaking($_SESSION['user']);
		}
	}elseif ($action=='endmatch' && $_SESSION['log']==1 && $_SESSION['shaking']==1){
		$_SESSION['shaking']=0;
		matcher::stopShaking($_SESSION['user']);
	}elseif ($action=='get' && $_SESSION['log']==1 && $_SESSION['shaking']==1){
		$returnArray = matcher::getGetResult($_SESSION['user']);
		responder::respondJson($returnArray);
	}elseif ($action=='refreshmatch' && $_SESSION['log']==1 && $_SESSION['shaking']==1){
		$returnArray = matcher::refreshMatch($_SESSION['user']);
		responder::respondJson($returnArray);
	}elseif ($action=='startinvite' && $_SESSION['log']==1 && $_SESSION['shaking']==1){
		grouper::requestToInvite($_SESSION['user'], $_POST['groupID']);
		responder::respondSimple('startinvite_success');
	}elseif ($action=='invitevote' && $_SESSION['log']==1 && $_SESSION['shaking']==1){
		grouper::processInviteVote($_SESSION['user'], $POST['vote']);
	}elseif ($action=='joinvote' && $_SESSION['log']==1 && $_SESSION['shaking']==1){
		grouper::processJoinVote($_SESSION['user'], $POST['vote']);
	}elseif ($action=='uploadpicture'){
		preferences::uploadProfilePic();
	}elseif ($action=='uploadthumb'){
		preferences::uploadThumbnail();
	}elseif ($action=='viewuser'){
		
	}elseif ($action=='getgroup'){
		$returnArray = array();
		$returnArray['group'] = grouper::makeGroupArray($_POST['groupID'], $_SESSION['user']);
		$returnArray['member'] = grouper::makeMemberArray($_POST['groupID'], true);
		responder::respondJson($returnArray);
	}elseif ($action=='usercheck'){
		if ($_SESSION['log']==1){
			if ($_SESSION['shaking']==1){
				matcher::stopShaking($_SESSION['user']);
			}
			userstate::logout($_SESSION['user']);
			session_destroy();
		}
	}
}
?>