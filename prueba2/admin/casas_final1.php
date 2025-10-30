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


$casas = $casaObj->getAll();
$comunidades = $ubicacionObj->getAllComunidades();
$provincias = $ubicacionObj->getAllProvincias();
$ciudades = $ubicacionObj->getAllCiudades();
$propietarios = $usuariosObj->getAll();

$accion = $_GET['accion'] ?? null;
$id = $_GET['id'] ?? null;
$errores = [];
$mensaje_exito = '';

// =========================================
// INICIALIZAR DATOS POR DEFECTO
// =========================================
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

// =========================================
// SI ES EDITAR, CARGAR DATOS EXISTENTES
// =========================================
if ($accion === "editar" && $id) {
    $datos_casa = $casaObj->getCasaById($id);
}

// =========================================
// PROCESAR FORMULARIO POST
// =========================================
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

    // =========================================
    // VALIDACIONES (0 NO ES VACÍO)
    // =========================================
    $errores = [];

    // Campos obligatorios que NO pueden estar vacíos
    if (empty($id_propietario)) {
        $errores['id_propietario'] = "Debe seleccionar un propietario.";
    }
    if (empty($id_comunidad)) {
        $errores['id_comunidad'] = "Debe seleccionar una comunidad.";
    }
    if (empty($id_provincia)) {
        $errores['id_provincia'] = "Debe seleccionar una provincia.";
    }
    if (empty($id_ciudad)) {
        $errores['id_ciudad'] = "Debe seleccionar una ciudad.";
    }
    if (empty($nombre)) {
        $errores['nombre'] = "El nombre de la casa no puede estar vacío.";
    }

    // Validaciones de valores razonables (sin considerar 0 como vacío)
    if ($capacidad < 1) {
        $errores['capacidad'] = "La capacidad debe ser al menos 1.";
    }
    if ($precio_noche < 0) {
        $errores['precio_noche'] = "El precio no puede ser negativo.";
    }
    if ($num_banos < 1) {
        $errores['num_banos'] = "Debe tener al menos 1 baño.";
    }
    if ($num_cocinas < 1) {
        $errores['num_cocinas'] = "Debe tener al menos 1 cocina.";
    }
    if ($num_nevera < 1) {
        $errores['num_nevera'] = "Debe tener al menos 1 nevera.";
    }

    // =========================================
    // SI NO HAY ERRORES, GUARDAR
    // =========================================
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
                $mensaje_exito = "Casa creada exitosamente.";
                header("Refresh: 2; url=casas.php");
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
                $mensaje_exito = "Casa actualizada exitosamente.";
                header("Refresh: 2; url=casas.php");
            }
        } catch (Exception $e) {
            $errores['general'] = "Error: " . $e->getMessage();
        }
    }
}

// =========================================
// MOSTRAR FORMULARIO SEGÚN ACCIÓN
// =========================================
$mostrar_formulario = ($accion === 'crear' || $accion === 'editar');
$mostrar_alerta_eliminar = ($accion === 'eliminar');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Casas Vacacionales</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles.css">
    <style>
        :root {
            --primary: #0b5482;
            --secondary: #4fd1c5;
            --accent: #ffd166;
            --dark: #072d4b;
            --light: #e9f7f6;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background: linear-gradient(90deg, var(--dark) 0%, var(--primary) 100%);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.4rem;
            color: var(--accent) !important;
            letter-spacing: 0.5px;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }

        .card-header {
            background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%) !important;
            border-radius: 12px 12px 0 0 !important;
            color: white !important;
        }

        .card-header h5 {
            margin: 0;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .btn-primary {
            background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
            border: none;
            font-weight: 600;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-primary:hover {
            background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(11, 84, 130, 0.3);
        }

        .btn-secondary {
            background: #6c757d;
            border: none;
            font-weight: 600;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        .btn-warning {
            background: #ffd166;
            border: none;
            color: var(--dark);
            font-weight: 600;
        }

        .btn-warning:hover {
            background: #ffb84d;
            color: var(--dark);
        }

        .btn-danger {
            background: #f14b4b;
            border: none;
            font-weight: 600;
        }

        .btn-danger:hover {
            background: #d63838;
        }

        .btn-info {
            background: var(--secondary);
            border: none;
            color: white;
            font-weight: 600;
        }

        .btn-info:hover {
            background: #3fbdb1;
            transform: translateY(-2px);
        }

        .form-label {
            font-weight: 600;
            color: var(--dark);
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
            transition: all 0.3s;
        }

        .form-check-input:checked {
            background-color: var(--secondary);
            border-color: var(--secondary);
        }

        .form-check-input:focus {
            border-color: var(--secondary);
            box-shadow: 0 0 0 0.25rem rgba(79, 209, 197, 0.25);
        }

        .form-check-label {
            padding-left: 0.5rem;
            color: var(--dark);
            font-weight: 500;
        }

        .alert {
            border: none;
            border-radius: 8px;
            border-left: 4px solid;
        }

        .alert-danger {
            background-color: #fff5f5;
            border-left-color: #f14b4b;
            color: #721c24;
        }

        .alert-success {
            background-color: #f0fdf4;
            border-left-color: #22c55e;
            color: #155724;
        }

        .alert-info {
            background-color: #f0f9ff;
            border-left-color: var(--secondary);
            color: #0c5460;
        }

        .table {
            background: white;
            border-radius: 8px;
            overflow: hidden;
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
        }

        .table tbody tr:hover {
            background-color: #f8f9ff;
        }

        .table tbody td {
            padding: 0.9rem 0.75rem;
            vertical-align: middle;
            border-color: #e9ecef;
        }

        .table img {
            border-radius: 6px;
            border: 2px solid #e9ecef;
            transition: transform 0.2s;
        }

        .table img:hover {
            transform: scale(1.1);
        }

        .badge {
            font-size: 0.75rem;
            padding: 0.35rem 0.65rem;
            border-radius: 4px;
        }

        .badge-secondary {
            background: #6c757d;
        }

        .btn-group-sm .btn {
            padding: 0.35rem 0.65rem;
            font-size: 0.85rem;
        }

        h6 {
            font-weight: 700;
            color: var(--primary);
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
        }

        .container {
            max-width: 1200px;
        }

        .mt-4 {
            margin-top: 2rem;
        }

        small {
            color: #6c757d;
            font-weight: 500;
        }

        .is-invalid {
            border-color: #f14b4b !important;
        }

        .invalid-feedback {
            color: #f14b4b;
            font-weight: 500;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="casas.php">🏠 CASAS VACACIONALES</a>
            <span class="navbar-text text-white-50 ms-auto">Panel de Administración</span>
        </div>
    </nav>

    <div class="container mt-4 mb-5">

        <!-- MOSTRAR MENSAJES DE ÉXITO -->
        <?php if (!empty($mensaje_exito)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>✅ Éxito:</strong> <?= htmlspecialchars($mensaje_exito) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- ALERTA DE CONFIRMACIÓN ELIMINAR -->
        <?php if ($mostrar_alerta_eliminar && $id): 
            $casaAEliminar = $casaObj->getCasaById($id);
        ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>⚠️ Confirmar Eliminación</strong><br>
                <p class="mb-2">¿Está seguro de que desea eliminar la casa:</p>
                <p class="mb-3" style="font-size: 1.1rem; font-weight: 700; color: var(--dark);">
                    "<?= htmlspecialchars($casaAEliminar['nombre'] ?? 'N/A') ?>"
                </p>
                <div>
                    <form method="POST" class="d-inline">
                        <input type="hidden" name="confirmar_eliminar" value="1">
                        <button type="submit" class="btn btn-danger btn-sm">🗑️ Sí, Eliminar</button>
                    </form>
                    <a href="casas.php" class="btn btn-secondary btn-sm">❌ Cancelar</a>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php 
            // Procesar confirmación de eliminación
            if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['confirmar_eliminar'])):
                try {
                    $casaObj->eliminarCasa($id);
                    $mensaje_exito = "Casa eliminada exitosamente.";
                    header("Location: casas.php");
                    exit();
                } catch (Exception $e) {
                    $errores['general'] = "Error al eliminar: " . $e->getMessage();
                }
            endif;
        endif; ?>

        <!-- FORMULARIO CREAR/EDITAR -->
        <?php if ($mostrar_formulario): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5>
                        <?= ($accion === 'crear') ? '➕ Crear Nueva Casa' : '✏️ Editar Casa' ?>
                    </h5>
                </div>
                <div class="card-body">

                    <!-- Mostrar errores -->
                    <?php if (!empty($errores)): ?>
                        <div class="alert alert-danger">
                            <strong>❌ Errores en el Formulario:</strong>
                            <ul class="mb-0 mt-2">
                                <?php foreach ($errores as $campo => $mensaje): ?>
                                    <li><?= htmlspecialchars($mensaje) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <!-- Columna Izquierda -->
                            <div class="col-md-6">
                                <h6 class="mb-3">📋 Información Básica</h6>

                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre *</label>
                                    <input type="text" class="form-control <?= isset($errores['nombre']) ? 'is-invalid' : '' ?>" 
                                           id="nombre" name="nombre" value="<?= htmlspecialchars($datos_casa['nombre'] ?? '') ?>" required>
                                    <?php if (isset($errores['nombre'])): ?>
                                        <div class="invalid-feedback"><?= $errores['nombre'] ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="mb-3">
                                    <label for="id_propietario" class="form-label">Propietario *</label>
                                    <select class="form-select <?= isset($errores['id_propietario']) ? 'is-invalid' : '' ?>" 
                                            id="id_propietario" name="id_propietario" required>
                                        <option value="">-- Seleccionar Propietario --</option>
                                        <?php foreach ($propietarios as $prop): ?>
                                            <option value="<?= $prop['id_usuario'] ?>" 
                                                    <?= ($datos_casa['id_propietario'] == $prop['id_usuario']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($prop['username']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($errores['id_propietario'])): ?>
                                        <div class="invalid-feedback"><?= $errores['id_propietario'] ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="mb-3">
                                    <label for="capacidad" class="form-label">Capacidad (personas) *</label>
                                    <input type="number" class="form-control <?= isset($errores['capacidad']) ? 'is-invalid' : '' ?>" 
                                           id="capacidad" name="capacidad" min="1" 
                                           value="<?= $datos_casa['capacidad'] ?? 1 ?>" required>
                                    <?php if (isset($errores['capacidad'])): ?>
                                        <div class="invalid-feedback"><?= $errores['capacidad'] ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="mb-3">
                                    <label for="precio_noche" class="form-label">Precio por Noche (€) *</label>
                                    <input type="number" class="form-control <?= isset($errores['precio_noche']) ? 'is-invalid' : '' ?>" 
                                           id="precio_noche" name="precio_noche" min="0" step="0.01" 
                                           value="<?= $datos_casa['precio_noche'] ?? 0 ?>" required>
                                    <?php if (isset($errores['precio_noche'])): ?>
                                        <div class="invalid-feedback"><?= $errores['precio_noche'] ?></div>
                                    <?php endif; ?>
                                </div>

                                <h6 class="mb-3 mt-4">📍 Ubicación</h6>

                                <div class="mb-3">
                                    <label for="id_comunidad" class="form-label">Comunidad *</label>
                                    <select class="form-select <?= isset($errores['id_comunidad']) ? 'is-invalid' : '' ?>" 
                                            id="id_comunidad" name="id_comunidad" required>
                                        <option value="">-- Seleccionar Comunidad --</option>
                                        <?php foreach ($comunidades as $com): ?>
                                            <option value="<?= $com['id_comunidad'] ?>" 
                                                    <?= ($datos_casa['id_comunidad'] == $com['id_comunidad']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($com['nombre']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($errores['id_comunidad'])): ?>
                                        <div class="invalid-feedback"><?= $errores['id_comunidad'] ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="mb-3">
                                    <label for="id_provincia" class="form-label">Provincia *</label>
                                    <select class="form-select <?= isset($errores['id_provincia']) ? 'is-invalid' : '' ?>" 
                                            id="id_provincia" name="id_provincia" required>
                                        <option value="">-- Seleccionar Provincia --</option>
                                        <?php foreach ($provincias as $prov): ?>
                                            <option value="<?= $prov['id_provincia'] ?>" 
                                                    <?= ($datos_casa['id_provincia'] == $prov['id_provincia']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($prov['nombre']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($errores['id_provincia'])): ?>
                                        <div class="invalid-feedback"><?= $errores['id_provincia'] ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="mb-3">
                                    <label for="id_ciudad" class="form-label">Ciudad *</label>
                                    <select class="form-select <?= isset($errores['id_ciudad']) ? 'is-invalid' : '' ?>" 
                                            id="id_ciudad" name="id_ciudad" required>
                                        <option value="">-- Seleccionar Ciudad --</option>
                                        <?php foreach ($ciudades as $ciu): ?>
                                            <option value="<?= $ciu['id_ciudad'] ?>" 
                                                    <?= ($datos_casa['id_ciudad'] == $ciu['id_ciudad']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($ciu['nombre']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($errores['id_ciudad'])): ?>
                                        <div class="invalid-feedback"><?= $errores['id_ciudad'] ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Columna Derecha -->
                            <div class="col-md-6">
                                <h6 class="mb-3">🏠 Habitaciones y Baños</h6>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="num_banos" class="form-label">Baños *</label>
                                        <input type="number" class="form-control <?= isset($errores['num_banos']) ? 'is-invalid' : '' ?>" 
                                               id="num_banos" name="num_banos" min="1" 
                                               value="<?= $datos_casa['num_banos'] ?? 1 ?>" required>
                                        <?php if (isset($errores['num_banos'])): ?>
                                            <div class="invalid-feedback"><?= $errores['num_banos'] ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="num_cocinas" class="form-label">Cocinas *</label>
                                        <input type="number" class="form-control <?= isset($errores['num_cocinas']) ? 'is-invalid' : '' ?>" 
                                               id="num_cocinas" name="num_cocinas" min="1" 
                                               value="<?= $datos_casa['num_cocinas'] ?? 1 ?>" required>
                                        <?php if (isset($errores['num_cocinas'])): ?>
                                            <div class="invalid-feedback"><?= $errores['num_cocinas'] ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="num_hab_individuales" class="form-label">Hab. Individuales</label>
                                        <input type="number" class="form-control" id="num_hab_individuales" 
                                               name="num_hab_individuales" min="0" 
                                               value="<?= $datos_casa['num_hab_individuales'] ?? 0 ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="num_hab_familiares" class="form-label">Hab. Familiares</label>
                                        <input type="number" class="form-control" id="num_hab_familiares" 
                                               name="num_hab_familiares" min="0" 
                                               value="<?= $datos_casa['num_hab_familiares'] ?? 0 ?>">
                                    </div>
                                </div>

                                <h6 class="mb-3 mt-4">🍳 Electrodomésticos</h6>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="num_nevera" class="form-label">Neveras *</label>
                                        <input type="number" class="form-control <?= isset($errores['num_nevera']) ? 'is-invalid' : '' ?>" 
                                               id="num_nevera" name="num_nevera" min="1" 
                                               value="<?= $datos_casa['num_nevera'] ?? 1 ?>" required>
                                        <?php if (isset($errores['num_nevera'])): ?>
                                            <div class="invalid-feedback"><?= $errores['num_nevera'] ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="num_congelador" class="form-label">Congeladores</label>
                                        <input type="number" class="form-control" id="num_congelador" 
                                               name="num_congelador" min="0" value="<?= $datos_casa['num_congelador'] ?? 0 ?>">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="num_lavadora" class="form-label">Lavadoras</label>
                                        <input type="number" class="form-control" id="num_lavadora" 
                                               name="num_lavadora" min="0" value="<?= $datos_casa['num_lavadora'] ?? 0 ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="num_secadora" class="form-label">Secadoras</label>
                                        <input type="number" class="form-control" id="num_secadora" 
                                               name="num_secadora" min="0" value="<?= $datos_casa['num_secadora'] ?? 0 ?>">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="num_lavavajillas" class="form-label">Lavavajillas</label>
                                        <input type="number" class="form-control" id="num_lavavajillas" 
                                               name="num_lavavajillas" min="0" value="<?= $datos_casa['num_lavavajillas'] ?? 0 ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="num_horno" class="form-label">Hornos</label>
                                        <input type="number" class="form-control" id="num_horno" 
                                               name="num_horno" min="0" value="<?= $datos_casa['num_horno'] ?? 0 ?>">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="num_microondas" class="form-label">Microondas</label>
                                    <input type="number" class="form-control" id="num_microondas" 
                                           name="num_microondas" min="0" value="<?= $datos_casa['num_microondas'] ?? 0 ?>">
                                </div>

                                <h6 class="mb-3 mt-4">⚙️ Servicios e Instalaciones</h6>

                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="tiene_wifi" name="tiene_wifi" 
                                           <?= ($datos_casa['tiene_wifi'] ?? 0) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="tiene_wifi">WiFi</label>
                                </div>

                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="tiene_calefaccion" name="tiene_calefaccion" 
                                           <?= ($datos_casa['tiene_calefaccion'] ?? 0) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="tiene_calefaccion">Calefacción</label>
                                </div>

                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="tiene_aire_acondicionado" name="tiene_aire_acondicionado" 
                                           <?= ($datos_casa['tiene_aire_acondicionado'] ?? 0) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="tiene_aire_acondicionado">Aire Acondicionado</label>
                                </div>

                                <div class="mb-3">
                                    <label for="num_ascensores" class="form-label">Ascensores</label>
                                    <input type="number" class="form-control" id="num_ascensores" 
                                           name="num_ascensores" min="0" value="<?= $datos_casa['num_ascensores'] ?? 0 ?>">
                                </div>

                                <h6 class="mb-3 mt-4">🏞️ Espacios Exteriores</h6>

                                <div class="mb-3">
                                    <label for="num_aparcamientos" class="form-label">Aparcamientos</label>
                                    <input type="number" class="form-control" id="num_aparcamientos" 
                                           name="num_aparcamientos" min="0" value="<?= $datos_casa['num_aparcamientos'] ?? 0 ?>">
                                </div>

                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="tiene_piscina" name="tiene_piscina" 
                                           <?= ($datos_casa['tiene_piscina'] ?? 0) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="tiene_piscina">Piscina</label>
                                </div>

                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="tiene_jardin" name="tiene_jardin" 
                                           <?= ($datos_casa['tiene_jardin'] ?? 0) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="tiene_jardin">Jardín</label>
                                </div>

                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="tiene_patio" name="tiene_patio" 
                                           <?= ($datos_casa['tiene_patio'] ?? 0) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="tiene_patio">Patio</label>
                                </div>

                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="tiene_barbacoa" name="tiene_barbacoa" 
                                           <?= ($datos_casa['tiene_barbacoa'] ?? 0) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="tiene_barbacoa">Barbacoa</label>
                                </div>

                                <h6 class="mb-3 mt-4">🎭 Amenidades Extra</h6>

                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="tiene_banera" name="tiene_banera" 
                                           <?= ($datos_casa['tiene_banera'] ?? 0) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="tiene_banera">Bañera</label>
                                </div>

                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="tiene_chimenea" name="tiene_chimenea" 
                                           <?= ($datos_casa['tiene_chimenea'] ?? 0) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="tiene_chimenea">Chimenea</label>
                                </div>

                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="tiene_sala_cine" name="tiene_sala_cine" 
                                           <?= ($datos_casa['tiene_sala_cine'] ?? 0) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="tiene_sala_cine">Sala de Cine</label>
                                </div>

                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="tiene_secador_pelo" name="tiene_secador_pelo" 
                                           <?= ($datos_casa['tiene_secador_pelo'] ?? 0) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="tiene_secador_pelo">Secador de Pelo</label>
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="tiene_adaptacion_discapacitados" name="tiene_adaptacion_discapacitados" 
                                           <?= ($datos_casa['tiene_adaptacion_discapacitados'] ?? 0) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="tiene_adaptacion_discapacitados">Adaptación para Discapacitados</label>
                                </div>

                                <div class="mb-3">
                                    <label for="imagen_principal" class="form-label">Imagen Principal</label>
                                    <input type="text" class="form-control" id="imagen_principal" name="imagen_principal" 
                                           placeholder="imagenes/casa1.jpg" value="<?= htmlspecialchars($datos_casa['imagen_principal'] ?? '') ?>">
                                    <small>Guardar imágenes en la carpeta /admin/imagenes</small>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <?= ($accion === 'crear') ? '💾 Crear Casa' : '💾 Actualizar Casa' ?>
                            </button>
                            <a href="casas.php" class="btn btn-secondary">❌ Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <!-- TABLA DE CASAS -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">📋 Listado de Casas</h5>
                <a href="casas.php?accion=crear" class="btn btn-light btn-sm">➕ Añadir Nueva Casa</a>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($casas)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Imagen</th>
                                    <th>Nombre</th>
                                    <th>Ubicación</th>
                                    <th>Propietario</th>
                                    <th>Capacidad</th>
                                    <th>Precio/Noche</th>
                                    <th style="width: 200px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($casas as $casa): ?>
                                    <tr>
                                        <td>
                                            <?php if (!empty($casa['imagen_principal'])): ?>
                                                <img src="<?= htmlspecialchars($casa['imagen_principal']) ?>" 
                                                     alt="Casa" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                            <?php else: ?>
                                                <span class="badge badge-secondary">Sin imagen</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><strong><?= htmlspecialchars($casa['nombre']) ?></strong></td>
                                        <td>
                                            <?= htmlspecialchars($casa['ciudad'] ?? 'N/A') ?>, 
                                            <?= htmlspecialchars($casa['provincia'] ?? 'N/A') ?>
                                        </td>
                                        <td><?= htmlspecialchars($casa['propietario'] ?? 'N/A') ?></td>
                                        <td><?= $casa['capacidad'] ?> pers.</td>
                                        <td><strong><?= number_format($casa['precio_noche'], 2) ?>€</strong></td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button type="button" class="btn btn-info" 
                                                        onclick="verDetalles(<?= htmlspecialchars(json_encode($casa)) ?>)">
                                                    ℹ️ Info
                                                </button>
                                                <a href="casas.php?accion=editar&id=<?= $casa['id_casa'] ?>" 
                                                   class="btn btn-warning">✏️ Editar</a>
                                                <a href="casas.php?accion=eliminar&id=<?= $casa['id_casa'] ?>" 
                                                   class="btn btn-danger">🗑️ Eliminar</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info mb-0">
                        📭 No hay casas registradas. <a href="casas.php?accion=crear" class="alert-link">Crear la primera casa</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- SCRIPT PARA MOSTRAR DETALLES -->
    <script>
        function verDetalles(casa) {
            let detalles = `
🏠 DETALLES DE LA CASA
════════════════════════════════════════════

📋 INFORMACIÓN BÁSICA:
   • Nombre: ${casa.nombre}
   • Capacidad: ${casa.capacidad} personas
   • Precio/Noche: €${parseFloat(casa.precio_noche).toFixed(2)}

📍 UBICACIÓN:
   • Comunidad: ${casa.comunidad || 'N/A'}
   • Provincia: ${casa.provincia || 'N/A'}
   • Ciudad: ${casa.ciudad || 'N/A'}

👤 PROPIETARIO:
   • Usuario: ${casa.propietario || 'N/A'}

🏠 HABITACIONES Y BAÑOS:
   • Baños: ${casa.num_banos}
   • Cocinas: ${casa.num_cocinas}
   • Hab. Individuales: ${casa.num_hab_individuales}
   • Hab. Familiares: ${casa.num_hab_familiares}

🍳 ELECTRODOMÉSTICOS:
   • Lavadoras: ${casa.num_lavadora}
   • Secadoras: ${casa.num_secadora}
   • Lavavajillas: ${casa.num_lavavajillas}
   • Hornos: ${casa.num_horno}
   • Microondas: ${casa.num_microondas}
   • Neveras: ${casa.num_nevera}
   • Congeladores: ${casa.num_congelador}

⚙️ SERVICIOS:
   • WiFi: ${casa.tiene_wifi ? '✅ Sí' : '❌ No'}
   • Ascensores: ${casa.num_ascensores}
   • Calefacción: ${casa.tiene_calefaccion ? '✅ Sí' : '❌ No'}
   • Aire Acondicionado: ${casa.tiene_aire_acondicionado ? '✅ Sí' : '❌ No'}
   • Secador de Pelo: ${casa.tiene_secador_pelo ? '✅ Sí' : '❌ No'}

🏞️ ESPACIOS EXTERIORES:
   • Aparcamientos: ${casa.num_aparcamientos}
   • Piscina: ${casa.tiene_piscina ? '✅ Sí' : '❌ No'}
   • Jardín: ${casa.tiene_jardin ? '✅ Sí' : '❌ No'}
   • Patio: ${casa.tiene_patio ? '✅ Sí' : '❌ No'}
   • Barbacoa: ${casa.tiene_barbacoa ? '✅ Sí' : '❌ No'}

🎭 AMENIDADES EXTRA:
   • Bañera: ${casa.tiene_banera ? '✅ Sí' : '❌ No'}
   • Chimenea: ${casa.tiene_chimenea ? '✅ Sí' : '❌ No'}
   • Sala de Cine: ${casa.tiene_sala_cine ? '✅ Sí' : '❌ No'}
   • Adaptación Discapacitados: ${casa.tiene_adaptacion_discapacitados ? '✅ Sí' : '❌ No'}`;
            alert(detalles);
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
