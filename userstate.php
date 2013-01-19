<?php

require_once 'responder.php';

class userstate{
	public static function login($un, $pw){
		require("db.php");
		$query = "SELECT * FROM users WHERE username='" . mysql_real_escape_string($un) . "'";
		$result = mysql_query($query, $db) or die(mysql_error());
		$row = mysql_fetch_assoc($result);
		if ($pw == $row["password"]) {
			responder::respondSimple("login_success");   //TODO change to fit need
			return $row["user_id"];
		}
		else {
			responder::respondSimple("login fucking failed");
			return 0;
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
		
		require("db.php");
		$query1 = "INSERT INTO users (username, password) VALUES ('".mysql_real_escape_string($un)."','".mysql_real_escape_string($pw)."'";
		$result1 = mysql_query($query1, $db) or die(mysql_error());
		if (!$result1){
			return -1;
		}else{
			
			$query2 = "INSERT INTO preferences (user_id, first_name, last_name, gender, email, phone_number, address, city, state, zipcode) VALUES ('".
			LAST_INSERT_ID()."','".mysql_real_escape_string($fn)."','".mysql_real_escape_string($ln).mysql_real_escape_string($gender)."','".mysql_real_escape_string($email)."','".
			mysql_real_escape_string($phone)."','".mysql_real_escape_string($address)."','".mysql_real_escape_string($city)."','".mysql_real_escape_string($state)."','".
			mysql_real_escape_string($zipcode)."'";
			$result2 = mysql_query($query1, $db) or die(mysql_error());
			if (!result2){
				return -1;
			}else{
				responder::respondSimple("signup_success");
			}
		}
	}
	
	public static function logout($userid){
		require("matcher.php");
		matcher::stopShaking($userid);
	}
}