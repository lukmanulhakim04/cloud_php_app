<?php
require 'vendor/autoload.php';
use MongoDB\Client;
use MongoDB\BSON\ObjectId;

$uri = 'mongodb+srv://ccuser:CcPass123@clustercloud.oblomos.mongodb.net/?appName=clustercloud';
$client = new Client($uri);
$collection = $client->cloud_app->test_collection;

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $collection->deleteOne(['_id' => new ObjectId($id)]);
}

header('Location: index.php');
exit;
