<?php

require_once 'responder.php';

class userstate{
	public static function login($un, $pw){
		require_once("db.php");
		$query = "SELECT * FROM users WHERE username='" . mysql_real_escape_string($un) . "'";
		$result = mysql_query($query, $db) or die(mysql_error());
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
	
	public static function makeInitResponse($userid){
		require_once("db.php");
		$responseArray = array();
		$query = "SELECT * FROM preferences WHERE user_id=".$userid."";
		$result = mysql_query($query, $db) or die(mysql_error());
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
		$result2 = mysql_query($query2, $db) or die(mysql_error());
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
		
		return $responseArray;
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
		$result1 = mysql_query($query1, $db) or die(mysql_error());
		if (!$result1){
			responder::respondSimple("Error inserting into database");
			return -1;
		}else{
			$useridquery = mysql_query("SELECT LAST_INSERT_ID()", $db) or die(mysql_error());
			$useridresult = mysql_fetch_assoc($useridquery);
			$userid = $useridresult['LAST_INSERT_ID()'];
			
			$query2 = "INSERT INTO profiles (user_id, first_name, last_name, gender, email, phone, address, city, state, zipcode) VALUES ('".
			$userid."','".mysql_real_escape_string($fn)."','".mysql_real_escape_string($ln)."','".mysql_real_escape_string($gender)."','".mysql_real_escape_string($email)."','".
			mysql_real_escape_string($phone)."','".mysql_real_escape_string($address)."','".mysql_real_escape_string($city)."','".mysql_real_escape_string($state)."','".
			mysql_real_escape_string($zipcode)."')";
			$result2 = mysql_query($query2, $db) or die(mysql_error());
			
			$query3 = "INSERT INTO preferences (user_id, address, city, state, zipcode) VALUES ('".
			mysql_real_escape_string($address)."','".mysql_real_escape_string($city)."','".mysql_real_escape_string($state)."','".
			mysql_real_escape_string($zipcode)."')";
			$result3 = mysql_query($query3, $db) or die(mysql_error());
			
			if (!$result2){
				responder::respondSimple("Error inserting into database");
				return -1;
			}else{
				//responder::respondSimple("signup_success");
				return userstate::login($un, $pw);
			}
		}
	}
	
	public static function logout($userid){
		require_once("matcher.php");
		matcher::stopShaking($userid);
		responder::respondSimple("logout_success");
	}
}