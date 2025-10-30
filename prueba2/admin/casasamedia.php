<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "./includes/crudCasas.php";
require_once "./includes/crudUbicacion.php";
require_once "./includes/crudUsuarios.php";
require_once "./includes/sessions.php";

// Verificar sesión de administrador
// verificarSesionAdmin();

$casaObj = new Casas();
$ubicacionObj = new Ubicacion();
$usuariosObj = new Usuarios();

$casas = $casaObj->getAll();
$comunidades = $ubicacionObj->getAllComunidades();
$provincias = $ubicacionObj->getAllProvincias();
$ciudades = $ubicacionObj->getAllCiudades();
$propietarios = $usuariosObj->getAll();

$accion = $_GET['accion'] ?? null;
$id = $_GET['id'] ?? null;

if($accion == "eliminar" && $id) {
    $casaObj->eliminarCasa($id);
    $mensaje = "Casa eliminada correctamente.";
}
$datos_casa = [
    'id_propietario' => '',
    'id_comunidad' => '',
    'id_provincia' => '',
    'id_ciudad' => '',
    'nombre' => '',
    'capacidad' => '',
    'precio_noche' => '',
    'num_banos' => '',
    'num_cocinas' => '',
    'num_hab_individuales' => '',
    'num_hab_familiares' => '',
    'num_aparcamientos' => '',
    'num_lavadora' => '',
    'num_secadora' => '',
    'num_lavavajillas' => '',
    'num_horno' => '',
    'num_microondas' => '',
    'num_nevera' => '',
    'num_congelador' => '',
    'tiene_wifi' => '',
    'num_ascensores' => '',
    'tiene_calefaccion' => '',
    'tiene_aire_acondicionado' => '',
    'tiene_piscina' => '',
    'tiene_banera' => '',
    'tiene_barbacoa' => '',
    'tiene_chimenea' => '',
    'tiene_adaptacion_discapacitados' => '',
    'tiene_jardin' => '',
    'tiene_patio' => '',
    'tiene_sala_cine' => '',
    'tiene_secador_pelo' => '',
    'imagen_principal' => ''
]; //para que el value del formulario salga vaci­o

if ($accion === "editar" && $id) {
  $datos_casa = $casaObj->getCasaById($id); 
}
// Procesar el formulario de creacion o edicion de categorÃ­a
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_propietario = $_POST['id_propietario'] ?? '';
    $id_comunidad = $_POST['id_comunidad'] ?? '';
    $id_provincia = $_POST['id_provincia'] ?? '';
    $id_ciudad = $_POST['id_ciudad'] ?? '';
    $nombre = $_POST['nombre'] ?? '';
    $capacidad = $_POST['capacidad'] ?? '';
    $precio_noche = $_POST['precio_noche'] ?? '';
    $num_banos = $_POST['num_banos'] ?? '';
    $num_cocinas = $_POST['num_cocinas'] ?? '';
    $num_hab_individuales = $_POST['num_hab_individuales'] ?? '';
    $num_hab_familiares = $_POST['num_hab_familiares'] ?? '';
    $num_aparcamientos = $_POST['num_aparcamientos'] ?? '';
    $num_lavadora = $_POST['num_lavadora'] ?? '';
    $num_secadora = $_POST['num_secadora'] ?? '';
    $num_lavavajillas = $_POST['num_lavavajillas'] ?? '';
    $num_horno = $_POST['num_horno'] ?? '';
    $num_microondas = $_POST['num_microondas'] ?? '';
    $num_nevera = $_POST['num_nevera'] ?? '';
    $num_congelador = $_POST['num_congelador'] ?? '';
    $tiene_wifi = $_POST['tiene_wifi'] ?? 0;
    $num_ascensores = $_POST['num_ascensores'] ?? 0;
    $tiene_calefaccion = $_POST['tiene_calefaccion'] ?? 0;
    $tiene_aire_acondicionado = $_POST['tiene_aire_acondicionado'] ?? 0;
    $tiene_piscina = $_POST['tiene_piscina'] ?? 0;
    $tiene_banera = $_POST['tiene_banera'] ?? 0;
    $tiene_barbacoa = $_POST['tiene_barbacoa'] ?? 0;
    $tiene_chimenea = $_POST['tiene_chimenea'] ?? 0;
    $tiene_adaptacion_discapacitados = $_POST['tiene_adaptacion_discapacitados'] ?? 0;
    $tiene_jardin = $_POST['tiene_jardin'] ?? 0;
    $tiene_patio = $_POST['tiene_patio'] ?? 0;
    $tiene_sala_cine = $_POST['tiene_sala_cine'] ?? 0;
    $tiene_secador_pelo = $_POST['tiene_secador_pelo'] ?? 0;
    $imagen_principal = $_POST['imagen_principal'] ?? '';
    
    if ($accion === "crear" ) {
        $casaObj->insertarCasa($id_propietario, $id_comunidad, $id_provincia, $id_ciudad,
        $nombre, $capacidad, $precio_noche,
        $num_banos, $num_cocinas, $num_hab_individuales, $num_hab_familiares,
        $num_aparcamientos, $num_lavadora, $num_secadora, $num_lavavajillas,
        $num_horno, $num_microondas, $num_nevera, $num_congelador,
        $tiene_wifi, $num_ascensores, $tiene_calefaccion, $tiene_aire_acondicionado,
        $tiene_piscina, $tiene_banera, $tiene_barbacoa, $tiene_chimenea,
        $tiene_adaptacion_discapacitados, $tiene_jardin, $tiene_patio, $tiene_sala_cine,
        $tiene_secador_pelo, $imagen_principal);
        header("Location: casas.php");
        exit();
        
    } elseif ($accion === "editar" && $id) {
        // Actualización sin cambiar contraseña
        $casaObj->actualizarCasa($id_casa, $id_propietario, $id_comunidad, $id_provincia, $id_ciudad, $nombre, $capacidad, $precio_noche,
        $num_banos, $num_cocinas, $num_hab_individuales, $num_hab_familiares,
        $num_aparcamientos, $num_lavadora, $num_secadora, $num_lavavajillas,
        $num_horno, $num_microondas, $num_nevera, $num_congelador,
        $tiene_wifi, $num_ascensores, $tiene_calefaccion, $tiene_aire_acondicionado,
        $tiene_piscina, $tiene_banera, $tiene_barbacoa, $tiene_chimenea,
        $tiene_adaptacion_discapacitados, $tiene_jardin, $tiene_patio, $tiene_sala_cine,
        $tiene_secador_pelo, $imagen_principal);
        
    }
    header("Location: casas.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Gestión de Casas • VacacionalPlus Admin</title>
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
          <li class="nav-item"><a class="nav-link" href="admin.php">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="gestion-usuarios.php">Usuarios</a></li>
          <li class="nav-item"><a class="nav-link active" href="gestion-casas.php">Casas</a></li>
          <li class="nav-item"><a class="nav-link" href="gestion-reservas.php">Reservas</a></li>
          <li class="nav-item"><a class="nav-link" href="logout.php">Cerrar Sesión</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Hero / Header -->
  <header class="hero">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-8">
          <h1 class="display-5 fw-bold">Gestión de Casas Vacacionales</h1>
          <p class="lead mb-3">Administra propiedades, servicios y disponibilidad de alquiler.</p>
        </div>
        <div class="col-lg-4 text-end">
          <button class="btn btn-warning btn-lg" data-bs-toggle="modal" data-bs-target="#modalNuevaCasa">
            + Nueva Casa
          </button>
        </div>
      </div>
    </div>
  </header>

  <!-- Contenido principal -->
  <div class="container py-4">
    <!-- Tarjetas de estadísticas -->
    <div class="row mb-4">
      <div class="col-md-3">
        <div class="card text-white bg-primary mb-3">
          <div class="card-header">Total Casas</div>
          <div class="card-body">
            <h5 class="card-title"><?= count($casas) ?></h5>
            <p class="card-text">Propiedades registradas</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-white bg-success mb-3">
          <div class="card-header">Disponibles</div>
          <div class="card-body">
            <h5 class="card-title">
                En desarrollo
              <?php
              //$disponibles = array_filter($casas, fn($c) => isset($c['disponible']) && $c['disponible']);
              //echo count($disponibles);
              ?>
            </h5>
            <p class="card-text">Para reservar</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-white bg-warning mb-3">
          <div class="card-header">Con Piscina</div>
          <div class="card-body">
            <h5 class="card-title">
              <?php
              $conPiscina = array_filter($casas, fn($c) => isset($c['tiene_piscina']) && $c['tiene_piscina']);
              echo count($conPiscina);
              ?>
            </h5>
            <p class="card-text">Amenidad premium</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-white bg-info mb-3">
          <div class="card-header">VIP</div>
          <div class="card-body">
            <h5 class="card-title">
                En desarrollo
              <?php
              $vip = array_filter($casas, fn($c) => isset($c['es_vip']) && $c['es_vip']);
              echo count($vip);
              ?>
            </h5>
            <p class="card-text">Destacadas</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Filtros rápidos -->
    <div class="card shadow-sm mb-4">
      <div class="card-body">
        <form class="row g-3">
          <div class="col-md-3">
            <label class="form-label small">Provincia</label>
            <select class="form-select" name="provincia">
              <option selected>Todas</option>
              <?php foreach ($provincias as $prov) : ?>
                <option value="<?= $prov['id_provincia'] ?>"><?= htmlspecialchars($prov['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label small">Ciudad</label>
            <select class="form-select" name="ciudad">
              <option selected>Todas</option>
              <?php foreach ($ciudades as $city) : ?>
                <option value="<?= $city['id_ciudad'] ?>"><?= htmlspecialchars($city['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label small">Capacidad mín.</label>
            <input type="number" class="form-control" name="capacidad" placeholder="0">
          </div>
          <div class="col-md-2">
            <label class="form-label small">Precio máx.</label>
            <input type="number" class="form-control" name="precio" placeholder="999">
          </div>
          <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Filtrar</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Tabla de casas -->
    <div class="card shadow-sm">
      <div class="card-header bg-white">
        <h4 class="mb-0">Lista de Casas Vacacionales</h4>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead class="table-dark">
              <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Ubicación</th>
                <th>Capacidad</th>
                <th>Precio/Noche</th>
                <th>Servicios</th>
                <th>Estado</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($casas as $casa) : ?>
              <tr>
                <td><?= $casa['id_casa'] ?></td>
                <td><?= htmlspecialchars($casa['nombre']) ?></td>
                <td>
                  <small>
                    <?= htmlspecialchars($casa['ciudad'] ?? 'N/A') ?>, 
                    <?= htmlspecialchars($casa['provincia'] ?? 'N/A') ?>
                  </small>
                </td>
                <td><?= $casa['capacidad'] ?> pers.</td>
                <td><?= number_format($casa['precio_noche'], 2) ?>€</td>
                <td>
                  <small>
                    <?= isset($casa['tiene_wifi']) && $casa['tiene_wifi'] ? '📶 ' : '' ?>
                    <?= isset($casa['tiene_piscina']) && $casa['tiene_piscina'] ? '🏊 ' : '' ?>
                    <?= isset($casa['tiene_parking']) && $casa['tiene_parking'] ? '🅿️ ' : '' ?>
                  </small>
                </td>
                <td>
                  <?php if (isset($casa['es_vip']) && $casa['es_vip']) : ?>
                    <span class="badge bg-warning text-dark">VIP</span>
                  <?php else : ?>
                    <span class="badge bg-secondary">Normal</span>
                  <?php endif; ?>
                </td>
                <td>
                  <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditarCasa<?= $casa['id_casa'] ?>">
                    Editar
                  </button>
                  <button class="btn btn-sm btn-outline-danger" onclick="confirmarEliminar(<?= $casa['id_casa'] ?>, '<?= htmlspecialchars($casa['nombre']) ?>')">
                    Eliminar
                  </button>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal: Nueva Casa -->
  <div class="modal fade" id="modalNuevaCasa" tabindex="-1" aria-labelledby="modalNuevaCasaLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="modalNuevaCasaLabel">Crear Nueva Casa Vacacional</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="./admin/includes/crudCasas.php" enctype="multipart/form-data">
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="nombre" class="form-label">Nombre de la Casa</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="id_propietario" class="form-label">Propietario</label>
                <select class="form-select" id="id_propietario" name="id_propietario" required>
                  <option value="">Seleccionar...</option>
                  <?php foreach ($propietarios as $prop) : ?>
                    <option value="<?= $prop['id_usuario'] ?>"><?= htmlspecialchars($prop['nombre'] . ' ' . $prop['apellidos']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-4 mb-3">
                <label for="id_comunidad" class="form-label">Comunidad Autónoma</label>
                <select class="form-select" id="id_comunidad" name="id_comunidad" required>
                  <option value="">Seleccionar...</option>
                  <?php foreach ($comunidades as $com) : ?>
                    <option value="<?= $com['id_comunidad'] ?>"><?= htmlspecialchars($com['nombre']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-4 mb-3">
                <label for="id_provincia" class="form-label">Provincia</label>
                <select class="form-select" id="id_provincia" name="id_provincia" required>
                  <option value="">Seleccionar...</option>
                  <?php foreach ($provincias as $prov) : ?>
                    <option value="<?= $prov['id_provincia'] ?>"><?= htmlspecialchars($prov['nombre']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-4 mb-3">
                <label for="id_ciudad" class="form-label">Ciudad</label>
                <select class="form-select" id="id_ciudad" name="id_ciudad" required>
                  <option value="">Seleccionar...</option>
                  <?php foreach ($ciudades as $city) : ?>
                    <option value="<?= $city['id_ciudad'] ?>"><?= htmlspecialchars($city['nombre']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-3 mb-3">
                <label for="capacidad" class="form-label">Capacidad</label>
                <input type="number" class="form-control" id="capacidad" name="capacidad" min="1" required>
              </div>
              <div class="col-md-3 mb-3">
                <label for="precio_noche" class="form-label">Precio/Noche (€)</label>
                <input type="number" step="0.01" class="form-control" id="precio_noche" name="precio_noche" required>
              </div>
              <div class="col-md-3 mb-3">
                <label for="num_banos" class="form-label">Nº Baños</label>
                <input type="number" class="form-control" id="num_banos" name="num_banos" value="1" min="1">
              </div>
              <div class="col-md-3 mb-3">
                <label for="num_cocinas" class="form-label">Nº Cocinas</label>
                <input type="number" class="form-control" id="num_cocinas" name="num_cocinas" value="1" min="1">
              </div>
              <div class="col-md-12 mb-3">
                <label for="imagen_principal" class="form-label">Imagen Principal</label>
                <input type="file" class="form-control" id="imagen_principal" name="imagen_principal" accept="image/*">
              </div>
              <div class="col-12">
                <h6>Servicios y Amenidades</h6>
                <div class="row">
                  <div class="col-md-3">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" id="tiene_wifi" name="tiene_wifi" value="1">
                      <label class="form-check-label" for="tiene_wifi">WiFi</label>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" id="tiene_piscina" name="tiene_piscina" value="1">
                      <label class="form-check-label" for="tiene_piscina">Piscina</label>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" id="tiene_parking" name="tiene_parking" value="1">
                      <label class="form-check-label" for="tiene_parking">Parking</label>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" id="tiene_jardin" name="tiene_jardin" value="1">
                      <label class="form-check-label" for="tiene_jardin">Jardín</label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" name="crear" class="btn btn-primary">Crear Casa</button>
          </div>
        </form>
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
  <script>
    function confirmarEliminar(id, nombre) {
      if (confirm(`¿Estás seguro de eliminar la casa "${nombre}"?`)) {
        window.location.href = `./admin/includes/crudCasas.php?eliminar=${id}`;
      }
    }
  </script>
</body>
</html>
