    <!-- Navbar -->
    <nav class="navbar navbar-dark">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">🏠 Panel Administración </span>
            <div class="d-flex">
                <span class="text-white me-3">👤 <?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?></span>
                <a href="./includes/logout.php" class="btn btn-danger btn-sm">Cerrar Sesión</a>
            </div>
        </div>
    </nav>
    