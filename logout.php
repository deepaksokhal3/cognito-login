<?php include('view/common/header.php'); 
    $client = require __DIR__ . '/lib/bootstrap.php';
	$client->logout($_SESSION["AccessToken"]);
	 header("Location:http://localhost/cognito-login/index.php");