<?php
 
function is_group($groupname){
	$client = require __DIR__ . '/lib/bootstrap.php';
     return $client->getUserGroup($groupname);
}