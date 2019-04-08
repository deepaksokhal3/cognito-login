<?php
 function is_group($groupname){
	$client = require 'bootstrap.php';
     return $client->getUserGroup($groupname);
}

function auth(){

	$client = require 'bootstrap.php';
	try{
     return (object)$client->buildFormatedObject($client->getCurrentUser($_SESSION['AccessToken']));
	}catch(Exception $e){
		return $e->getMessage();
	}
}

function get_current_user_id(){
	$client = require 'bootstrap.php';
	return  isset($_SESSION['sub_id'])? $client->decript($_SESSION['sub_id']):'';
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