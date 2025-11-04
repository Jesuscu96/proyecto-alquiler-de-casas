<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once "./includes/sessions.php";
$sesion = new Sessions();
if (!$sesion->comprobarSesion()) {
    header("Location: ../login.php");
    exit();
}
$usuario = $_SESSION['usuario']['username'];
require_once "./includes/crudUsuarios.php";
require_once "./includes/crudCasas.php";
require_once "./includes/crudReservas.php";
require_once "./includes/sessions.php";
require_once "./includes/crudUbicacion.php";

$usuariosObj = new Usuarios();
$casaObj = new Casas();
$reservasObj = new Reservas();
$ubicacionObj = new Ubicacion();
$usuarios = $usuariosObj->getAll();
$casas = $casaObj->getAll();
$reservas = $reservasObj->getAll();

$comunidad = $ubicacionObj->getAllComunidades();
$provincia = $ubicacionObj->getAllProvincias();
$ciudad = $ubicacionObj->getAllCiudades();
$casas = $casaObj->getAll();
$casasVip = $casaObj->getCasasVip();




// Calcular estadísticas
$totalUsuarios = count($usuarios);
$casasActivas = count($casas);
//$reservasPendientes = count(array_filter($reservas, fn($r) => isset($r['estado']) && $r['estado'] === 'pendiente'));
//$reservasConfirmadas = count(array_filter($reservas, fn($r) => isset($r['estado']) && $r['estado'] === 'confirmada'));
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard Admin • VacacionalPlus</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="./admin/assets/css/styles.css">
</head>
<body class="bg-light">
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
      <a class="navbar-brand" href="admin.php">Vacacional<span class="brand-highlight">Plus</span> Admin</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="mainNav">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link active" href="admin.php">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="usuarios.php">Usuarios</a></li>
          <li class="nav-item"><a class="nav-link" href="casas2.php">Casas</a></li>
          <li class="nav-item"><a class="nav-link" href="reservas.php">Reservas</a></li>
          <li class="nav-item"><a class="nav-link" href="logout.php">Cerrar Sesión</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Hero / Header -->
  <header class="hero">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-12 text-center">
          <h1 class="display-4 fw-bold">Panel de Administración</h1>
          <p class="lead mb-3">Bienvenido al sistema de gestión de VacacionalPlus</p>
        </div>
      </div>
    </div>
  </header>

  <!-- Contenido principal -->
  <div class="container py-4">
    <!-- Tarjetas de estadísticas principales -->
    <div class="row mb-4">
      <div class="col-md-3">
        <div class="card text-white bg-primary mb-3 shadow-sm">
          <div class="card-header">Total Usuarios</div>
          <div class="card-body">
            <h5 class="card-title display-6"><?= $totalUsuarios ?></h5>
            <p class="card-text">Propietarios y Huéspedes</p>
            <a href="gestion-usuarios.php" class="btn btn-light btn-sm">Ver todos</a>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-white bg-success mb-3 shadow-sm">
          <div class="card-header">Casas Activas</div>
          <div class="card-body">
            <h5 class="card-title display-6"><?= $casasActivas ?></h5>
            <p class="card-text">Disponibles para Reserva</p>
            <a href="gestion-casas.php" class="btn btn-light btn-sm">Gestionar</a>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-white bg-warning mb-3 shadow-sm">
          <div class="card-header">Reservas Pendientes</div>
          <div class="card-body">
            <h5 class="card-title display-6"><?= $reservasPendientes ?></h5>
            <p class="card-text">En Espera de Confirmación</p>
            <a href="gestion-reservas.php" class="btn btn-light btn-sm">Revisar</a>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-white bg-info mb-3 shadow-sm">
          <div class="card-header">Reservas Confirmadas</div>
          <div class="card-body">
            <h5 class="card-title display-6"><?= $reservasConfirmadas ?></h5>
            <p class="card-text">Activas en el sistema</p>
            <a href="gestion-reservas.php" class="btn btn-light btn-sm">Ver todas</a>
          </div>
        </div>
      </div>
    </div>

    <!-- Accesos rápidos -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="card shadow-sm">
          <div class="card-header bg-white">
            <h4 class="mb-0">Accesos Rápidos</h4>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-4">
                <a href="gestion-usuarios.php" class="btn btn-outline-primary btn-lg w-100">
                  👤 Gestionar Usuarios
                </a>
              </div>
              <div class="col-md-4">
                <a href="gestion-casas.php" class="btn btn-outline-success btn-lg w-100">
                  🏡 Gestionar Casas
                </a>
              </div>
              <div class="col-md-4">
                <a href="gestion-reservas.php" class="btn btn-outline-warning btn-lg w-100">
                  📅 Gestionar Reservas
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Últimas reservas -->
    <div class="card shadow-sm">
      <div class="card-header bg-white">
        <h4 class="mb-0">Últimas Reservas</h4>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead class="table-dark">
              <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Casa</th>
                <th>Fechas</th>
                <th>Estado</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php 
              $ultimasReservas = array_slice($reservas, 0, 5);
              foreach ($ultimasReservas as $reserva) : 
              ?>
              <tr>
                <td><?= $reserva['id_reserva'] ?></td>
                <td>
                  <?php
                  $usuario = array_filter($usuarios, fn($u) => $u['id_usuario'] == $reserva['id_usuario']);
                  $usuario = reset($usuario);
                  echo $usuario ? htmlspecialchars($usuario['nombre']) : 'N/A';
                  ?>
                </td>
                <td>
                  <?php
                  $casa = array_filter($casas, fn($c) => $c['id_casa'] == $reserva['id_casa']);
                  $casa = reset($casa);
                  echo $casa ? htmlspecialchars($casa['nombre']) : 'N/A';
                  ?>
                </td>
                <td>
                  <?= date('d/m/Y', strtotime($reserva['fecha_inicio'])) ?> - 
                  <?= date('d/m/Y', strtotime($reserva['fecha_fin'])) ?>
                </td>
                <td>
                  <?php
                  $estado = $reserva['estado'];
                  $badgeClass = 'bg-secondary';
                  if ($estado === 'confirmada') $badgeClass = 'bg-success';
                  elseif ($estado === 'pendiente') $badgeClass = 'bg-warning text-dark';
                  elseif ($estado === 'cancelada') $badgeClass = 'bg-danger';
                  ?>
                  <span class="badge <?= $badgeClass ?>"><?= ucfirst($estado) ?></span>
                </td>
                <td>
                  <a href="gestion-reservas.php" class="btn btn-sm btn-outline-primary">Ver</a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <div class="text-center mt-3">
          <a href="gestion-reservas.php" class="btn btn-primary">Ver todas las reservas</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="site-footer py-4 mt-5">
    <div class="container">
      <div class="row">
        <div class="col-md-6">
          <p class="footer-brand mb-2">VacacionalPlus Admin</p>
          <p class="footer-note">Panel de administración para gestión de alquileres vacacionales.</p>
        </div>
        <div class="col-md-6 text-md-end">
          <p class="footer-note mb-1">&copy; 2025 VacacionalPlus. Todos los derechos reservados.</p>
        </div>
      </div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>