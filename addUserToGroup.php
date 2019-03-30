<?php
$client = require __DIR__ . '/lib/bootstrap.php';
if(isset($_GET['uid']) && isset($_GET['gp']))
	$response = $client->addUserToGroup($_GET['uid'], $_GET['gp']);
echo json_encode($response);