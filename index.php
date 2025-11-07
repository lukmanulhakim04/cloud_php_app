<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
?>

<?php
require 'vendor/autoload.php';
use MongoDB\Client;
use MongoDB\BSON\ObjectId;

$uri = 'mongodb+srv://ccuser:CcPass123@clustercloud.oblomos.mongodb.net/?appName=clustercloud';
$client = new Client($uri);
$collection = $client->cloud_app->test_collection;

// --- Search & Sort ---
$filter = [];
$sort = [];
$search = $_GET['search'] ?? '';
$sort_field = $_GET['sort'] ?? '';

if ($search) {
    $filter = ['$or' => [
        ['name' => new MongoDB\BSON\Regex($search, 'i')],
        ['role' => new MongoDB\BSON\Regex($search, 'i')]
    ]];
}

if ($sort_field && in_array($sort_field, ['name','role','created_at'])) {
    $sort[$sort_field] = 1; // 1 = ascending
}

// --- Pagination ---
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 5;
$skip = ($page - 1) * $limit;

$total = $collection->countDocuments($filter);
$pages = ceil($total / $limit);

$options = ['limit'=>$limit, 'skip'=>$skip];
if ($sort) $options['sort'] = $sort;

$documents = $collection->find($filter, $options);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cloud App Upgrade</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">

    <!-- Header + Logout -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Data Students</h1>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>

    <!-- Search & Sort -->
    <form class="row g-3 mb-3" method="GET">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Search name or role" value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div class="col-md-3">
            <select name="sort" class="form-select">
                <option value="">Sort by</option>
                <option value="name" <?php if($sort_field=='name') echo 'selected'; ?>>Name</option>
                <option value="role" <?php if($sort_field=='role') echo 'selected'; ?>>Role</option>
                <option value="created_at" <?php if($sort_field=='created_at') echo 'selected'; ?>>Created At</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary">Apply</button>
        </div>
        <div class="col-md-3 text-end">
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addModal">Tambah Data</button>
        </div>
    </form>

    <!-- Table -->
    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Nama</th>
                <th>Role</th>
                <th>Waktu Dibuat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($documents as $doc): ?>
            <tr>
                <td><?php echo htmlspecialchars($doc['name']); ?></td>
                <td><?php echo htmlspecialchars($doc['role']); ?></td>
                <td><?php echo date('d-m-Y H:i', $doc['created_at']->toDateTime()->getTimestamp()); ?></td>
                <td>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $doc['_id']; ?>">Edit</button>
                    <a href="delete.php?id=<?php echo $doc['_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin hapus?')">Hapus</a>
                </td>
            </tr>

            <!-- Edit Modal -->
            <div class="modal fade" id="editModal<?php echo $doc['_id']; ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST" action="edit.php?id=<?php echo $doc['_id']; ?>">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Student</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <label class="form-label">Nama</label>
                                <input type="text" name="name" value="<?php echo htmlspecialchars($doc['name']); ?>" class="form-control" required>
                                <label class="form-label mt-2">Role</label>
                                <input type="text" name="role" value="<?php echo htmlspecialchars($doc['role']); ?>" class="form-control" required>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Update</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <nav>
        <ul class="pagination">
            <?php for($i=1;$i<=$pages;$i++): ?>
            <li class="page-item <?php if($i==$page) echo 'active'; ?>">
                <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo $sort_field; ?>"><?php echo $i; ?></a>
            </li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="insert.php">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">Nama</label>
                    <input type="text" name="name" class="form-control" required>
                    <label class="form-label mt-2">Role</label>
                    <input type="text" name="role" class="form-control" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
