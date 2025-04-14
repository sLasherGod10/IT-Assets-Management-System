<?php
require_once 'config/database.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ensure database connection is valid
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle asset addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_asset'])) {
    // Sanitize and fallback
    $asset_tag = $_POST['asset_tag'] ?? '';
    $category_id = $_POST['category_id'] ?? 1;
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $purchase_date = $_POST['purchase_date'] ?? null;
    $purchase_cost = is_numeric($_POST['purchase_cost']) ? floatval($_POST['purchase_cost']) : 0;
    $status = $_POST['status'] ?? 'available';

    // Check for required fields
    if (empty($asset_tag) || empty($name) || empty($purchase_date)) {
        die("Missing required fields.");
    }

    $stmt = $conn->prepare("INSERT INTO assets (asset_tag, category_id, name, description, purchase_date, purchase_cost, status) VALUES (?, ?, ?, ?, ?, ?, ?)");

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("sisssds", $asset_tag, $category_id, $name, $description, $purchase_date, $purchase_cost, $status);

    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }

    $stmt->close();

    // Safe redirect using JavaScript to avoid header issues
    echo "<script>window.location.href='index.php?page=assets';</script>";
    exit();
}

// Handle asset deletion with allocation check
$delete_error = null;
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_asset'])) {
    $asset_id = $_POST['asset_id'];

    // Check if the asset has any allocation history
    $check = $conn->prepare("SELECT COUNT(*) FROM asset_allocations WHERE asset_id = ?");
    $check->bind_param("i", $asset_id);
    $check->execute();
    $check->bind_result($count);
    $check->fetch();
    $check->close();

    if ($count > 0) {
        $delete_error = "Cannot delete asset. It has allocation history.";
    } else {
        // Delete asset
        $stmt = $conn->prepare("DELETE FROM assets WHERE id = ?");
        $stmt->bind_param("i", $asset_id);

        if (!$stmt->execute()) {
            die("Error executing delete statement: " . $stmt->error);
        }
        $stmt->close();

        header("Location: index.php?page=assets");
        exit();
    }
}

// Fetch assets
$assets_query = "
    SELECT a.*, c.name AS category_name 
    FROM assets a
    LEFT JOIN asset_categories c ON a.category_id = c.id
    ORDER BY a.id DESC
";
$assets = $conn->query($assets_query);

// Fetch categories for dropdown
$categories = $conn->query("SELECT id, name FROM asset_categories");

// Bootstrap classes for status
function getStatusClass($status) {
    return match ($status) {
        'available' => 'success',
        'in_use' => 'primary',
        'maintenance' => 'warning',
        'decommissioned' => 'danger',
        default => 'secondary'
    };
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Asset Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to bottom right, #6a11cb, #2575fc);
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
        }
        .container {
            padding-top: 80px;
        }
        .card, .modal-content {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 1rem;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }
        .table thead {
            background-color: rgba(255, 255, 255, 0.9);
        }
        .modal-header, .modal-footer {
            border-color: rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #fff;
            text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.6);
        }
        label, .form-label, .form-select, .form-control {
            font-weight: 500;
        }
        .btn-primary, .btn-success, .btn-danger, .btn-secondary {
            border: none;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }
        .btn-primary:hover, .btn-success:hover, .btn-danger:hover, .btn-secondary:hover {
            opacity: 0.9;
        }
        .alert-danger {
            border-radius: 0.75rem;
        }
    </style>
</head>
<body>

<div class="container">
    <?php if (!empty($delete_error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($delete_error); ?></div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Asset List</h2>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addAssetModal">+ Add Asset</button>
    </div>

    <div class="table-responsive card p-3">
        <table class="table table-bordered table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Asset Tag</th>
                    <th>Category</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Purchase Date</th>
                    <th>Cost</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($assets && $assets->num_rows > 0): ?>
                    <?php while ($row = $assets->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id']; ?></td>
                            <td><?= htmlspecialchars($row['asset_tag']); ?></td>
                            <td><?= htmlspecialchars($row['category_name']); ?></td>
                            <td><?= htmlspecialchars($row['name']); ?></td>
                            <td><?= htmlspecialchars($row['description']); ?></td>
                            <td><?= htmlspecialchars($row['purchase_date']); ?></td>
                            <td>₹<?= number_format($row['purchase_cost'], 2); ?></td>
                            <td>
                                <span class="badge bg-<?= getStatusClass($row['status']); ?>">
                                    <?= ucfirst($row['status']); ?>
                                </span>
                            </td>
                            <td><?= $row['created_at']; ?></td>
                            <td>
                                <form method="POST" onsubmit="return confirm('Are you sure you want to delete this asset?');">
                                    <input type="hidden" name="delete_asset" value="1">
                                    <input type="hidden" name="asset_id" value="<?= $row['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="10" class="text-center">No assets found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Asset Modal -->
<div class="modal fade" id="addAssetModal" tabindex="-1" aria-labelledby="addAssetModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <input type="hidden" name="add_asset" value="1">
            <div class="modal-header">
                <h5 class="modal-title" id="addAssetModalLabel">Add New Asset</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Asset Tag</label>
                    <input type="text" name="asset_tag" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-select" required>
                        <option value="">Select Category</option>
                        <?php while ($cat = $categories->fetch_assoc()): ?>
                            <option value="<?= $cat['id']; ?>"><?= htmlspecialchars($cat['name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Asset Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="2"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Purchase Date</label>
                    <input type="date" name="purchase_date" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Purchase Cost (₹)</label>
                    <input type="number" step="0.01" name="purchase_cost" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="available">Available</option>
                        <option value="in_use">In Use</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="decommissioned">Decommissioned</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Asset</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
