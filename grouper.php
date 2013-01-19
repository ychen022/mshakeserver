<?php
class grouper{
	
}

class group{
	public $users;
	public $foodtype;
	
	public function __construct($initUser){
		$this->$users = array();
		$this->$users[] = $initUser;
		
	}
	
	public function addUser($user){
		$this->$users[] = $user;
	}
	
	public function removeUser($user){
		if(($key = array_search($user, $this->users)) !== false) {
	    	unset($messages[$key]);
		}
	}
	
	public function packToJson(){
		
	}
}