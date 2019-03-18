<?php
/** @var \pmill\AwsCognito\CognitoClient $client */
$client = require __DIR__ . '/bootstrap.php';

$confirmationCode = '825377';
$username = 'ashoka326@gmail.com';

$client->confirmUserRegistration($confirmationCode, $username);

//You can now login, run login.php test now with your username/password
