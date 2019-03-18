<?php
use pmill\AwsCognito\CognitoClient;
use pmill\AwsCognito\Exception\ChallengeException;
use pmill\AwsCognito\Exception\PasswordResetRequiredException;

/** @var CognitoClient $client */
$client = require __DIR__ . '/bootstrap.php';

$username = 'ashoka326@gmail.com';
$password = 'Ashok!123';

try {

	$authenticationResponse = $client->authenticate($username, $password);

} catch (ChallengeException $e) {
	if ($e->getChallengeName() === CognitoClient::CHALLENGE_NEW_PASSWORD_REQUIRED) {
		$authenticationResponse = $client->respondToNewPasswordRequiredChallenge($username, 'password_new', $e->getSession());
	}
} catch (PasswordResetRequiredException $e) {
	die("PASSWORD RESET REQUIRED");
}

var_dump($authenticationResponse);