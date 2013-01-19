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
	}
}else if ($action=='signup'){
	if ($_SESSION['log']==0){
		$result = userstate::signup($_POST);
		if ($result!=-1 && $result!=0){ // "sign up failed"
			$_SESSION['user'] = $result;
			$_SESSION['log']==1;
		}else{
		}
	}else{
		// notify the user that it's an illegal action
	}
}else if ($action=='logout'){
	if ($_SESSION['log']==1){
		userstate::logout($_SESSION['user']);
		$_SESSION['log']=0;
		unset($_SESSION['user']);
	}else{
		// notify the user that it's an illegal action
	}
}else if ($action=='editpref'){
	preferences::editPreferences($_SESSION['user'], $_POST);
}else if ($action=='getpref'){
	preferences::getPreferences($_SESSION['user']);
}else if ($action=='startmatch'){
	matcher::startShaking($_SESSION['user']);
}else if ($action=='endmatch'){
	matcher::stopShaking($_SESSION['user']);
}else if ($action=='viewuser'){
	
}else if ($action=='sendrequest'){
	
}