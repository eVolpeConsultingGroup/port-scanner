<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        addEntry($db, $_POST['ip'], $_POST['name'], $_POST['excluded_ports']);
    } elseif ($_POST['action'] === 'delete' && isset($_POST['id'])) {
        deleteEntry($db, $_POST['id']);
    } elseif ($_POST['action'] === 'edit' && isset($_POST['id'])) {
        editEntry($db, $_POST['id'], $_POST['ip'], $_POST['name'], $_POST['excluded_ports']);
    }
}

$entries = getEntries($db);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Port Monitoring System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./styles.css">
</head>
<body>
<div class="container">
    <h1>Add IP Address</h1>
    <form method="POST" class="mb-4">
        <input type="hidden" name="action" value="add">
        <div class="mb-3">
            <label for="ip" class="form-label">IP Address</label>
            <input type="text" name="ip" id="ip" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
    <div class="mb-3">
        <label>Excluded Ports</label>
        <div id="port-fields-wrapper">
        <div class="input-group mb-2">
            <input type="text" name="excluded_ports[]" class="form-control">
            <button type="button" class="btn btn-success add-port-field">+</button>
            </div>
        </div>
    </div>
        <button type="submit" class="btn btn-success">Add</button>
    </form>

    <h2>IP List</h2>
    <table class="table table-bordered">
        <thead>
            <tr><th>IP</th><th>Name</th><th>Excluded Ports</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php foreach ($entries as $entry): ?>
                <tr>
                    <td><?= htmlspecialchars($entry['ip']) ?></td>
                    <td><?= htmlspecialchars($entry['name']) ?></td>
                    <td><?= htmlspecialchars(implode(', ', json_decode($entry['excluded_ports'] ?? '[]', true) ?? [])) ?></td>
                    <td>
                        <!-- Delete Button -->
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($entry['id']) ?>">
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>

                        <!-- Edit Button -->
                        <button type="button" class="btn btn-primary"
        data-bs-toggle="modal"
        data-bs-target="#editModal"
        data-id="<?= $entry['id'] ?>"
        data-ip="<?= htmlspecialchars($entry['ip']) ?>"
        data-name="<?= htmlspecialchars($entry['name']) ?>"
        data-excluded-ports="<?= htmlspecialchars($entry['excluded_ports'] ?? '[]') ?>"> <!-- Zmiana nazwy atrybutu -->
    Edit
</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Edit Modal -->
    <?php include 'edit_modal.php'; ?>
</div>

<script src="./scripts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>

</body>
</html>
