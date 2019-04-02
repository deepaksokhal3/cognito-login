<?php
 
function is_group($groupname){
	$client = require 'bootstrap.php';
     return $client->getUserGroup($groupname);
}

function is_user_exist($username){
	$client = require 'bootstrap.php';
     return $client->checkUserExistInGroup($username);
}

function getToken(){
	$client = require 'bootstrap.php';

	return $client->getAssociateToken();
}