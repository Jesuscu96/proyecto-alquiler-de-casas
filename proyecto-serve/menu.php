<?php
// Detectar página actual
require_once "./admin/includes/sessions.php";
$sesion = new Sessions();
$paginaActual = basename($_SERVER['PHP_SELF']);
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
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
  <?php if ($sesion->comprobarSesion()) : ?>
      <li>
        <a href="./añadircasa.php" class="navbar-link <?= $paginaActual === 'añadircasa.php' ? 'active' : ''; ?>"><i class="bi bi-house-gear-fill"></i></a>
      </li>
      <li>
        <a href="./admin/includes/logout.php" class="navbar-link <?= $paginaActual === './admin/includes/logout.php' ? 'active' : ''; ?>" ><i class="bi bi-box-arrow-left"></i> Cerrar Sesión</a>
        <span class="text-white me-3"><i class="bi bi-person-circle"></i> <?= htmlspecialchars($_SESSION['usuario']['username']) ?></span>
      </li>
  <?php else : ?>
      <li>
        <a href="./login.php" class="navbar-link <?= $paginaActual === 'login.php' ? 'active' : ''; ?>">Iniciar sesión</a>
      </li>
      <li>
        <a href="./registro.php" class="navbar-link <?= $paginaActual === 'registro.php' ? 'active' : ''; ?>">Registrarse</a>
      </li>
  <?php endif;?>
      
    </ul>
  </div>
</nav>
