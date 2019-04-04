<?php include('view/common/header.php'); 
    $client = require __DIR__ . '/lib/bootstrap.php';
	$client->logout($_SESSION["AccessToken"]);
	 header("Location:".$_SERVER['HTTP_ORIGIN']."/cognito-login");