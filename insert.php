<?php
require 'vendor/autoload.php';
use MongoDB\Client;

$uri = 'mongodb+srv://ccuser:CcPass123@clustercloud.oblomos.mongodb.net/?appName=clustercloud';
$client = new Client($uri);
$collection = $client->cloud_app->test_collection;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $role = $_POST['role'] ?? '';

    if ($name && $role) {
        $collection->insertOne([
            'name' => $name,
            'role' => $role,
            'created_at' => new MongoDB\BSON\UTCDateTime()
        ]);
    }

    // Kalau dipanggil dari modal, redirect kembali ke index
    header('Location: index.php');
    exit;
}
