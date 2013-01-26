<?php
session_start();

require_once 'userstate.php';
require_once 'matcher.php';
require_once 'preferences.php';
require_once 'responder.php';

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
			$_SESSION['log']=($result==0)?0:1;  //TODO: Store this in database
		}else{
			echo $_SESSION['log'];
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
			if ($result!=-1 && $result!=0){ // "sign up failed"
				$_SESSION['user'] = $result;
				$_SESSION['log']=1;
			}else{
				
			}
		}else{
			// notify the user that it's an illegal action
	// 		$_SESSION['user'] = $result;
	// 		$_SESSION['log']==1;
		}
	}elseif ($action=='logout'){
		if ($_SESSION['log']==1){
			if ($_SESSION['shaking']==1){
				matcher::stopShaking($_SESSION['user']);
			}
			userstate::logout($_SESSION['user']);
			$_SESSION['log']=0;
			unset($_SESSION['user']);
		}else{
			// notify the user that it's an illegal action
	// 		userstate::logout($_SESSION['user']);
	// 		$_SESSION['log']=0;
	// 		unset($_SESSION['user']);
		}
	}elseif ($action=='init'){
		$initres = userstate::makeInitResponse($_SESSION['user']);
		responder::respondJson($initres);
	}elseif ($action=='editpref'){
		preferences::editPreferences($_SESSION['user'], $_POST);
	}elseif ($action=='getpref'){
		preferences::getPreferences($_SESSION['user']);
	}elseif ($action=='startmatch'){
		if ($_SESSION['log']==1 && $_SESSION['shaking']==0){
			$_SESSION['shaking']=1;
			preferences::editPreferences($_SESSION['user'], $_POST);
			matcher::startShaking($_SESSION['user']);
			matcher::getResult($_SESSION['user']);
		}else{
			// notify the user that it's an illegal action
// 			preferences::editPreferences($_SESSION['user'], $_POST);
// 			matcher::startShaking($_SESSION['user']);
		}
	}elseif ($action=='endmatch' && $_SESSION['shaking']==1){
		$_SESSION['shaking']=0;
		matcher::stopShaking($_SESSION['user']);
	}elseif ($action=='get' && $_SESSION['shaking']==1){
		matcher::getResult($_SESSION['user']);
	}elseif ($action=='vote'){
		
	}elseif ($action=='viewuser'){
		
	}elseif ($action=='sendrequest'){
		
	}elseif ($action=='getgroup'){
		
	}
}
?>