<?php
require_once 'config/database.php';

// Count users by role
$userResult = $conn->query("SELECT role, COUNT(*) as count FROM users GROUP BY role");
if (!$userResult) {
    die("User role query failed: " . $conn->error);
}
$userData = $userResult->fetch_all(MYSQLI_ASSOC);

// Count assets by category
$assetResult = $conn->query("
    SELECT ac.name AS category, COUNT(a.id) AS count 
    FROM asset_categories ac
    LEFT JOIN assets a ON ac.id = a.category_id
    GROUP BY ac.id
");
if (!$assetResult) {
    die("Asset category query failed: " . $conn->error);
}
$assetData = $assetResult->fetch_all(MYSQLI_ASSOC);

// Fetch asset allocation history
$historyQuery = "
    SELECT a.asset_tag, a.name AS asset_name, u.name AS user_name, aa.allocated_date, aa.return_date, aa.status, aa.notes
    FROM asset_allocations aa
    JOIN assets a ON aa.asset_id = a.id
    JOIN users u ON aa.user_id = u.id
    ORDER BY aa.allocated_date DESC
";
$historyResult = $conn->query($historyQuery);
if (!$historyResult) {
    die("Asset history query failed: " . $conn->error);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reports | IT Asset Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            background: linear-gradient(135deg, #1b1f2a, #0a0f1f);
            min-height: 100vh;
            margin: 0;
            font-family: 'Poppins', sans-serif;
            color: #ffffff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding-top: 80px;
        }

        .container {
            max-width: 1200px;
            width: 100%;
            padding: 2rem;
        }

        .card {
            border: none;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            color: #ffffff;
            box-shadow: 0 12px 25px rgba(0,0,0,0.4);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: scale(1.03);
        }

        .card-title {
            font-weight: 600;
        }

        h2 {
            text-align: center;
            font-weight: bold;
            margin-bottom: 2rem;
            color: #00ffc3;
            text-shadow: 0px 0px 8px rgba(0,255,195,0.3);
        }

        canvas {
            animation: fadeIn 1.2s ease forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .shadow {
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5) !important;
        }

        .chart-container {
            background: linear-gradient(145deg, #141824, #1e2233);
            border-radius: 20px;
            padding: 1rem;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>📊 nextASSETS - Reports</h2>

    <div class="row g-4">
        <!-- Pie Chart for User Roles -->
        <div class="col-md-6">
            <div class="card p-4 chart-container">
                <h5 class="card-title mb-3"><i class="fas fa-users me-2"></i>Users by Role</h5>
                <canvas id="userPieChart" height="300"></canvas>
            </div>
        </div>

        <!-- Bar Chart for Asset Categories -->
        <div class="col-md-6">
            <div class="card p-4 chart-container">
                <h5 class="card-title mb-3"><i class="fas fa-boxes-stacked me-2"></i>Assets by Category</h5>
                <canvas id="assetBarChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Configuration -->
<script>
    const userRoleData = {
        labels: <?= json_encode(array_column($userData, 'role')) ?>,
        datasets: [{
            data: <?= json_encode(array_column($userData, 'count')) ?>,
            backgroundColor: ['#0d6efd', '#20c997', '#ffc107', '#dc3545'],
            borderWidth: 2,
        }]
    };

    const userPieChart = new Chart(document.getElementById('userPieChart'), {
        type: 'doughnut',
        data: userRoleData,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#fff',
                        font: { size: 14 }
                    }
                }
            },
            animation: {
                animateScale: true,
                animateRotate: true
            }
        }
    });

    const assetCategoryData = {
        labels: <?= json_encode(array_column($assetData, 'category')) ?>,
        datasets: [{
            label: 'Assets',
            data: <?= json_encode(array_column($assetData, 'count')) ?>,
            backgroundColor: [
                '#0d6efd', '#6610f2', '#6f42c1', '#d63384', '#fd7e14',
                '#20c997', '#198754', '#dc3545', '#ffc107'
            ],
            borderRadius: 5
        }]
    };

    const assetBarChart = new Chart(document.getElementById('assetBarChart'), {
        type: 'bar',
        data: assetCategoryData,
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { color: '#fff' },
                    grid: { color: 'rgba(255,255,255,0.1)' }
                },
                x: {
                    ticks: { color: '#fff' },
                    grid: { color: 'rgba(255,255,255,0.1)' }
                }
            },
            plugins: {
                legend: {
                    labels: { color: '#fff' }
                }
            },
            animation: {
                duration: 1500,
                easing: 'easeOutBounce'
            }
        }
    });

    
</script>
<!-- Asset History Table -->
<div class="card mt-5 p-4 shadow chart-container">
    <h5 class="card-title mb-3"><i class="fas fa-history me-2"></i>Asset Allocation History</h5>
    <div class="table-responsive">
        <table class="table table-dark table-striped table-hover">
            <thead>
                <tr>
                    <th>Asset Tag</th>
                    <th>Asset Name</th>
                    <th>User</th>
                    <th>Allocated Date</th>
                    <th>Return Date</th>
                    <th>Status</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $historyResult->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['asset_tag']) ?></td>
                    <td><?= htmlspecialchars($row['asset_name']) ?></td>
                    <td><?= htmlspecialchars($row['user_name']) ?></td>
                    <td><?= htmlspecialchars($row['allocated_date']) ?></td>
                    <td><?= $row['return_date'] ? htmlspecialchars($row['return_date']) : '<span class="text-warning">Not returned</span>' ?></td>
                    <td>
                        <span class="badge <?= $row['status'] === 'returned' ? 'bg-success' : 'bg-info' ?>">
                            <?= ucfirst($row['status']) ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($row['notes']) ?: '-' ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
