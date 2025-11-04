<?php
// Detectar página actual
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
        <a href="./index.php" class="navbar-link <?php echo $paginaActual === 'index.php' ? 'active' : ''; ?>">Inicio</a>
      </li>
      <li>
        <a href="./login.php" class="navbar-link <?php echo $paginaActual === 'login.php' ? 'active' : ''; ?>">Iniciar sesión</a>
      </li>
      <li>
        <a href="./registro.php" class="navbar-link <?php echo $paginaActual === 'registro.php' ? 'active' : ''; ?>">Registrarse</a>
      </li>
    </ul>
  </div>
</nav>
