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

      $reservasObj->insertarReserva($id_usuario, $id_casa, $fecha_inicio, $fecha_fin, $precio_total, 'confirmada');
      header("Location: casa.php?id=" . $id_casa);
      exit();
      //para refrescar 
      $todas = $reservasObj->getAll();
      $exitoReserva = "¡Reserva creada exitosamente! Está en estado pendiente.";
      
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
            <div class="carousel-inner rounded">
              <?php foreach ($imagenes as $idx => $imagen): ?>
                <div class="carousel-item <?= $idx === 0 ? 'active' : '' ?>">
                  <img src="./imagenes/<?= htmlspecialchars($imagen['url']) ?>" class="d-block w-100" alt="<?= htmlspecialchars($imagen['descripcion'] ?? 'Imagen de la casa') ?>">
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
          <img src="./imagenes/<?= $imagenPrincipal ?>" class="img-fluid rounded mb-4" alt="Imagen principal" />
        <?php endif; ?>

        <!-- Info rápida -->
        <div class="row g-3 mb-4">
          <div class="col-6 col-md-3">
            <div class="card text-center card-info-basic">
              <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($datos_casa['capacidad']) ?></h5>
                <p class="card-text small">Personas</p>
              </div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="card text-center card-info-basic">
              <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($datos_casa['num_hab_individuales'] + $datos_casa['num_hab_familiares']) ?></h5>
                <p class="card-text small">Habitaciones</p>
              </div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="card text-center card-info-basic">
              <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($datos_casa['num_banos']) ?></h5>
                <p class="card-text small">Baños</p>
              </div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="card text-center card-info-price">
              <div class="card-body">
                <h5 class="card-title"><?= number_format($datos_casa['precio_noche'], 2, ',', '.') ?>€</h5>
                <p class="card-text small">€/noche</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Amenidades -->
        <div class="card mb-4">
          <div class="card-header gradient-primary">
            <h5 class="mb-0">Servicios y Amenidades</h5>
          </div>
          <div class="card-body">
            <div class="amenities-grid">
              <div class="amenity <?= !$datos_casa['tiene_wifi'] ? 'disabled' : '' ?>"><i class="bi bi-wifi"></i> WiFi <?= $datos_casa['tiene_wifi'] ? '✓' : '✗' ?></div>
              <div class="amenity <?= !$datos_casa['tiene_piscina'] ? 'disabled' : '' ?>"><i class="bi bi-water"></i> Piscina <?= $datos_casa['tiene_piscina'] ? '✓' : '✗' ?></div>
              <div class="amenity <?= !$datos_casa['tiene_aire_acondicionado'] ? 'disabled' : '' ?>"><i class="bi bi-thermometer-snow"></i> A/C <?= $datos_casa['tiene_aire_acondicionado'] ? '✓' : '✗' ?></div>
              <div class="amenity <?= !$datos_casa['tiene_calefaccion'] ? 'disabled' : '' ?>"><i class="bi bi-thermometer-sun"></i></i> Calefacción <?= $datos_casa['tiene_calefaccion'] ? '✓' : '✗' ?></div>
              <div class="amenity <?= !$datos_casa['tiene_barbacoa'] ? 'disabled' : '' ?>"><i class="bi bi-fork-knife"></i> Barbacoa <?= $datos_casa['tiene_barbacoa'] ? '✓' : '✗' ?></div>
              <div class="amenity <?= !$datos_casa['tiene_chimenea'] ? 'disabled' : '' ?>"><i class="bi bi-fire"></i> Chimenea <?= $datos_casa['tiene_chimenea'] ? '✓' : '✗' ?></div>
              <div class="amenity <?= !$datos_casa['tiene_jardin'] ? 'disabled' : '' ?>"><i class="bi bi-tree-fill"></i> Jardín <?= $datos_casa['tiene_jardin'] ? '✓' : '✗' ?></div>
              <div class="amenity <?= !$datos_casa['tiene_patio'] ? 'disabled' : '' ?>"><i class="bi bi-house-door"></i> Patio <?= $datos_casa['tiene_patio'] ? '✓' : '✗' ?></div>
              <div class="amenity <?= !$datos_casa['tiene_sala_cine'] ? 'disabled' : '' ?>"><i class="bi bi-film"></i> Sala Cine <?= $datos_casa['tiene_sala_cine'] ? '✓' : '✗' ?></div>
              <div class="amenity <?= !$datos_casa['tiene_adaptacion_discapacitados'] ? 'disabled' : '' ?>"><i class="bi bi-person-wheelchair"></i> Adaptado <?= $datos_casa['tiene_adaptacion_discapacitados'] ? '✓' : '✗' ?></div>
              <div class="amenity <?= !$datos_casa['tiene_secador_pelo'] ? 'disabled' : '' ?>"><i class="bi bi-wind"></i> Secador Pelo <?= $datos_casa['tiene_secador_pelo'] ? '✓' : '✗' ?></div>
              <div class="amenity <?= !$datos_casa['num_cocinas'] ? 'disabled' : '' ?>"><i class="bi bi-fork-knife"></i> Coninas <?= $datos_casa['num_cocinas'] ? htmlspecialchars($datos_casa['num_cocinas']) : '✗' ?></div>
              <div class="amenity <?= !$datos_casa['num_microondas'] ? 'disabled' : '' ?>"> <!-- No encontre un icono del microondas y use este svg -->
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-microwave" viewBox="0 0 16 16">
                <!-- Cuerpo del microondas -->
                <rect x="1" y="3" width="14" height="10" rx="1" ry="1" stroke="currentColor" fill="none" stroke-width="1"/>
                <!-- Puerta del microondas -->
                <rect x="2" y="4" width="10" height="8" rx="0.5" ry="0.5" fill="currentColor" fill-opacity="0.1"/>
                <!-- Botones -->
                <circle cx="13" cy="5" r="0.5" fill="currentColor"/>
                <circle cx="13" cy="7" r="0.5" fill="currentColor"/>
                <circle cx="13" cy="9" r="0.5" fill="currentColor"/>
                <!-- Indicador de ondas de calor -->
                <path d="M3 6c1 1 2-1 3 0s2-1 3 0" stroke="currentColor" fill="none" stroke-width="0.5"/>
              </svg>
              Microondas <?= $datos_casa['num_microondas'] ? htmlspecialchars($datos_casa['num_microondas']) : '✗' ?></div>
              <div class="amenity <?= !$datos_casa['num_horno'] ? 'disabled' : '' ?>"> <!-- No encontre un icono del horno y use este svg -->
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-oven" viewBox="0 0 16 16">
                  <!-- Cuerpo del horno -->
                  <rect x="1" y="2" width="14" height="12" rx="1" ry="1" stroke="currentColor" fill="none" stroke-width="1"/>
                  
                  <!-- Puerta del horno -->
                  <rect x="2" y="4" width="12" height="8" rx="0.5" ry="0.5" fill="currentColor" fill-opacity="0.1"/>
                  
                  <!-- Botones -->
                  <circle cx="3.5" cy="3" r="0.5" fill="currentColor"/>
                  <circle cx="5" cy="3" r="0.5" fill="currentColor"/>
                  <circle cx="6.5" cy="3" r="0.5" fill="currentColor"/>
                  
                  <!-- Parrilla interna -->
                  <line x1="3" y1="6" x2="13" y2="6" stroke="currentColor" stroke-width="0.5"/>
                  <line x1="3" y1="7" x2="13" y2="7" stroke="currentColor" stroke-width="0.5"/>
                  <line x1="3" y1="8" x2="13" y2="8" stroke="currentColor" stroke-width="0.5"/>
                </svg>
 
              Hornos <?= $datos_casa['num_horno'] ? htmlspecialchars($datos_casa['num_horno']) : '✗' ?></div>
              <div class="amenity <?= !$datos_casa['num_nevera'] ? 'disabled' : '' ?>"><i class="bi bi-snow"></i> Neveras <?= $datos_casa['num_nevera'] ? htmlspecialchars($datos_casa['num_nevera']) : '✗' ?></div>
              <div class="amenity <?= !$datos_casa['num_congelador'] ? 'disabled' : '' ?>"><i class="bi bi-snow2"></i>Congeladores<?= $datos_casa['num_congelador'] ? htmlspecialchars($datos_casa['num_congelador']) : '✗' ?></div>
              <div class="amenity <?= !$datos_casa['num_lavavajillas'] ? 'disabled' : '' ?>"><i class="bi bi-droplet"></i> Lavajillas <?= $datos_casa['num_lavavajillas'] ? htmlspecialchars($datos_casa['num_lavavajillas']) : '✗' ?></div>
              <div class="amenity <?= !$datos_casa['num_lavadora'] ? 'disabled' : '' ?>"><i class="bi bi-droplet-half"></i> Lavadoras <?= $datos_casa['num_lavadora'] ? htmlspecialchars($datos_casa['num_lavadora']) : '✗' ?></div>
              <div class="amenity <?= !$datos_casa['num_secadora'] ? 'disabled' : '' ?>"><i class="bi bi-fan"></i> Secadoras <?= $datos_casa['num_secadora'] ? htmlspecialchars($datos_casa['num_secadora']) : '✗' ?></div>
              <div class="amenity <?= !$datos_casa['tiene_banera'] ? 'disabled' : '' ?>"><i class="bi bi-hdmi-fill"></i> Bañeras <?= $datos_casa['tiene_banera'] ? htmlspecialchars($datos_casa['tiene_banera']) : '✗' ?></div>
              <div class="amenity <?= !$datos_casa['num_aparcamientos'] ? 'disabled' : '' ?>"><i class="bi bi-car-front"></i> Palzas coche <?= $datos_casa['num_aparcamientos'] ? htmlspecialchars($datos_casa['num_aparcamientos']) : '✗' ?></div>
              <div class="amenity <?= !$datos_casa['num_ascensores'] ? 'disabled' : '' ?>"><i class="bi bi-arrow-down-up"></i> Ascensores <?= $datos_casa['num_ascensores'] ? htmlspecialchars($datos_casa['num_ascensores']) : '✗' ?></div>
              
      

            </div>
          </div>
        </div>
      </div>

      <!-- Sidebar reserva -->
      <div class="col-lg-4">
        <div class="form-reserva sticky-lg-top sticky-sidebar">
          <h3 class="mb-3 reserva-heading">Realizar Reserva</h3>

          <?php if ($exitoReserva): ?>
            <div class="alert alert-success"><?= $exitoReserva ?></div>
          <?php endif; ?>
          <?php if ($errorReserva): ?>
            <div class="alert alert-error whitespace-pre"><?= $errorReserva ?></div>
          <?php endif; ?>

          <?php if ($sesion->comprobarSesion()): ?>
            <form method="POST" action="">
              <div class="mb-3">
                <label class="form-label fw-600">Fecha de entrada</label>
                <input type="date" name="fecha_inicio" class="form-input" />
              </div>
              <div class="mb-3">
                <label class="form-label fw-600">Fecha de salida</label>
                <input type="date" name="fecha_fin" class="form-input" />
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
      <div class="card-header gradient-primary">
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
                    <td><span class="badge bg-warning text-dark"><?= htmlspecialchars($reserva['estado']) ?></span></td>
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
