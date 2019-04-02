<?php
 

function is_group($groupname){
	$client = require 'bootstrap.php';
     return $client->getUserGroup($groupname);
}

function is_user_exist($username, $groupname){
	$client = require 'bootstrap.php';
	try{
		$users =  $client->buildAdminFormatedObject($client->getListUsersInGroup($groupname));
		$flag = false;
		if(count($users)>0):
			foreach($users as $user):
		 		if(trim($user['Username']) == trim($username))
		 			return $flag = trie;
			endforeach;
		endif;
	}catch(Exception $e){
		
	}
}