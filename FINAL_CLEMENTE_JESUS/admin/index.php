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

    
  </div>

  <!-- Footer -->
  <?php include './footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>