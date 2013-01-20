<?php

class responder{
	public static function respondSimple($message){
		echo $message;
	}
	
	public static function respondJson($messageArray){
		echo json_encode($messageArray);
	}
}