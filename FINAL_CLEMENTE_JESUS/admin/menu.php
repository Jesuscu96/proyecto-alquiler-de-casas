<?php
// Detectar página actual
require_once "./includes/sessions.php";
$sesion = new Sessions();
$paginaActual = basename($_SERVER['PHP_SELF']);
?>

<link rel="stylesheet"  href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
<nav class="navbar-custom">
  <div class="navbar-container">
    <div class="navbar-brand">
      <span class="navbar-logo"><i class="bi bi-house-door-fill"></i></span>
      <h1 class="navbar-title">Panel Administración</h1>
    </div>
    <ul class="navbar-menu">
      <li>
        <a href="./index.php" class="navbar-link <?= $paginaActual === 'index.php' ? 'active' : ''; ?>">Inicio</a>
      </li>
      <li>
        <a href="../index.php" class="navbar-link <?= $paginaActual === '../index.php' ? 'active' : ''; ?>"><i class="bi bi-house-door-fill"></i>ApartaHome</a>
      </li>
      <li>
        <a href="./usuarios.php" class="navbar-link <?= $paginaActual === 'usuarios.php' ? 'active' : ''; ?>">Ususarios</a>
      </li>
      <li>
        <a href="./casas.php" class="navbar-link <?= $paginaActual === 'casas.php' ? 'active' : ''; ?>">Casas</a>
      </li>
      <li>
        <a href="./reservas.php" class="navbar-link <?= $paginaActual === 'reservas.php' ? 'active' : ''; ?>">Reservas</a>
      </li>
      
  <?php if ($sesion->comprobarSesion()) : ?>
      <li>
        <a href="./includes/logout.php" class="navbar-link <?= $paginaActual === './includes/logout.php' ? 'active' : ''; ?>" ><i class="bi bi-box-arrow-left"></i> Cerrar Sesión</a>
        <span class="text-white me-3"><i class="bi bi-person-circle"></i> <?= htmlspecialchars($_SESSION['usuario']['username']) ?></span>
      </li>
  <?php else : ?>
      <li>
        <a href="../login.php" class="navbar-link <?= $paginaActual === 'login.php' ? 'active' : ''; ?>">Iniciar sesión</a>
      </li>
  <?php endif;?>
      
    </ul>
  </div>
</nav>
