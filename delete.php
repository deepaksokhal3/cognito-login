<?php
$client = require __DIR__ . '/lib/bootstrap.php';
try{
	$client->deleteUser($_SESSION['AccessToken']);
	header("Location:".$_SERVER['TTP_ORIGIN']."/cognito-login/members.php");
}catch(Exception $e){
	echo $e->getMessage();
}