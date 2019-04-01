<?php
$client = require __DIR__ . '/lib/bootstrap.php';
if(isset($_GET['gp']))
	$response = $client->removeUserFromGroup($_GET['gp'],$_GET['uid']);
echo json_encode($response);