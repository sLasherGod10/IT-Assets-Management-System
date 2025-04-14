<?php
require_once 'config/database.php';
$activePage = 'dashboard';

$query = "SELECT 
    COUNT(*) as total_assets,
    SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) as available_assets,
    SUM(CASE WHEN status = 'in_use' THEN 1 ELSE 0 END) as in_use_assets,
    SUM(CASE WHEN status = 'maintenance' THEN 1 ELSE 0 END) as maintenance_assets
FROM assets";
$result = $conn->query($query);
$stats = $result->fetch_assoc();

$allocations_query = "SELECT a.*, ast.name as asset_name, u.name as user_name 
    FROM asset_allocations a
    JOIN assets ast ON a.asset_id = ast.id
    JOIN users u ON a.user_id = u.id
    ORDER BY a.allocated_date DESC LIMIT 5";
$recent_allocations = $conn->query($allocations_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>IT Asset Management Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body, html {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            font-size: 16px;
        }
        #assetStatusChart {
    background-color: #1e1e1e; /* Dark background */
    border-radius: 10px;
    padding: 10px;
}

.chart-container {
    background-color: #121212; /* Even darker container */
    padding: 20px;
    border-radius: 15px;
}


        h1, h4 {
            font-weight: 700;
        }

        .dashboard-wrapper {
            min-height: 100vh;
            padding: 6rem 2rem 2rem;
            background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
            background-size: 400% 400%;
            animation: gradientMove 15s ease infinite;
        }

        @keyframes gradientMove {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .dashboard-card {
            background: linear-gradient(145deg, #1a1a2e, #3c1053, #2c003e) !important;
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 1rem;
            box-shadow: 0 20px 30px rgba(0, 0, 0, 0.35);
            transition: transform 0.5s ease, box-shadow 0.5s ease;
            cursor: pointer;
            color: #fff;
        }

        .dashboard-card:hover {
            transform: rotateX(10deg) rotateY(-10deg) scale(1.03);
            box-shadow: 0 30px 50px rgba(0, 0, 0, 0.4);
        }

        .card-body h4 {
            font-size: 2rem;
            color: #f0eaff;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.6);
            margin-bottom: 0.5rem;
        }

        .card-body div {
            color: #e0d7f5;
            font-weight: 500;
        }

        .card-header {
            background: rgba(255, 255, 255, 0.15) !important;
            color: #fff;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .fade-in {
            animation: fadeIn 1s ease-in-out both;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        table td, table th {
            font-size: 15px;
        }

        .badge {
            font-size: 0.9rem;
        }

        .navbar {
            background: linear-gradient(135deg, #1f1c2c, #928dab);
        }
        .chart-container {
    background-color: #1e1e1e !important;
    padding: 20px;
    border-radius: 1rem;
    color: #fff;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.dashboard-card .card-header {
    background: rgba(255, 255, 255, 0.08) !important;
    color: #ffffff;
}

body, html {
    height: 100%;
    width: 100%;
    margin: 0;
    padding: 0;
    font-family: 'Poppins', sans-serif;
    font-size: 16px;
    background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
    background-size: 400% 400%;
    animation: gradientMove 15s ease infinite;
}


    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg fixed-top px-4 shadow">
    <a class="navbar-brand d-flex align-items-center text-white" href="index.php" style="font-size: 1.8rem;">
        <img src="assets/logo.png" alt="Logo" style="height: 50px; margin-right: 10px;"> nextASSETS
    </a>
    <div class="ms-auto d-flex align-items-center">
        <span class="text-white me-4" style="font-size: 1.1rem;">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
        <a href="logout.php" class="btn btn-outline-light btn-sm" style="padding: 0.4rem 0.9rem; font-size: 1rem; border-radius: 10px;">Logout</a>
    </div>
</nav>

<div class="dashboard-wrapper">
    <div class="container-fluid px-4 fade-in">
        <h1 class="text-white">Dashboard</h1>

        <!-- Slideshow Card -->
<div class="row mt-4 fade-in">
    <div class="col-12">
        <div class="card dashboard-card text-white overflow-hidden" style="position: relative; height: 450px;">
            <div id="thoughtCarousel" class="carousel slide h-100" data-bs-ride="carousel">
                <div class="carousel-inner h-100">
                    <div class="carousel-item active h-100">
                        <img src="assets/Slide1.jpeg" class="d-block w-100 h-100" style="object-fit: cover; filter: brightness(0.65);" alt="Slide 1">
                    </div>
                    <div class="carousel-item h-100">
                        <img src="assets/Slide2.jpeg" class="d-block w-100 h-100" style="object-fit: cover; filter: brightness(0.65);" alt="Slide 2">
                    </div>
                    <div class="carousel-item h-100">
                        <img src="assets/Slide3.jpeg" class="d-block w-100 h-100" style="object-fit: cover; filter: brightness(0.65);" alt="Slide 3">
                    </div>
                </div>
            </div>

            <?php
            $thoughts = [
                'dashboard' => 'Insight. Control. Growth. All in one dashboard.',
                'users' => 'Every user matters. Every click counts.',
                'assets' => 'Your assets, always within reach.',
                'reports' => 'Smart reports for smarter decisions.',
                'default' => 'Redefining how you manage IT — seamlessly and smartly.'
            ];
            $displayThought = isset($thoughts[$activePage]) ? $thoughts[$activePage] : $thoughts['default'];
            ?>

            <div class="position-absolute top-50 start-0 translate-middle-y text-start px-4" style="z-index: 10; max-width: 55%;">
                <h2 style="font-family: 'Poppins', sans-serif; font-size: 3rem; font-weight: 600; color: #fff; text-shadow: 2px 2px 6px rgba(0,0,0,0.7); background: rgba(0,0,0,0.35); padding: 0.8rem 1.5rem; border-radius: 1.2rem;">
                    “<?= $displayThought ?>”
                </h2>
                <p class="mt-2 text-light fw-semibold" style="font-size: 1.2rem;">— nextASSETS Platform</p>
            </div>
        </div>
    </div>
</div>


        <!-- Asset Cards -->
        <div class="row mt-4">
            <?php
            $labels = ['Total Assets', 'Available', 'In Use', 'Maintenance'];
            $values = [
                $stats['total_assets'],
                $stats['available_assets'],
                $stats['in_use_assets'],
                $stats['maintenance_assets']
            ];
            foreach ($labels as $i => $label): ?>
                <div class="col-xl-3 col-md-6">
                    <div class="card mb-4 dashboard-card">
                        <div class="card-body">
                            <h4><?= $values[$i] ?></h4>
                            <div><?= $label ?></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Charts -->
        <!-- Replace this part in your existing code -->
<!-- Charts -->
<div class="row mt-4 fade-in">
    <div class="col-xl-6">
        <div class="card mb-4 dashboard-card chart-container">
            <div class="card-header"><i class="fas fa-chart-pie me-1"></i> Asset Status Distribution</div>
            <div class="card-body p-4"><canvas id="assetStatusChart" height="280"></canvas></div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card mb-4 dashboard-card chart-container">
            <div class="card-header"><i class="fas fa-chart-line me-1"></i> Allocation Trend (Sample)</div>
            <div class="card-body p-4"><canvas id="allocationTrendChart" height="280"></canvas></div>
        </div>
    </div>
</div>

        

        <!-- Recent Allocations -->
        
        <!-- Recent Allocations -->
<div class="card mb-4 fade-in dashboard-card">
    <div class="card-header"><i class="fas fa-table me-1"></i> Recent Asset Allocations</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-dark table-striped table-hover align-middle">
                <thead>
                <tr>
                    <th>Asset</th>
                    <th>Allocated To</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                <?php while($allocation = $recent_allocations->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($allocation['asset_name']) ?></td>
                        <td><?= htmlspecialchars($allocation['user_name']) ?></td>
                        <td><?= date('M d, Y', strtotime($allocation['allocated_date'])) ?></td>
                        <td>
                            <span class="badge bg-<?= $allocation['status'] === 'active' ? 'success' : 'secondary' ?>">
                                <?= ucfirst($allocation['status']) ?>
                            </span>
                        </td>
                    </tr>
                <?php endwhile; ?>
                <?php if ($recent_allocations->num_rows === 0): ?>
                    <tr><td colspan="4" class="text-center text-light">No recent allocations found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<!-- Scripts -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Set canvas containers to black background
        const chartCanvases = ['assetStatusChart', 'allocationTrendChart'];
        chartCanvases.forEach(id => {
            const canvas = document.getElementById(id);
            if (canvas) {
                canvas.parentElement.style.backgroundColor = '#000'; // dark background
                canvas.parentElement.style.border = 'none';
            }
        });

        // Asset Status Chart
        const assetStatusCtx = document.getElementById('assetStatusChart').getContext('2d');
const assetStatusChart = new Chart(assetStatusCtx, {
    type: 'doughnut',
    data: {
        labels: ['Available', 'In Use', 'Maintenance'],
        datasets: [{
            data: [
                <?= $stats['available_assets']; ?>,
                <?= $stats['in_use_assets']; ?>,
                <?= $stats['maintenance_assets']; ?>
            ],
            backgroundColor: [
                'rgba(40, 167, 69, 0.8)',
                'rgba(255, 193, 7, 0.8)',
                'rgba(220, 53, 69, 0.8)'
            ],
            borderColor: [
                '#28a745',
                '#ffc107',
                '#dc3545'
            ],
            borderWidth: 2,
            hoverOffset: 15
        }]
    },
    options: {
        cutout: '60%',
        responsive: true,
        animation: {
            animateScale: true,
            animateRotate: true
        },
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    color: '#ffffff',
                    font: {
                        weight: 'bold'
                    }
                }
            },
            tooltip: {
                backgroundColor: '#1e1e1e',
                titleColor: '#ffffff',
                bodyColor: '#ffffff',
                borderColor: '#444',
                borderWidth: 1,
                callbacks: {
                    label: function (context) {
                        return `${context.label}: ${context.parsed} assets`;
                    }
                }
            }
        },
        layout: {
            padding: 10
        }
    }
});

        // Allocation Trend Chart
        const allocationTrendCtx = document.getElementById('allocationTrendChart').getContext('2d');
        const gradient = allocationTrendCtx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(111, 66, 193, 0.3)');
        gradient.addColorStop(1, 'rgba(111, 66, 193, 0)');

        const allocationTrendChart = new Chart(allocationTrendCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Allocations',
                    data: [10, 15, 12, 18, 20, 25],
                    fill: true,
                    backgroundColor: gradient,
                    borderColor: '#6f42c1',
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#6f42c1',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        labels: {
                            color: '#fff' // white text
                        }
                    },
                    tooltip: {
                        backgroundColor: '#222',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            color: '#fff'
                        },
                        grid: {
                            color: 'rgba(255,255,255,0.1)'
                        }
                    },
                    y: {
                        ticks: {
                            color: '#fff'
                        },
                        grid: {
                            color: 'rgba(255,255,255,0.1)'
                        }
                    }
                }
            }
        });
    });
</script>


</body>
</html>
