<?php

include_once '../vendor/autoload.php';

use session\Session;
use \MongoClient;

$client = new MongoClient("mongodb://localhost:27017");
$db = $client->selectDB("session-mongo-test");
Session::setMongoHandler($db);
$session = Session::singleton();