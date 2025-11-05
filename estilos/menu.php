<?php
// Detectar página actual
require_once "./admin/includes/sessions.php";
$sesion = new Sessions();
$paginaActual = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar-custom">
  <div class="navbar-container">
    <div class="navbar-brand">
      <span class="navbar-logo">🏠</span>
      <h1 class="navbar-title">ApartaHome</h1>
    </div>
    <ul class="navbar-menu">
      <li>
        <a href="./index.php" class="navbar-link <?= $paginaActual === 'index.php' ? 'active' : ''; ?>">Inicio</a>
      </li>
  <?php if (!$sesion->comprobarSesion()) { ?>
      <li>
        <a href="./login.php" class="navbar-link <?= $paginaActual === 'login.php' ? 'active' : ''; ?>">Iniciar sesión</a>
      </li>
  <?php } ?>
      <li>
        <span class="text-white me-3">👤 <?= htmlspecialchars($_SESSION['username']) ?></span>
        <a href="./admin/includes/logout.php" class="navbar-link <?= $paginaActual === 'login.php' ? 'active' : ''; ?>" >Cerrar Sesión</a>
      </li>
      
      <li>
        <a href="./registro.php" class="navbar-link <?= $paginaActual === 'registro.php' ? 'active' : ''; ?>">Registrarse</a>
      </li>
    </ul>
  </div>
</nav>
