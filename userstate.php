<?php

class userstate{
	public static function login($request){
		$un = $request['username'];
		$pw = $request['password'];
		$query = "SELECT * from users WHERE username='" . mysql_real_escape_string($un) . "'";
		$result = mysql_query($query, $db) or die(mysql_error());
		$row = mysql_fetch_assoc($result);
		if ($pw == $row["PASSWORD"]) {
			return $row["user_id"];
		}
		else {
			return -1;
		}
	}
	
	public static function signup($request){
		
	}
	
	public static function logout(){
		
	}
}