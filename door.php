<?php
session_start();

require_once 'userstate.php';
require_once 'matcher.php';
require_once 'preferences.php';
require_once 'responder.php';

$_SESSION['log'] = 0;  //0 for not logged in, 1 for logged in

$action = $_POST["action"];
if ($action=='login'){
	if ($_SESSION['log']==0){
		$result = userstate::login($_POST['username'],$_POST['password']);
		$_SESSION['user']=$result;
		$_SESSION['log']==1;
	}else{
		// notify the user that it's an illegal action
// 		$result = userstate::login($_POST['username'],$_POST['password']);
// 		$_SESSION['user']=$result;
// 		$_SESSION['log']==1;
	}
}elseif ($action=='signup'){
	if ($_SESSION['log']==0){
		$result = userstate::signup($_POST);
		if ($result!=-1 && $result!=0){ // "sign up failed"
			$_SESSION['user'] = $result;
			$_SESSION['log']==1;
		}else{
			
		}
	}else{
		// notify the user that it's an illegal action
// 		$_SESSION['user'] = $result;
// 		$_SESSION['log']==1;
	}
}elseif ($action=='logout'){
	if ($_SESSION['log']==1){
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
	if ($_SESSION['log']==1){
		preferences::editPreferences($_SESSION['user'], $_POST);
		matcher::startShaking($_SESSION['user']);
		matcher::getResult($_SESSION['user']);
	}else{
		// notify the user that it's an illegal action
		preferences::editPreferences($_SESSION['user'], $_POST);
		matcher::startShaking($_SESSION['user']);
	}
}elseif ($action=='endmatch'){
	matcher::stopShaking($_SESSION['user']);
}elseif ($action=='get'){
	matcher::getResult($_SESSION['user']);
}elseif ($action=='viewuser'){
	
}elseif ($action=='sendrequest'){
	
}