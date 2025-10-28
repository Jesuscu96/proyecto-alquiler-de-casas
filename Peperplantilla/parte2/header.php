<?php
session_start();
$usuario_logueado = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo isset($page_title) ? $page_title : 'VacacionalPlus'; ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <style>
    :root {
      --primary-color: #FF5A5F;
      --secondary-color: #00A699;
      --dark-bg: #1a1a1a;
      --light-gray: #f7f7f7;
    }
    body { 
      font-family: 'Circular', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background-color: var(--light-gray);
      margin-left: 280px;
      padding: 2rem 3rem;
    }
    .side-header {
      position: fixed;
      left: 0;
      top: 0;
      width: 280px;
      height: 100vh;
      background: linear-gradient(180deg, var(--dark-bg) 0%, #2d2d2d 100%);
      padding: 2rem 1.5rem;
      box-shadow: 4px 0 20px rgba(0,0,0,0.1);
      z-index: 1000;
      overflow-y: auto;
    }
    .side-header .brand {
      display: flex;
      flex-direction: column;
      align-items: center;
      margin-bottom: 3rem;
      padding-bottom: 2rem;
      border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    .side-header .brand h1 {
      color: white;
      font-size: 1.8rem;
      font-weight: 700;
      margin: 0;
      letter-spacing: -0.5px;
    }
    .side-header .brand .tagline {
      color: var(--secondary-color);
      font-size: 0.85rem;
      margin-top: 0.5rem;
    }
    .side-nav {
      list-style: none;
      padding: 0;
      margin: 0;
    }
    .side-nav li {
      margin-bottom: 0.5rem;
    }
    .side-nav a {
      display: flex;
      align-items: center;
      padding: 1rem 1.2rem;
      color: rgba(255,255,255,0.8);
      text-decoration: none;
      border-radius: 12px;
      transition: all 0.3s ease;
      font-weight: 500;
    }
    .side-nav a:hover, .side-nav a.active {
      background: rgba(255,90,95,0.15);
      color: var(--primary-color);
      transform: translateX(5px);
    }
    .side-nav i {
      font-size: 1.3rem;
      margin-right: 1rem;
      width: 24px;
    }
    .user-profile {
      position: absolute;
      bottom: 2rem;
      left: 1.5rem;
      right: 1.5rem;
      background: rgba(255,255,255,0.05);
      padding: 1rem;
      border-radius: 12px;
      color: white;
    }
    .content-wrapper {
      margin-left: 280px;
      min-height: 100vh;
      padding: 2rem 3rem;
    }
    @media (max-width: 991px) {
      .side-header {
        width: 100%;
        height: auto;
        position: relative;
      }
      .content-wrapper {
        margin-left: 0;
        padding: 1rem;
      }
    }
  </style>
</head>
<body>
  <aside class="side-header">
    <div class="brand">
      <h1><i class="bi bi-house-heart-fill" style="color: var(--primary-color);"></i></h1>
      <h1>VacacionalPlus</h1>
      <span class="tagline">Tu hogar perfecto te espera</span>
    </div>

    <ul class="side-nav">
      <li><a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
        <i class="bi bi-search"></i> Explorar Casas
      </a></li>

      <?php if($usuario_logueado): ?>
        <li><a href="mis_reservas.php">
          <i class="bi bi-calendar-check"></i> Mis Reservas
        </a></li>
        <li><a href="agregar_casa.php">
          <i class="bi bi-house-add"></i> Publicar Casa
        </a></li>
        <li><a href="mis_casas.php">
          <i class="bi bi-buildings"></i> Mis Propiedades
        </a></li>

        <?php if($usuario_logueado['rol'] == 'admin'): ?>
          <li style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid rgba(255,255,255,0.1);">
            <a href="admin_dashboard.php">
              <i class="bi bi-speedometer2"></i> Dashboard Admin
            </a>
          </li>
          <li><a href="admin_usuarios.php">
            <i class="bi bi-people"></i> Gestión Usuarios
          </a></li>
          <li><a href="admin_reservas.php">
            <i class="bi bi-clipboard-data"></i> Gestión Reservas
          </a></li>
        <?php endif; ?>

      <?php else: ?>
        <li><a href="login.php">
          <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
        </a></li>
        <li><a href="registro.php">
          <i class="bi bi-person-plus"></i> Registrarse
        </a></li>
      <?php endif; ?>
    </ul>

    <?php if($usuario_logueado): ?>
    <div class="user-profile">
      <div class="d-flex align-items-center">
        <div class="flex-grow-1">
          <div class="fw-bold"><?php echo htmlspecialchars($usuario_logueado['nombre']); ?></div>
          <small class="text-muted"><?php echo htmlspecialchars($usuario_logueado['email']); ?></small>
        </div>
        <a href="logout.php" class="btn btn-sm btn-outline-light"><i class="bi bi-box-arrow-right"></i></a>
      </div>
    </div>
    <?php endif; ?>
  </aside>

  <div class="content-wrapper">
