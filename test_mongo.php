<?php
require 'vendor/autoload.php';

use MongoDB\Client;

$uri = 'mongodb+srv://ccuser:CcPass123@clustercloud.oblomos.mongodb.net/?appName=clustercloud';
$client = new Client($uri);

try {
    $client->selectDatabase('admin')->command(['ping' => 1]);
    echo "Koneksi ke MongoDB Atlas berhasil!";
} catch (Exception $e) {
    echo "Terjadi kesalahan: " . $e->getMessage();
}
