<?php

//require(responder.php);

session_start();

if (isset($_POST['action'])){
	if ($_POST['action']=="signup" || $_POST['action']=="login"){
// 		$result = array();
// 		$result['address']='1 blah avenue';
// 		//....continue constructing the array
// 		// for list:
// 		$result[] = 'something'; //directly append
		$result = "signup/login";
		echo $result;
	}elseif ($_POST['action']=='logout'){
		echo "logout_success";
	}else{
		echo "nothing";
	}
}

?>