<nav class="navbar navbar-expand-lg fixed-top px-5 py-3"
     style="background: linear-gradient(135deg, #0f2027, #203a43, #2c5364); box-shadow: 0 6px 16px rgba(0, 0, 0, 0.5); font-family: 'Poppins', sans-serif; font-size: 1.4rem; height: 80px;">

    <!-- Logo and nextASSETS moved further right -->
    <a class="navbar-brand d-flex align-items-center text-white" href="index.php"
       style="font-size: 2rem; margin-left: 100px;">
        <img src="assets/logo.png" alt="Logo" style="height: 55px; margin-right: 15px;">
        nextASSETS
    </a>

    <!-- Right-aligned welcome & logout -->
    <div class="ms-auto d-flex align-items-center">
        <span class="text-white me-4" style="font-size: 1.2rem;">
            Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
        </span>
        <a href="logout.php" class="btn btn-outline-light btn-sm"
           style="padding: 0.5rem 1rem; font-size: 1rem; border-radius: 12px;">Logout</a>
    </div>
</nav>
