<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>






<?php
// Error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database.php';

// Handle form submission for adding allocation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_allocation'])) {
    $asset_id = $_POST['asset_id'];
    $user_id = $_POST['user_id'];
    $allocated_date = $_POST['allocated_date'];
    $return_date = $_POST['return_date'] ?? null;
    $notes = $_POST['notes'];

    // Prepare and execute allocation insert
    $stmt = $conn->prepare("INSERT INTO asset_allocations (asset_id, user_id, allocated_date, return_date, status, notes) VALUES (?, ?, ?, ?, 'active', ?)");
    $stmt->bind_param("iisss", $asset_id, $user_id, $allocated_date, $return_date, $notes);
    
    if ($stmt->execute()) {
        $stmt->close();

        // Update asset status to 'in_use'
        $updateAsset = $conn->prepare("UPDATE assets SET status = 'in_use' WHERE id = ?");
        $updateAsset->bind_param("i", $asset_id);
        $updateAsset->execute();
        $updateAsset->close();

        header("Location: index.php?page=allocations");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Handle return allocation ✅ NEW
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['return_allocation'])) {
    $allocation_id = $_POST['allocation_id'];

    // Get asset ID associated with the allocation
    $stmt = $conn->prepare("SELECT asset_id FROM asset_allocations WHERE id = ?");
    $stmt->bind_param("i", $allocation_id);
    $stmt->execute();
    $stmt->bind_result($asset_id);
    $stmt->fetch();
    $stmt->close();

    // Update allocation status to 'returned'
    $stmt = $conn->prepare("UPDATE asset_allocations SET status = 'returned' WHERE id = ?");
    $stmt->bind_param("i", $allocation_id);

    if ($stmt->execute()) {
        $stmt->close();

        // Update asset status to 'available'
        $updateAsset = $conn->prepare("UPDATE assets SET status = 'available' WHERE id = ?");
        $updateAsset->bind_param("i", $asset_id);
        $updateAsset->execute();
        $updateAsset->close();

        header("Location: index.php?page=allocations");
        exit();
    } else {
        echo "Error returning allocation: " . $stmt->error;
    }
}

// Handle remove allocation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_allocation'])) {
    $allocation_id = $_POST['allocation_id'];

    // Get the asset ID first
    $stmt = $conn->prepare("SELECT asset_id FROM asset_allocations WHERE id = ?");
    $stmt->bind_param("i", $allocation_id);
    $stmt->execute();
    $stmt->bind_result($asset_id);
    $stmt->fetch();
    $stmt->close();

    // Delete the allocation
    $stmt = $conn->prepare("DELETE FROM asset_allocations WHERE id = ?");
    $stmt->bind_param("i", $allocation_id);

    if ($stmt->execute()) {
        $stmt->close();

        // Update asset status to 'available'
        $updateAsset = $conn->prepare("UPDATE assets SET status = 'available' WHERE id = ?");
        $updateAsset->bind_param("i", $asset_id);
        $updateAsset->execute();
        $updateAsset->close();

        header("Location: index.php?page=allocations");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Fetch allocations
$allocations = $conn->query("SELECT aa.*, a.asset_tag, a.name AS asset_name, u.name AS user_name, u.email FROM asset_allocations aa JOIN assets a ON aa.asset_id = a.id JOIN users u ON aa.user_id = u.id ORDER BY aa.allocated_date DESC");

// Fetch assets and users for the dropdowns
$assets = $conn->query("SELECT id, asset_tag, name FROM assets WHERE status IN ('available', 'in_use')");
$users = $conn->query("SELECT id, name, email FROM users");
?>

<!-- Bootstrap CDN -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<!-- Custom Styles -->
<style>
    body {
        background: linear-gradient(135deg, rgb(186, 116, 235), rgb(229, 172, 172));
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-attachment: fixed;
        margin: 0;
        padding-top: 80px;
    }

    .fade-in {
        animation: fadeIn 0.6s ease-in-out;
    }

    @keyframes fadeIn {
        from {opacity: 0; transform: translateY(10px);}
        to {opacity: 1; transform: translateY(0);}
    }

    .table-wrapper {
        background: #fff;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }

    .btn-glow {
        background: linear-gradient(to right, rgb(40, 0, 219), #0083b0);
        color: white;
        font-weight: 600;
        border: none;
        transition: all 0.3s ease;
    }

    .btn-glow:hover {
        box-shadow: 0 0 10px rgba(0, 180, 219, 0.6);
        transform: scale(1.05);
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 131, 176, 0.08);
        transition: background-color 0.3s ease;
    }

    .modal-content {
        border-radius: 1rem;
        animation: fadeIn 0.5s ease;
    }

    .table td, .table th {
        vertical-align: middle;
    }

    h2.text-white {
        color: rgb(255, 255, 255) !important;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
    }
</style>

<!-- Main UI -->
<div class="container mt-5 pt-4 fade-in">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-white">Asset Allocations</h2>
        <button class="btn btn-glow px-4 py-2" data-bs-toggle="modal" data-bs-target="#addAllocationModal">
            <i class="bi bi-plus-circle"></i> Add Allocation
        </button>
    </div>

    <div class="table-wrapper">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Asset</th>
                        <th>User</th>
                        <th>Allocated Date</th>
                        <th>Return Date</th>
                        <th>Status</th>
                        <th>Notes</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($allocations && $allocations->num_rows > 0): ?>
                        <?php while ($row = $allocations->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['id']; ?></td>
                                <td><?= htmlspecialchars($row['asset_tag'] . ' - ' . $row['asset_name']); ?></td>
                                <td><?= htmlspecialchars($row['user_name'] . ' (' . $row['email'] . ')'); ?></td>
                                <td><?= $row['allocated_date']; ?></td>
                                <td><?= $row['return_date'] ?? '-'; ?></td>
                                <td>
                                    <span class="badge 
                                        <?= $row['status'] === 'active' ? 'bg-success' : ($row['status'] === 'returned' ? 'bg-secondary' : 'bg-dark'); ?>">
                                        <?= ucfirst($row['status']); ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($row['notes']); ?></td>
                                <td>
                                    <?php if ($row['status'] === 'active'): ?>
                                        <form method="POST" style="display:inline;" onsubmit="return confirm('Return this asset?');">
                                            <input type="hidden" name="return_allocation" value="1">
                                            <input type="hidden" name="allocation_id" value="<?= $row['id']; ?>">
                                            <button class="btn btn-sm btn-warning me-1">
                                                <i class="bi bi-arrow-return-left"></i> Return
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Remove this allocation?');">
                                        <input type="hidden" name="remove_allocation" value="1">
                                        <input type="hidden" name="allocation_id" value="<?= $row['id']; ?>">
                                        <button class="btn btn-sm btn-danger">
                                            <i class="bi bi-trash"></i> Remove
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="text-center text-muted">No allocations found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Allocation Modal -->
<div class="modal fade" id="addAllocationModal" tabindex="-1" aria-labelledby="addAllocationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <input type="hidden" name="add_allocation" value="1">
            <div class="modal-header">
                <h5 class="modal-title" id="addAllocationModalLabel">Add New Allocation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <div class="mb-3">
                    <label class="form-label">Select Asset</label>
                    <select name="asset_id" class="form-select" required>
                        <option value="">Choose asset</option>
                        <?php while ($asset = $assets->fetch_assoc()): ?>
                            <option value="<?= $asset['id']; ?>"><?= htmlspecialchars($asset['asset_tag'] . ' - ' . $asset['name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Assign To (User)</label>
                    <select name="user_id" class="form-select" required>
                        <option value="">Choose user</option>
                        <?php while ($user = $users->fetch_assoc()): ?>
                            <option value="<?= $user['id']; ?>"><?= htmlspecialchars($user['name'] . ' (' . $user['email'] . ')'); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Allocated Date</label>
                    <input type="date" name="allocated_date" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Expected Return Date (optional)</label>
                    <input type="date" name="return_date" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Additional info..."></textarea>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Allocate Asset</button>
            </div>
        </form>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


