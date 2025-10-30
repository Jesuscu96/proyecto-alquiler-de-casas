<?php
require_once "./includes/crudCasas.php";
require_once "./includes/crudUbicacion.php";
require_once "./includes/crudUsuarios.php";
require_once "./includes/sessions.php";
$sesion = new Sessions();
if (!$sesion->comprobarSesion()) {
    header("Location: ../login.php");
    exit();
}

$casaObj = new Casas();
$ubicacionObj = new Ubicacion();
$usuariosObj = new Usuarios();

// Obtener datos
$casas = $casaObj->getAll();
$comunidades = $ubicacionObj->getAllComunidades();
$provincias = $ubicacionObj->getAllProvincias();
$ciudades = $ubicacionObj->getAllCiudades();
$propietarios = $usuariosObj->getAll();

// Calcular estadísticas
$total_casas = count($casas);
$casas_vip = array_filter($casas, function($casa) {
    return $casa['precio_noche'] >= 1000 && $casa['tiene_adaptacion_discapacitados'];
});
$total_casas_vip = count($casas_vip);
$precio_promedio = !empty($casas) ? array_sum(array_column($casas, 'precio_noche')) / $total_casas : 0;

$accion = $_GET['accion'] ?? null;
$id = $_GET['id'] ?? null;
$errores = [];
$mensaje_exito = '';

// Datos por defecto
$datos_casa = [
    'id_propietario' => '',
    'id_comunidad' => '',
    'id_provincia' => '',
    'id_ciudad' => '',
    'nombre' => '',
    'capacidad' => 1,
    'precio_noche' => 0,
    'num_banos' => 1,
    'num_cocinas' => 1,
    'num_hab_individuales' => 0,
    'num_hab_familiares' => 0,
    'num_aparcamientos' => 0,
    'num_lavadora' => 0,
    'num_secadora' => 0,
    'num_lavavajillas' => 0,
    'num_horno' => 0,
    'num_microondas' => 0,
    'num_nevera' => 0,
    'num_congelador' => 0,
    'tiene_wifi' => 0,
    'num_ascensores' => 0,
    'tiene_calefaccion' => 0,
    'tiene_aire_acondicionado' => 0,
    'tiene_piscina' => 0,
    'tiene_banera' => 0,
    'tiene_barbacoa' => 0,
    'tiene_chimenea' => 0,
    'tiene_adaptacion_discapacitados' => 0,
    'tiene_jardin' => 0,
    'tiene_patio' => 0,
    'tiene_sala_cine' => 0,
    'tiene_secador_pelo' => 0,
    'imagen_principal' => null
];

// Si es editar, cargar datos
if ($accion === "editar" && $id) {
    $datos_casa = $casaObj->getCasaById($id);
}

// Procesar formulario POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_propietario = $_POST['id_propietario'] ?? '';
    $id_comunidad = $_POST['id_comunidad'] ?? '';
    $id_provincia = $_POST['id_provincia'] ?? '';
    $id_ciudad = $_POST['id_ciudad'] ?? '';
    $nombre = trim($_POST['nombre'] ?? '');
    $capacidad = (int)($_POST['capacidad'] ?? 1);
    $precio_noche = (float)($_POST['precio_noche'] ?? 0);
    $num_banos = (int)($_POST['num_banos'] ?? 1);
    $num_cocinas = (int)($_POST['num_cocinas'] ?? 1);
    $num_hab_individuales = (int)($_POST['num_hab_individuales'] ?? 0);
    $num_hab_familiares = (int)($_POST['num_hab_familiares'] ?? 0);
    $num_aparcamientos = (int)($_POST['num_aparcamientos'] ?? 0);
    $num_lavadora = (int)($_POST['num_lavadora'] ?? 0);
    $num_secadora = (int)($_POST['num_secadora'] ?? 0);
    $num_lavavajillas = (int)($_POST['num_lavavajillas'] ?? 0);
    $num_horno = (int)($_POST['num_horno'] ?? 0);
    $num_microondas = (int)($_POST['num_microondas'] ?? 0);
    $num_nevera = (int)($_POST['num_nevera'] ?? 0);
    $num_congelador = (int)($_POST['num_congelador'] ?? 0);
    $tiene_wifi = isset($_POST['tiene_wifi']) ? 1 : 0;
    $num_ascensores = (int)($_POST['num_ascensores'] ?? 0);
    $tiene_calefaccion = isset($_POST['tiene_calefaccion']) ? 1 : 0;
    $tiene_aire_acondicionado = isset($_POST['tiene_aire_acondicionado']) ? 1 : 0;
    $tiene_piscina = isset($_POST['tiene_piscina']) ? 1 : 0;
    $tiene_banera = isset($_POST['tiene_banera']) ? 1 : 0;
    $tiene_barbacoa = isset($_POST['tiene_barbacoa']) ? 1 : 0;
    $tiene_chimenea = isset($_POST['tiene_chimenea']) ? 1 : 0;
    $tiene_adaptacion_discapacitados = isset($_POST['tiene_adaptacion_discapacitados']) ? 1 : 0;
    $tiene_jardin = isset($_POST['tiene_jardin']) ? 1 : 0;
    $tiene_patio = isset($_POST['tiene_patio']) ? 1 : 0;
    $tiene_sala_cine = isset($_POST['tiene_sala_cine']) ? 1 : 0;
    $tiene_secador_pelo = isset($_POST['tiene_secador_pelo']) ? 1 : 0;
    $imagen_principal = $_POST['imagen_principal'] ?? '';

    // Validaciones
    $errores = [];

    if (empty($id_propietario)) $errores['id_propietario'] = "Selecciona un propietario.";
    if (empty($id_comunidad)) $errores['id_comunidad'] = "Selecciona una comunidad.";
    if (empty($id_provincia)) $errores['id_provincia'] = "Selecciona una provincia.";
    if (empty($id_ciudad)) $errores['id_ciudad'] = "Selecciona una ciudad.";
    if (empty($nombre)) $errores['nombre'] = "El nombre no puede estar vacío.";
    if ($capacidad < 1) $errores['capacidad'] = "La capacidad debe ser al menos 1.";
    if ($precio_noche < 0) $errores['precio_noche'] = "El precio no puede ser negativo.";
    if ($num_banos < 1) $errores['num_banos'] = "Debe tener al menos 1 baño.";
    if ($num_cocinas < 1) $errores['num_cocinas'] = "Debe tener al menos 1 cocina.";
    if ($num_nevera < 1) $errores['num_nevera'] = "Debe tener al menos 1 nevera.";

    // Guardar
    if (empty($errores)) {
        try {
            if ($accion === 'crear') {
                $casaObj->insertarCasa(
                    $id_propietario, $id_comunidad, $id_provincia, $id_ciudad,
                    $nombre, $capacidad, $precio_noche, $num_banos, $num_cocinas,
                    $num_hab_individuales, $num_hab_familiares, $num_aparcamientos,
                    $num_lavadora, $num_secadora, $num_lavavajillas, $num_horno,
                    $num_microondas, $num_nevera, $num_congelador, $tiene_wifi,
                    $num_ascensores, $tiene_calefaccion, $tiene_aire_acondicionado,
                    $tiene_piscina, $tiene_banera, $tiene_barbacoa, $tiene_chimenea,
                    $tiene_adaptacion_discapacitados, $tiene_jardin, $tiene_patio,
                    $tiene_sala_cine, $tiene_secador_pelo, $imagen_principal
                );
                $mensaje_exito = "✅ Casa creada exitosamente.";
                header("Refresh: 2; url=casasamedia.php");
            } elseif ($accion === 'editar' && $id) {
                $casaObj->actualizarCasa(
                    $id, $id_propietario, $id_comunidad, $id_provincia, $id_ciudad,
                    $nombre, $capacidad, $precio_noche, $num_banos, $num_cocinas,
                    $num_hab_individuales, $num_hab_familiares, $num_aparcamientos,
                    $num_lavadora, $num_secadora, $num_lavavajillas, $num_horno,
                    $num_microondas, $num_nevera, $num_congelador, $tiene_wifi,
                    $num_ascensores, $tiene_calefaccion, $tiene_aire_acondicionado,
                    $tiene_piscina, $tiene_banera, $tiene_barbacoa, $tiene_chimenea,
                    $tiene_adaptacion_discapacitados, $tiene_jardin, $tiene_patio,
                    $tiene_sala_cine, $tiene_secador_pelo, $imagen_principal
                );
                $mensaje_exito = "✅ Casa actualizada exitosamente.";
                header("Refresh: 2; url=casasamedia.php");
            }
        } catch (Exception $e) {
            $errores['general'] = "Error: " . $e->getMessage();
        }
    }
}

// Procesar eliminación
if ($accion === 'eliminar_confirmar' && $id) {
    try {
        $casaObj->eliminarCasa($id);
        $mensaje_exito = "✅ Casa eliminada exitosamente.";
        header("Location: casasamedia.php");
        exit();
    } catch (Exception $e) {
        $errores['general'] = "Error al eliminar: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Casas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #0b5482;
            --secondary: #4fd1c5;
            --accent: #ffd166;
            --dark: #072d4b;
            --light: #e9f7f6;
            --danger: #f14b4b;
            --warning: #ffd166;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background: linear-gradient(90deg, var(--dark) 0%, var(--primary) 100%);
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--accent) !important;
            letter-spacing: 0.5px;
        }

        /* STATS CARDS */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            border-left: 5px solid;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            border-radius: 50%;
            transform: translate(30px, -30px);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }

        .stat-card.total {
            border-left-color: var(--primary);
        }

        .stat-card.vip {
            border-left-color: var(--accent);
        }

        .stat-card.precio {
            border-left-color: var(--secondary);
        }

        .stat-card h3 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 1;
        }

        .stat-card p {
            color: #6c757d;
            font-weight: 500;
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .stat-icon {
            font-size: 2.5rem;
            position: absolute;
            top: 1rem;
            right: 1.5rem;
            opacity: 0.1;
            z-index: 0;
        }

        /* MODAL MEJORADO */
        .modal-header {
            background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border: none;
        }

        .modal-header .btn-close {
            filter: brightness(0) invert(1);
        }

        .modal-title {
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        /* FORMULARIO EN MODAL */
        .form-label {
            font-weight: 600;
            color: var(--dark);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 0.65rem 0.75rem;
            transition: border-color 0.3s;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--secondary);
            box-shadow: 0 0 0 0.2rem rgba(79, 209, 197, 0.15);
        }

        .form-check-input {
            width: 1.25em;
            height: 1.25em;
            border: 2px solid #dee2e6;
            border-radius: 4px;
        }

        .form-check-input:checked {
            background-color: var(--secondary);
            border-color: var(--secondary);
        }

        h6 {
            font-weight: 700;
            color: var(--primary);
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            margin-top: 1rem;
            margin-bottom: 0.75rem;
        }

        /* BOTONES */
        .btn-primary {
            background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
            border: none;
            font-weight: 600;
        }

        .btn-primary:hover {
            background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(11, 84, 130, 0.3);
        }

        .btn-warning {
            background: var(--accent);
            color: var(--dark);
            border: none;
            font-weight: 600;
        }

        .btn-info {
            background: var(--secondary);
            border: none;
            color: white;
            font-weight: 600;
        }

        .btn-danger {
            background: var(--danger);
            border: none;
            font-weight: 600;
        }

        .btn-sm {
            padding: 0.35rem 0.65rem;
            font-size: 0.85rem;
        }

        /* ALERTAS */
        .alert {
            border: none;
            border-radius: 8px;
            border-left: 4px solid;
        }

        .alert-success {
            background-color: #f0fdf4;
            border-left-color: #22c55e;
            color: #155724;
        }

        .alert-danger {
            background-color: #fff5f5;
            border-left-color: var(--danger);
            color: #721c24;
        }

        /* TABLA */
        .table-responsive {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        .table {
            background: white;
            margin-bottom: 0;
        }

        .table thead {
            background: linear-gradient(90deg, var(--dark) 0%, var(--primary) 100%);
            color: white;
        }

        .table thead th {
            border: none;
            font-weight: 600;
            padding: 1rem 0.75rem;
        }

        .table tbody tr {
            transition: background-color 0.2s;
            border-bottom: 1px solid #e9ecef;
        }

        .table tbody tr:hover {
            background-color: #f8f9ff;
        }

        .table img {
            border-radius: 6px;
            border: 2px solid #e9ecef;
        }

        .badge {
            font-size: 0.75rem;
            padding: 0.35rem 0.65rem;
            border-radius: 4px;
        }

        .badge-vip {
            background: linear-gradient(135deg, var(--accent) 0%, #ffb84d 100%);
            color: var(--dark);
            font-weight: 600;
        }

        .badge-accesible {
            background: linear-gradient(135deg, var(--secondary) 0%, #3fbdb1 100%);
            color: white;
            font-weight: 600;
        }

        .section-title {
            margin-top: 2rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 3px solid var(--secondary);
            font-weight: 700;
            color: var(--dark);
        }

        .container-fluid {
            padding-top: 2rem;
            padding-bottom: 2rem;
        }

        .btn-action {
            padding: 0.4rem 0.8rem;
            font-size: 0.8rem;
            margin: 0.2rem;
        }
    </style>
</head>
<body>
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="casasamedia.php">🏠 ADMINISTRACIÓN DE CASAS</a>
            <span class="navbar-text text-white-50">Panel Administrativo</span>
        </div>
    </nav>

    <div class="container-fluid">

        <!-- MENSAJES -->
        <?php if (!empty($mensaje_exito)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($mensaje_exito) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- ESTADÍSTICAS -->
        <h2 class="section-title">📊 Panel de Control</h2>
        <div class="stats-container">
            <div class="stat-card total">
                <i class="fas fa-house stat-icon"></i>
                <h3><?= $total_casas ?></h3>
                <p><i class="fas fa-home"></i> Total de Casas</p>
            </div>
            <div class="stat-card vip">
                <i class="fas fa-crown stat-icon"></i>
                <h3><?= $total_casas_vip ?></h3>
                <p><i class="fas fa-star"></i> Casas VIP Premium</p>
            </div>
            <div class="stat-card precio">
                <i class="fas fa-euro-sign stat-icon"></i>
                <h3>€<?= number_format($precio_promedio, 2) ?></h3>
                <p><i class="fas fa-chart-line"></i> Precio Promedio</p>
            </div>
        </div>

        <!-- BOTÓN CREAR CASA -->
        <h2 class="section-title">🛠️ Gestión de Casas</h2>
        <div class="mb-3">
            <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#modalCasa" onclick="abrirModalCrear()">
                <i class="fas fa-plus"></i> Crear Nueva Casa
            </button>
        </div>

        <!-- TABLA DE CASAS -->
        <?php if (!empty($casas)): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Imagen</th>
                            <th>Nombre</th>
                            <th>Ubicación</th>
                            <th>Precio/Noche</th>
                            <th>Capacidad</th>
                            <th>Especial</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($casas as $casa): 
                            $es_vip = $casa['precio_noche'] >= 1000 && $casa['tiene_adaptacion_discapacitados'];
                        ?>
                            <tr>
                                <td>
                                    <?php if (!empty($casa['imagen_principal'])): ?>
                                        <img src="<?= htmlspecialchars($casa['imagen_principal']) ?>" 
                                             alt="<?= htmlspecialchars($casa['nombre']) ?>" 
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Sin imagen</span>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?= htmlspecialchars($casa['nombre']) ?></strong></td>
                                <td><?= htmlspecialchars($casa['ciudad'] ?? 'N/A') ?></td>
                                <td><strong>€<?= number_format($casa['precio_noche'], 2) ?></strong></td>
                                <td><?= $casa['capacidad'] ?> pers.</td>
                                <td>
                                    <?php if ($es_vip): ?>
                                        <span class="badge badge-vip">👑 VIP Premium</span>
                                    <?php endif; ?>
                                    <?php if ($casa['tiene_adaptacion_discapacitados']): ?>
                                        <span class="badge badge-accesible">♿ Accesible</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-info btn-action" 
                                            onclick="verDetalles(<?= htmlspecialchars(json_encode($casa)) ?>)"
                                            title="Ver información">
                                        <i class="fas fa-info-circle"></i> Info
                                    </button>
                                    <button type="button" class="btn btn-warning btn-action" 
                                            onclick="abrirModalEditar(<?= $casa['id_casa'] ?>)"
                                            title="Editar">
                                        <i class="fas fa-edit"></i> Editar
                                    </button>
                                    <button type="button" class="btn btn-danger btn-action" 
                                            onclick="confirmarEliminar(<?= $casa['id_casa'] ?>, '<?= htmlspecialchars($casa['nombre']) ?>')"
                                            title="Eliminar">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-inbox"></i> No hay casas registradas. 
                <button type="button" class="btn btn-primary btn-sm ms-2" data-bs-toggle="modal" data-bs-target="#modalCasa" onclick="abrirModalCrear()">
                    Crear la primera casa
                </button>
            </div>
        <?php endif; ?>
    </div>

    <!-- MODAL CREAR/EDITAR CASA -->
    <div class="modal fade" id="modalCasa" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <span id="modalTitulo">➕ Crear Nueva Casa</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formCasa" method="POST">
                    <div class="modal-body">
                        <div id="erroresModal"></div>

                        <h6>📋 Información Básica</h6>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="nombre" class="form-label">Nombre *</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>
                            <div class="col-md-6">
                                <label for="id_propietario" class="form-label">Propietario *</label>
                                <select class="form-select" id="id_propietario" name="id_propietario" required>
                                    <option value="">-- Seleccionar --</option>
                                    <?php foreach ($propietarios as $prop): ?>
                                        <option value="<?= $prop['id_usuario'] ?>">
                                            <?= htmlspecialchars($prop['username']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="capacidad" class="form-label">Capacidad *</label>
                                <input type="number" class="form-control" id="capacidad" name="capacidad" min="1" value="1" required>
                            </div>
                            <div class="col-md-6">
                                <label for="precio_noche" class="form-label">Precio/Noche (€) *</label>
                                <input type="number" class="form-control" id="precio_noche" name="precio_noche" min="0" step="0.01" value="0" required>
                            </div>
                        </div>

                        <h6>📍 Ubicación</h6>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="id_comunidad" class="form-label">Comunidad *</label>
                                <select class="form-select" id="id_comunidad" name="id_comunidad" required>
                                    <option value="">-- Seleccionar --</option>
                                    <?php foreach ($comunidades as $com): ?>
                                        <option value="<?= $com['id_comunidad'] ?>">
                                            <?= htmlspecialchars($com['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="id_provincia" class="form-label">Provincia *</label>
                                <select class="form-select" id="id_provincia" name="id_provincia" required>
                                    <option value="">-- Seleccionar --</option>
                                    <?php foreach ($provincias as $prov): ?>
                                        <option value="<?= $prov['id_provincia'] ?>">
                                            <?= htmlspecialchars($prov['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="id_ciudad" class="form-label">Ciudad *</label>
                            <select class="form-select" id="id_ciudad" name="id_ciudad" required>
                                <option value="">-- Seleccionar --</option>
                                <?php foreach ($ciudades as $ciu): ?>
                                    <option value="<?= $ciu['id_ciudad'] ?>">
                                        <?= htmlspecialchars($ciu['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <h6>🏠 Habitaciones</h6>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="num_banos" class="form-label">Baños *</label>
                                <input type="number" class="form-control" id="num_banos" name="num_banos" min="1" value="1" required>
                            </div>
                            <div class="col-md-6">
                                <label for="num_cocinas" class="form-label">Cocinas *</label>
                                <input type="number" class="form-control" id="num_cocinas" name="num_cocinas" min="1" value="1" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="num_hab_individuales" class="form-label">Hab. Individuales</label>
                                <input type="number" class="form-control" id="num_hab_individuales" name="num_hab_individuales" min="0" value="0">
                            </div>
                            <div class="col-md-6">
                                <label for="num_hab_familiares" class="form-label">Hab. Familiares</label>
                                <input type="number" class="form-control" id="num_hab_familiares" name="num_hab_familiares" min="0" value="0">
                            </div>
                        </div>

                        <h6>🍳 Electrodomésticos</h6>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="num_nevera" class="form-label">Neveras *</label>
                                <input type="number" class="form-control" id="num_nevera" name="num_nevera" min="1" value="1" required>
                            </div>
                            <div class="col-md-6">
                                <label for="num_congelador" class="form-label">Congeladores</label>
                                <input type="number" class="form-control" id="num_congelador" name="num_congelador" min="0" value="0">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="num_lavadora" class="form-label">Lavadoras</label>
                                <input type="number" class="form-control" id="num_lavadora" name="num_lavadora" min="0" value="0">
                            </div>
                            <div class="col-md-6">
                                <label for="num_secadora" class="form-label">Secadoras</label>
                                <input type="number" class="form-control" id="num_secadora" name="num_secadora" min="0" value="0">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="num_lavavajillas" class="form-label">Lavavajillas</label>
                                <input type="number" class="form-control" id="num_lavavajillas" name="num_lavavajillas" min="0" value="0">
                            </div>
                            <div class="col-md-6">
                                <label for="num_horno" class="form-label">Hornos</label>
                                <input type="number" class="form-control" id="num_horno" name="num_horno" min="0" value="0">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="num_microondas" class="form-label">Microondas</label>
                            <input type="number" class="form-control" id="num_microondas" name="num_microondas" min="0" value="0">
                        </div>

                        <h6>⚙️ Servicios</h6>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="tiene_wifi" name="tiene_wifi">
                                    <label class="form-check-label" for="tiene_wifi">WiFi</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="tiene_calefaccion" name="tiene_calefaccion">
                                    <label class="form-check-label" for="tiene_calefaccion">Calefacción</label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="tiene_aire_acondicionado" name="tiene_aire_acondicionado">
                                    <label class="form-check-label" for="tiene_aire_acondicionado">Aire Acondicionado</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="num_ascensores" class="form-label">Ascensores</label>
                                <input type="number" class="form-control" id="num_ascensores" name="num_ascensores" min="0" value="0">
                            </div>
                        </div>

                        <h6>🏞️ Espacios Exteriores</h6>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="tiene_piscina" name="tiene_piscina">
                                    <label class="form-check-label" for="tiene_piscina">Piscina</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="tiene_jardin" name="tiene_jardin">
                                    <label class="form-check-label" for="tiene_jardin">Jardín</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="tiene_patio" name="tiene_patio">
                                    <label class="form-check-label" for="tiene_patio">Patio</label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="tiene_barbacoa" name="tiene_barbacoa">
                                    <label class="form-check-label" for="tiene_barbacoa">Barbacoa</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="num_aparcamientos" class="form-label">Aparcamientos</label>
                                <input type="number" class="form-control" id="num_aparcamientos" name="num_aparcamientos" min="0" value="0">
                            </div>
                        </div>

                        <h6>🎭 Amenidades Extra</h6>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="tiene_banera" name="tiene_banera">
                                    <label class="form-check-label" for="tiene_banera">Bañera</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="tiene_chimenea" name="tiene_chimenea">
                                    <label class="form-check-label" for="tiene_chimenea">Chimenea</label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="tiene_sala_cine" name="tiene_sala_cine">
                                    <label class="form-check-label" for="tiene_sala_cine">Sala de Cine</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="tiene_secador_pelo" name="tiene_secador_pelo">
                                    <label class="form-check-label" for="tiene_secador_pelo">Secador de Pelo</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="tiene_adaptacion_discapacitados" name="tiene_adaptacion_discapacitados">
                                <label class="form-check-label" for="tiene_adaptacion_discapacitados">
                                    <strong>♿ Adaptación para Discapacitados</strong>
                                </label>
                            </div>
                        </div>

                        <h6>🖼️ Imagen</h6>
                        <div class="mb-3">
                            <label for="imagen_principal" class="form-label">Ruta Imagen Principal</label>
                            <input type="text" class="form-control" id="imagen_principal" name="imagen_principal" 
                                   placeholder="imagenes/casa1.jpg">
                            <small class="text-muted">Guardar en /admin/imagenes</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="btnSubmit">
                            <i class="fas fa-save"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL ELIMINAR -->
    <div class="modal fade" id="modalEliminar" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Confirmar Eliminación</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro de que desea eliminar la casa?</p>
                    <p class="fw-bold" id="casaNombreEliminar"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="btnConfirmarEliminar">
                        <i class="fas fa-trash"></i> Sí, Eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const modalCasa = new bootstrap.Modal(document.getElementById('modalCasa'));
        const modalEliminar = new bootstrap.Modal(document.getElementById('modalEliminar'));
        let idCasaActual = null;

        function abrirModalCrear() {
            document.getElementById('modalTitulo').textContent = '➕ Crear Nueva Casa';
            document.getElementById('btnSubmit').innerHTML = '<i class="fas fa-plus"></i> Crear Casa';
            document.getElementById('formCasa').reset();
            document.getElementById('formCasa').action = '';
            document.getElementById('formCasa').setAttribute('data-accion', 'crear');
            document.getElementById('erroresModal').innerHTML = '';
            idCasaActual = null;
        }

        function abrirModalEditar(id) {
            document.getElementById('modalTitulo').textContent = '✏️ Editar Casa';
            document.getElementById('btnSubmit').innerHTML = '<i class="fas fa-save"></i> Actualizar';
            document.getElementById('formCasa').setAttribute('data-accion', 'editar');
            idCasaActual = id;
            // Aquí cargarías los datos con AJAX si lo deseas
            modalCasa.show();
        }

        function verDetalles(casa) {
            let detalles = `
🏠 DETALLES COMPLETOS
════════════════════════════════════════
📋 BÁSICA: ${casa.nombre} | €${casa.precio_noche}/noche | ${casa.capacidad} pers.
📍 UBICACIÓN: ${casa.ciudad}, ${casa.provincia}
👤 PROPIETARIO: ${casa.propietario}
🏠 HABITACIONES: ${casa.num_banos} baños | ${casa.num_cocinas} cocinas
🍳 ELECTRODOMÉSTICOS: ${casa.num_lavadora} lavadora(s) | ${casa.num_lavavajillas} lavavajillas
⚙️ SERVICIOS: WiFi: ${casa.tiene_wifi ? '✅' : '❌'} | Calefacción: ${casa.tiene_calefaccion ? '✅' : '❌'}
🏞️ EXTERIORES: Piscina: ${casa.tiene_piscina ? '✅' : '❌'} | Jardín: ${casa.tiene_jardin ? '✅' : '❌'}
🎭 EXTRAS: Chimenea: ${casa.tiene_chimenea ? '✅' : '❌'} | Adaptado: ${casa.tiene_adaptacion_discapacitados ? '✅' : '❌'}`;
            alert(detalles);
        }

        function confirmarEliminar(id, nombre) {
            idCasaActual = id;
            document.getElementById('casaNombreEliminar').textContent = nombre;
            document.getElementById('btnConfirmarEliminar').onclick = function() {
                window.location.href = `casasamedia.php?accion=eliminar_confirmar&id=${id}`;
            };
            modalEliminar.show();
        }

        document.getElementById('formCasa').addEventListener('submit', function(e) {
            e.preventDefault();
            const accion = this.getAttribute('data-accion');
            const formData = new FormData(this);

            // Hacer POST al mismo archivo
            fetch('casasamedia.php?accion=' + accion + (idCasaActual ? '&id=' + idCasaActual : ''), {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                location.reload();
            })
            .catch(error => console.error('Error:', error));
        });
    </script>
</body>
</html>
