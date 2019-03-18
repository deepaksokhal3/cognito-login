<?php
/** @var \pmill\AwsCognito\CognitoClient $client */
$client = require __DIR__ . '/bootstrap.php';

$password = 'Ashok!123';
$email = 'ashoka326@gmail.com';
$username = $email;

$client->registerUser($username, $password, [
	'email' => $email,
]);

//Your email address should receive an email with a confirmation code, run confirmUserRegistration.php next with your code
