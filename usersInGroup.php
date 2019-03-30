<?php 
    $client = require __DIR__ . '/lib/bootstrap.php';
    if(isset($_GET['gp'])){
	   $response['users'] =  $client->buildAdminFormatedObject($client->getListUsersInGroup($_GET['gp']));
	   echo json_encode($response);
	}