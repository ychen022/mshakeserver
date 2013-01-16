<?php
session_start();

include 'userstate.php';
include 'signup.php';
include 'matcher.php';
include 'preferences.php';

$_SESSION['log'] = 0;  //0 for not logged in, 1 for logged in

$action = $_POST['action'];
if ($action=='login'){
	if ($_SESSION['log']==0){
		userstate.login($_POST['username'],$_POST['password']);
	}else{
		// notify the user that it's an illegal action
	}
}else if ($action=='signup'){
	if ($_SESSION['log']==0){
		userstate.signup($_POST['username'],$_POST['password']);
	}else{
		// notify the user that it's an illegal action
	}
}else if ($action=='logout'){
	if ($_SESSION['log']==1){
		userstate.logout();
	}else{
		// notify the user that it's an illegal action
	}
}else if ($action=='editpref'){
	
}else if ($action=='getpref'){
	
}else if ($action=='match'){
	
}else if ($action=='viewuser'){
	
}else if ($action=='sendrequest'){
	
}
