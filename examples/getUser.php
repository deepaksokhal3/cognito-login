<?php
/** @var \pmill\AwsCognito\CognitoClient $client */
$client = require __DIR__ . '/bootstrap.php';

$username = 'ashoka326@gmail.com';

try {
	$user = $client->getUser($username);
	echo $user['Username'] . PHP_EOL;
	echo '<pre>';
	print_r($user['UserAttributes']);
} catch (Exception $e) {
	echo "An error occurred: " . $e->getMessage();
}
