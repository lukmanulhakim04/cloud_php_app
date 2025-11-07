<?php
require 'vendor/autoload.php';
use MongoDB\Client;
use MongoDB\BSON\ObjectId;

$uri = 'mongodb+srv://ccuser:CcPass123@clustercloud.oblomos.mongodb.net/?appName=clustercloud';
$client = new Client($uri);
$collection = $client->cloud_app->test_collection;

$id = $_GET['id'] ?? '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
    $name = $_POST['name'] ?? '';
    $role = $_POST['role'] ?? '';

    if ($name && $role) {
        $collection->updateOne(
            ['_id' => new ObjectId($id)],
            ['$set' => ['name' => $name, 'role' => $role]]
        );
    }

    // Setelah update, kembali ke index
    header('Location: index.php');
    exit;
}
