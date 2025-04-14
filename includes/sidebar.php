<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>nextASSETS Dashboard</title>

  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

  <!-- Google Fonts: Poppins -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

  <style>
    /* Gradient Navbar (if needed) */
    .gradient-navbar {
      background: linear-gradient(135deg, #3a0ca3, #7209b7, #10002b);
      background-size: 300% 300%;
      animation: navbarGradient 12s ease infinite;
      color: white;
    }

    @keyframes navbarGradient {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    /* Sidebar Styling */
    #sidebarMenu {
      width: 260px !important;
      min-height: 100vh;
      background: linear-gradient(135deg, #2e003e, #3a0ca3, #10002b, #000000);
      background-size: 400% 400%;
      animation: sidebarGradient 15s ease infinite;
      color: #ffffff;
      border-right: 1px solid rgba(255, 255, 255, 0.1);
      box-shadow: 8px 0 25px rgba(0, 0, 0, 0.4);
      backdrop-filter: blur(12px);
      transform-style: preserve-3d;
      transition: all 0.3s ease-in-out;
      font-family: 'Poppins', sans-serif;
    }

    @keyframes sidebarGradient {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    #sidebarMenu .nav-link {
      color: rgba(255, 255, 255, 0.9);
      font-weight: 600;
      font-size: 1.1rem;
      padding: 16px 24px;
      border-radius: 10px;
      margin-bottom: 10px;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      letter-spacing: 0.5px;
      text-transform: capitalize;
    }

    #sidebarMenu .nav-link:hover,
    #sidebarMenu .nav-link.active {
      background: rgba(255, 255, 255, 0.2);
      color: #ffffff;
      transform: translateX(8px);
      box-shadow: 0 4px 14px rgba(0, 0, 0, 0.3);
    }

    #sidebarMenu .nav-link i {
      font-size: 1.2rem;
      margin-right: 12px;
      color: #ffc6ff;
    }

    #sidebarMenu ul.nav.flex-column {
      padding: 1.2rem;
    }

    .main-content {
      margin-left: 260px;
      padding: 2rem;
    }

    @media (max-width: 768px) {
      .main-content {
        margin-left: 0;
      }
    }
  </style>
</head>
<body>

<!-- Sidebar -->
<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar collapse">
  <div class="position-sticky pt-3">
    <ul class="nav flex-column">
      <li class="nav-item">
        <a class="nav-link <?php echo ($_GET['page'] ?? '') === 'dashboard' ? 'active' : ''; ?>" href="index.php">
          <i class="fas fa-tachometer-alt"></i>
          Dashboard
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?php echo ($_GET['page'] ?? '') === 'assets' ? 'active' : ''; ?>" href="index.php?page=assets">
          <i class="fas fa-laptop"></i>
          Assets
        </a>
      </li>
      <?php if (isAdmin()): ?>
      <li class="nav-item">
        <a class="nav-link <?php echo ($_GET['page'] ?? '') === 'users' ? 'active' : ''; ?>" href="index.php?page=users">
          <i class="fas fa-users"></i>
          Users
        </a>
      </li>
      <?php endif; ?>
      <li class="nav-item">
        <a class="nav-link <?php echo ($_GET['page'] ?? '') === 'allocations' ? 'active' : ''; ?>" href="index.php?page=allocations">
          <i class="fas fa-exchange-alt"></i>
          Allocations
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?php echo ($_GET['page'] ?? '') === 'reports' ? 'active' : ''; ?>" href="index.php?page=reports">
          <i class="fas fa-chart-bar"></i>
          Reports
        </a>
      </li>
    </ul>
  </div>
</nav>




</body>
</html>
