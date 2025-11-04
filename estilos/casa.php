<?php
session_start();
require_once "./admin/includes/sessions.php";
require_once "./admin/includes/crudCasas.php";
require_once "./admin/includes/crudReservas.php";

$sesion = new Sessions();
$casaObj = new Casas();
$reservasObj = new Reservas();

// ID de casa
$id_casa = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id_casa === 0) {
  header("Location: index.php");
  exit();
}

// Estructura de datos base
$datos_casa = [
  'id_casa' => '', 'nombre' => '', 'capacidad' => '', 'precio_noche' => '',
  'num_banos' => '', 'num_cocinas' => '', 'num_hab_individuales' => '',
  'num_hab_familiares' => '', 'num_aparcamientos' => '', 'num_lavadora' => '',
  'num_secadora' => '', 'num_lavavajillas' => '', 'num_horno' => '',
  'num_microondas' => '', 'num_nevera' => '', 'num_congelador' => '',
  'tiene_wifi' => false, 'num_ascensores' => '', 'tiene_calefaccion' => false,
  'tiene_aire_acondicionado' => false, 'tiene_piscina' => false, 'tiene_banera' => false,
  'tiene_barbacoa' => false, 'tiene_chimenea' => false, 'tiene_adaptacion_discapacitados' => false,
  'tiene_jardin' => false, 'tiene_patio' => false, 'tiene_sala_cine' => false,
  'tiene_secador_pelo' => false, 'imagen_principal' => '',
  'id_provincia' => '', 'id_ciudad' => '', 'provincia' => '', 'ciudad' => '',
];

// Obtener casa
$casa = $casaObj->getCasaById($id_casa);
if (!$casa) {
  header("Location: index.php");
  exit();
}
$datos_casa = $casa;
$imagenes = $casaObj->getImagenesByCasa($id_casa);

// Obtener reservas de la casa
$todas = $reservasObj->getAll();
$reservasCasa = array_filter($todas, fn($r) => $r['id_casa'] == $id_casa);

// Reserva
$errorReserva = '';
$exitoReserva = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  if (!$sesion->comprobarSesion()) {
    header("Location: login.php");
    exit();
  }

  $fecha_inicio = trim($_POST['fecha_inicio'] ?? '');
  $fecha_fin = trim($_POST['fecha_fin'] ?? '');
  $errores = [];

  if ($fecha_inicio === '') $errores[] = "La fecha de inicio no puede estar vacía.";
  if ($fecha_fin === '') $errores[] = "La fecha de fin no puede estar vacía.";

  if ($fecha_inicio !== '' && $fecha_fin !== '') {
    if (strtotime($fecha_fin) <= strtotime($fecha_inicio)) {
      $errores[] = "La fecha de fin debe ser posterior a la fecha de inicio.";
    }
    // Solapamiento
    $sol_inicio = strtotime($fecha_inicio);
    $sol_fin = strtotime($fecha_fin);
    foreach ($reservasCasa as $reserva) {
      $res_inicio = strtotime($reserva['fecha_inicio']);
      $res_fin = strtotime($reserva['fecha_fin']);
      if (($sol_inicio < $res_fin) && ($sol_fin > $res_inicio)) {
        $errores[] = "Estas fechas no están disponibles. Elige otras fechas.";
        break;
      }
    }
  }

  if (empty($errores)) {
    try {
      $id_usuario = $_SESSION['usuario']['id_usuario'];
      $dias = (new DateTime($fecha_inicio))->diff(new DateTime($fecha_fin))->days;
      $precio_total = $dias * $datos_casa['precio_noche'];

      $reservasObj->insertarReserva($id_usuario, $id_casa, $fecha_inicio, $fecha_fin, 'pendiente', $precio_total);

      $exitoReserva = "¡Reserva creada exitosamente! Está en estado pendiente.";
      // Refrescar reservas
      $todas = $reservasObj->getAll();
      $reservasCasa = array_filter($todas, fn($r) => $r['id_casa'] == $id_casa);
    } catch (Exception $e) {
      $errorReserva = "Error al crear la reserva: " . $e->getMessage();
    }
  } else {
    $errorReserva = implode("\n", $errores);
  }
}

$imagenPrincipal = htmlspecialchars($datos_casa['imagen_principal'] ?? './imagenes/default.jpg');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($datos_casa['nombre']) ?> - ApartaHome</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="./css/styles.css" />
  <style>
    .hero-casa { background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%); color: #fff; padding: 3rem 0; }
    .carousel-inner img { height: 400px; object-fit: cover; }
    .amenities-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin: 1.5rem 0; }
    .amenity { padding: 1rem; background: #f8fafc; border-radius: 8px; text-align: center; border-left: 4px solid #4f46e5; }
    .amenity.disabled { opacity: .5; border-left-color: #cbd5e1; }
    .form-reserva { background: #fff; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,.08); }
    .calendar-info { background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%); color: #fff; padding: 1.5rem; border-radius: 8px; margin: 1rem 0; }
  </style>
</head>
<body>
  <?php include("menu.php"); ?>

  <!-- Hero -->
  <div class="hero-casa">
    <div class="container py-4">
      <h1 class="display-4 fw-bold"><?= htmlspecialchars($datos_casa['nombre']) ?></h1>
      <p class="lead"><?= htmlspecialchars($datos_casa['provincia']) ?> • <?= htmlspecialchars($datos_casa['ciudad']) ?></p>
    </div>
  </div>

  <div class="main-container">
    <div class="row g-4">
      <!-- Galería -->
      <div class="col-lg-8">
        <?php if (!empty($imagenes)): ?>
          <div id="carouselCasa" class="carousel slide mb-4" data-bs-ride="carousel">
            <div class="carousel-inner rounded" style="border-radius: 12px; overflow: hidden;">
              <?php foreach ($imagenes as $idx => $imagen): ?>
                <div class="carousel-item <?= $idx === 0 ? 'active' : '' ?>">
                  <img src="<?= htmlspecialchars($imagen['url']) ?>" class="d-block w-100" alt="<?= htmlspecialchars($imagen['descripcion'] ?? 'Imagen de la casa') ?>">
                </div>
              <?php endforeach; ?>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselCasa" data-bs-slide="prev">
              <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselCasa" data-bs-slide="next">
              <span class="carousel-control-next-icon"></span>
            </button>
          </div>
        <?php else: ?>
          <img src="<?= $imagenPrincipal ?>" class="img-fluid rounded mb-4" alt="Imagen principal" />
        <?php endif; ?>

        <!-- Info rápida -->
        <div class="row g-3 mb-4">
          <div class="col-6 col-md-3">
            <div class="card text-center" style="border-top: 4px solid #4f46e5;">
              <div class="card-body">
                <h5 class="card-title" style="color:#4f46e5;"><?= htmlspecialchars($datos_casa['capacidad']) ?></h5>
                <p class="card-text small">Personas</p>
              </div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="card text-center" style="border-top: 4px solid #4f46e5;">
              <div class="card-body">
                <h5 class="card-title" style="color:#4f46e5;"><?= htmlspecialchars($datos_casa['num_hab_individuales'] + $datos_casa['num_hab_familiares']) ?></h5>
                <p class="card-text small">Habitaciones</p>
              </div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="card text-center" style="border-top: 4px solid #4f46e5;">
              <div class="card-body">
                <h5 class="card-title" style="color:#4f46e5;"><?= htmlspecialchars($datos_casa['num_banos']) ?></h5>
                <p class="card-text small">Baños</p>
              </div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="card text-center" style="border-top: 4px solid #f59e0b;">
              <div class="card-body">
                <h5 class="card-title" style="color:#f59e0b;"><?= number_format($datos_casa['precio_noche'], 2, ',', '.') ?>€</h5>
                <p class="card-text small">/noche</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Amenidades -->
        <div class="card mb-4">
          <div class="card-header" style="background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%); color: #fff;">
            <h5 class="mb-0">Servicios y Amenidades</h5>
          </div>
          <div class="card-body">
            <div class="amenities-grid">
              <div class="amenity <?= !$datos_casa['tiene_wifi'] ? 'disabled' : '' ?>">📶 WiFi <?= $datos_casa['tiene_wifi'] ? '✓' : '✗' ?></div>
              <div class="amenity <?= !$datos_casa['tiene_piscina'] ? 'disabled' : '' ?>">🏊 Piscina <?= $datos_casa['tiene_piscina'] ? '✓' : '✗' ?></div>
              <div class="amenity <?= !$datos_casa['tiene_aire_acondicionado'] ? 'disabled' : '' ?>">❄️ A/C <?= $datos_casa['tiene_aire_acondicionado'] ? '✓' : '✗' ?></div>
              <div class="amenity <?= !$datos_casa['tiene_calefaccion'] ? 'disabled' : '' ?>">🔥 Calefacción <?= $datos_casa['tiene_calefaccion'] ? '✓' : '✗' ?></div>
              <div class="amenity <?= !$datos_casa['tiene_barbacoa'] ? 'disabled' : '' ?>">🍖 Barbacoa <?= $datos_casa['tiene_barbacoa'] ? '✓' : '✗' ?></div>
              <div class="amenity <?= !$datos_casa['tiene_chimenea'] ? 'disabled' : '' ?>">🔥 Chimenea <?= $datos_casa['tiene_chimenea'] ? '✓' : '✗' ?></div>
              <div class="amenity <?= !$datos_casa['tiene_jardin'] ? 'disabled' : '' ?>">🌳 Jardín <?= $datos_casa['tiene_jardin'] ? '✓' : '✗' ?></div>
              <div class="amenity <?= !$datos_casa['tiene_patio'] ? 'disabled' : '' ?>">🏡 Patio <?= $datos_casa['tiene_patio'] ? '✓' : '✗' ?></div>
              <div class="amenity <?= !$datos_casa['tiene_sala_cine'] ? 'disabled' : '' ?>">🎬 Sala Cine <?= $datos_casa['tiene_sala_cine'] ? '✓' : '✗' ?></div>
              <div class="amenity <?= !$datos_casa['tiene_adaptacion_discapacitados'] ? 'disabled' : '' ?>">♿ Adaptado <?= $datos_casa['tiene_adaptacion_discapacitados'] ? '✓' : '✗' ?></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Sidebar reserva -->
      <div class="col-lg-4">
        <div class="form-reserva sticky-lg-top" style="top: 20px;">
          <h3 class="mb-3" style="color:#4f46e5;">Realizar Reserva</h3>

          <?php if ($exitoReserva): ?>
            <div class="alert alert-success"><?= $exitoReserva ?></div>
          <?php endif; ?>
          <?php if ($errorReserva): ?>
            <div class="alert alert-error" style="white-space: pre-line;"><?= $errorReserva ?></div>
          <?php endif; ?>

          <?php if ($sesion->comprobarSesion()): ?>
            <form method="POST" action="">
              <div class="mb-3">
                <label class="form-label fw-600">Fecha de entrada</label>
                <input type="date" name="fecha_inicio" class="form-input" required />
              </div>
              <div class="mb-3">
                <label class="form-label fw-600">Fecha de salida</label>
                <input type="date" name="fecha_fin" class="form-input" required />
              </div>

              <div class="calendar-info">
                <strong>Precio por noche:</strong><br />
                €<?= number_format($datos_casa['precio_noche'], 2, ',', '.') ?>
              </div>

              <button type="submit" class="btn btn-primary w-100 fw-600">Reservar Ahora</button>
            </form>
          <?php else: ?>
            <div class="alert alert-info">
              Debes <a href="login.php" class="link-text">iniciar sesión</a> para realizar una reserva.
            </div>
            <a href="login.php" class="btn btn-primary w-100 fw-600">Iniciar Sesión</a>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Disponibilidad -->
    <div class="card mt-5">
      <div class="card-header" style="background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%); color: #fff;">
        <h5 class="mb-0">Disponibilidad</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped">
            <thead class="table-light">
              <tr>
                <th>Fecha Entrada</th>
                <th>Fecha Salida</th>
                <th>Estado</th>
              </tr>
            </thead>
            <tbody>
              <?php if (count($reservasCasa) > 0): ?>
                <?php foreach ($reservasCasa as $reserva): ?>
                  <tr>
                    <td><?= date('d/m/Y', strtotime($reserva['fecha_inicio'])) ?></td>
                    <td><?= date('d/m/Y', strtotime($reserva['fecha_fin'])) ?></td>
                    <td><span class="badge bg-warning text-dark"><?= ucfirst($reserva['estado']) ?></span></td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="3" class="text-center text-muted">No hay reservas actualmente</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="mt-4 text-center">
      <a href="index.php" class="btn btn-outline-primary">← Volver al Catálogo</a>
    </div>
  </div>

  <?php include("footer.php"); ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
