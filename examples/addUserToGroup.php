<?php
/** @var \pmill\AwsCognito\CognitoClient $client */
$client = require __DIR__ . '/bootstrap.php';

$username = 'ashoka326@gmail.com';
$groupName = 'initial_aws_user_group';

$client->addUserToGroup($username, $groupName);
