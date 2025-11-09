<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once "./includes/sessions.php";
$sesion = new Sessions();
if (!$sesion->comprobarSesion() || !in_array($_SESSION['usuario']['rol'], ["admin", "superAdmin"])) {
  header("Location: ../login.php");
  exit();
}
$usuario = $_SESSION['usuario']['username'];
require_once "./includes/crudUsuarios.php";
require_once "./includes/crudCasas.php";
require_once "./includes/crudReservas.php";
require_once "./includes/crudUbicacion.php";

$usuarioObj = new Usuarios();
$casaObj = new Casas();
$reservasObj = new Reservas();
$ubicacionObj = new Ubicacion();
$usuarios = $usuarioObj->getAll();
$casas = $casaObj->getAll();
$reservas = $reservasObj->getAll();

$comunidad = $ubicacionObj->getAllComunidades();
$provincia = $ubicacionObj->getAllProvincias();
$ciudad = $ubicacionObj->getAllCiudades();
$casas = $casaObj->getAll();
$casasVip = $casaObj->getCasasVip();
// Obtener datos

$totalUsuarios = $usuarioObj->getCantidadUsuarios();
$reservasConfirmadas = $reservasObj->getCantidadReservasConfirmadas();
$reservasCanceladas = $reservasObj->getCantidadReservasCanceladas();
$casasActivas = $casaObj->getCantidadCasas();



// Calcular estadísticas


//$reservasPendientes = count(array_filter($reservas, fn($r) => isset($r['estado']) && $r['estado'] === 'pendiente'));
//$reservasConfirmadas = count(array_filter($reservas, fn($r) => isset($r['estado']) && $r['estado'] === 'confirmada'));
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard Admin • VacacionalPlus</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../css/styles.css">
  <link rel="stylesheet" href="./assets/css/admin.css">
  </head>
<body class="admin-page bg-light">
  <!-- Navbar -->
  <?php include  './menu.php'; ?>

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
        <div class="card text-white  mb-3 shadow-sm">
          <div class="card-header">Total Usuarios</div>
          <div class="card-body">
            <h5 class="card-title display-6"><?= $totalUsuarios ?></h5>
            <p class="card-text">Propietarios y Huéspedes</p>           
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-white  mb-3 shadow-sm">
          <div class="card-header">Casas Activas</div>
          <div class="card-body">
            <h5 class="card-title display-6"><?= $casasActivas ?></h5>
            <p class="card-text">Disponibles para Reserva</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-white  mb-3 shadow-sm">
          <div class="card-header">Reservas Confirmadas</div>
          <div class="card-body">
            <h5 class="card-title display-6"><?= $reservasConfirmadas ?></h5>
            <p class="card-text">Activas en el sistema</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-white  mb-3 shadow-sm">
          <div class="card-header">Reservas Canceladas</div>
          <div class="card-body">
            <h5 class="card-title display-6"><?= $reservasCanceladas ?></h5>
            <p class="card-text">En Espera de Confirmación</p>
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
                  if ($estado === 'confirmada') $badgeClass = 'badge-accesible';
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
  <?php include './footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>