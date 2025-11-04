<?php
require_once "./includes/crudReservas.php";
require_once "./includes/sessions.php";
$sesion = new Sessions();
if (!$sesion->comprobarSesion()) {
    header("Location: ../login.php");
    exit();
}

$reservaObj = new Reservas();

// Obtener datos
$reservas = $reservaObj->getAll();

// Parámetros de acción
$accion = $_GET['accion'] ?? null;
$id = $_GET['id'] ?? null;




// // Paginación
// $pagina = (int)($_GET['pagina'] ?? 1);
// $por_pagina = 8;
// $total_casas = count($casas);
// $total_paginas = ceil($total_casas / $por_pagina);
// $inicio = ($pagina - 1) * $por_pagina;
// $casas_pagina = array_slice($casas, $inicio, $por_pagina);

// Calcular estadísticas

$totalReservas = count($reservas);


// Datos por defecto del formulario
$datos_reserva = [
    'id_usuario' => '',
    'id_casa' => '',
    'fecha_inicio' => '',
    'fecha_fin' => '',
    'total_precio' => '',
    'estado' => 1,
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
    'num_nevera' => 1,
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
    'imagen_principal' => ''
];

// Si es editar, cargar datos
if ($accion === "editar" && $id) {
    $datos_casa = $casaObj->getReservaById($id);
}

// Procesar eliminación
if ($accion === 'eliminar' && $id) {
    $casaObj->eliminarReserva($id);
    header("Location: casas2.php");
    exit();
}

// Procesar formulario POST
$errores = [];
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_usuario = $_POST['id_usuario'] ?? '';
    $id_casa = $_POST['id_casa'] ?? '';
    $fecha_inicio = $_POST['fecha_inicio'] ?? '';
    $fecha_fin = $_POST['fecha_fin'] ?? '';
    $total_precio = trim($_POST['total_precio'] ?? '');
    $estado = trim($_POST['estado'] ?? '');
    $precio_noche = (float)($_POST['precio_noche'] ?? 0);
    $num_banos = (int)($_POST['num_banos'] ?? 1);
    $num_cocinas = (int)($_POST['num_cocinas'] ?? 1);
    $num_hab_individuales = (int)($_POST['num_hab_individuales'] ?? 0);
    $num_hab_familiares = (int)($_POST['num_hab_familiares'] ?? 0);
    $num_aparcamientos = (int)($_POST['num_aparcamientos'] ?? 0);
    $num_lavadora = (int)($_POST['num_lavadora'] ?? 0);
   
    

    // Validaciones
    if (empty($id_usuario)) $errores['id_usuario'] = "El total_precio del cliente no puede estar vacío.";
    if (empty($id_casa)) $errores['id_casa'] = "El total_precio del propietario no puede estar vacío.";
    if (empty($fecha_inicio)) $errores['fecha_inicio'] = "La fecha de inicio no puede estar vacia.";
    if (empty($fecha_fin)) $errores['fecha_fin'] = "La fecha del fin no puede estar vacia.";
    if (empty($total_precio)) $errores['total_precio'] = "El total_precio no puede estar vacío.";
    

    // Guardar si no hay errores
    if (empty($errores)) {
        try {
            if ($accion === 'crear') {
                $casaObj->insertarReserva(
                    $id_usuario, $id_casa, $fecha_inicio, $fecha_fin,
                    $total_precio, $estado, $precio_noche, $num_banos, $num_cocinas,
                    $num_hab_individuales, $num_hab_familiares, $num_aparcamientos,
                    $num_lavadora, $num_secadora, $num_lavavajillas, $num_horno,
                    $num_microondas, $num_nevera, $num_congelador, $tiene_wifi,
                    $num_ascensores, $tiene_calefaccion, $tiene_aire_acondicionado,
                    $tiene_piscina, $tiene_banera, $tiene_barbacoa, $tiene_chimenea,
                    $tiene_adaptacion_discapacitados, $tiene_jardin, $tiene_patio,
                    $tiene_sala_cine, $tiene_secador_pelo, $imagen_guardada
                );
            } elseif ($accion === 'editar' && $id) {
                $casaObj->actualizarReserva(
                    $id, $id_usuario, $id_casa, $fecha_inicio, $fecha_fin,
                    $total_precio, $estado, $precio_noche, $num_banos, $num_cocinas,
                    $num_hab_individuales, $num_hab_familiares, $num_aparcamientos,
                    $num_lavadora, $num_secadora, $num_lavavajillas, $num_horno,
                    $num_microondas, $num_nevera, $num_congelador, $tiene_wifi,
                    $num_ascensores, $tiene_calefaccion, $tiene_aire_acondicionado,
                    $tiene_piscina, $tiene_banera, $tiene_barbacoa, $tiene_chimenea,
                    $tiene_adaptacion_discapacitados, $tiene_jardin, $tiene_patio,
                    $tiene_sala_cine, $tiene_secador_pelo, $imagen_guardada
                );
            }
            header("Location: casas2.php");
            exit();
        } catch (Exception $e) {
            $errores['general'] = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Casas Vacacionales</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="./assets/css/admin.css">
</head>
<body>
    <!-- Navbar -->
    <?php include("./menu.php"); ?>

    <div class="container-fluid mt-4">
        
        <!-- Estadísticas (siempre visibles) -->
        <div class="stats-container">
            <div class="stat-card total">
                <span class="stat-icon">🏠</span>
                <h3><?= $total_casas_todos ?></h3>
                <p><i class="bi bi-house-fill"></i> Total de Casas</p>
            </div>
            <div class="stat-card vip">
                <span class="stat-icon">⭐</span>
                <h3><?= $total_casas_vip ?></h3>
                <p><i class="bi bi-star-fill"></i> Casas Premium Accesibles</p>
            </div>
            <div class="stat-card precio">
                <span class="stat-icon">💰</span>
                <h3>€<?= number_format($precio_promedio, 0) ?></h3>
                <p><i class="bi bi-cash-coin"></i> Precio Promedio</p>
            </div>
        </div>

        <?php if ($accion === 'crear' || $accion === 'editar'): ?>
            <!-- FORMULARIO (visible solo cuando accion=crear o editar) -->
            <div class="card shadow-lg border-0">
                <div class="card-header" style="background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%); color: white;">
                    <h4 class="mb-0">
                        <i class="bi bi-<?= $accion === 'crear' ? 'plus-circle' : 'pencil-square' ?>"></i>
                        <?= $accion === 'crear' ? 'Crear Nueva Casa' : 'Editar Casa' ?>
                    </h4>
                </div>
                <div class="card-body" style="max-height: 70vh; overflow-y: auto;">
                    <?php if (!empty($errores)): ?>
                        <div class="alert alert-danger">
                            <strong>⚠️ Errores encontrados:</strong>
                            <ul class="mb-0">
                                <?php foreach ($errores as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data">
                        
                        <!-- INFORMACIÓN BÁSICA -->
                        <h6><i class="bi bi-info-circle-fill"></i> INFORMACIÓN BÁSICA</h6>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Propietario *</label>
                                <select name="id_usuario" class="form-select" required>
                                    <option value="">Seleccionar...</option>
                                    <?php foreach ($propietarios as $prop): ?>
                                        <option value="<?= $prop['id_usuario'] ?>" <?= $datos_casa['id_usuario'] == $prop['id_usuario'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($prop['username']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">total_precio de la Casa *</label>
                                <input type="text" name="total_precio" class="form-control" value="<?= htmlspecialchars($datos_casa['total_precio']) ?>" required>
                            </div>
                        </div>

                        <!-- UBICACIÓN -->
                        <h6><i class="bi bi-geo-alt-fill"></i> UBICACIÓN</h6>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Comunidad Autónoma *</label>
                                <select name="id_casa" class="form-select" required>
                                    <option value="">Seleccionar...</option>
                                    <?php foreach ($comunidades as $com): ?>
                                        <option value="<?= $com['id_casa'] ?>" <?= $datos_casa['id_casa'] == $com['id_casa'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($com['total_precio']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Provincia *</label>
                                <select name="fecha_inicio" class="form-select" required>
                                    <option value="">Seleccionar...</option>
                                    <?php foreach ($provincias as $prov): ?>
                                        <option value="<?= $prov['fecha_inicio'] ?>" <?= $datos_casa['fecha_inicio'] == $prov['fecha_inicio'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($prov['total_precio']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Ciudad *</label>
                                <select name="fecha_fin" class="form-select" required>
                                    <option value="">Seleccionar...</option>
                                    <?php foreach ($ciudades as $ciudad): ?>
                                        <option value="<?= $ciudad['fecha_fin'] ?>" <?= $datos_casa['fecha_fin'] == $ciudad['fecha_fin'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($ciudad['total_precio']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- estado Y PRECIO -->
                        <h6><i class="bi bi-cash-stack"></i> estado Y PRECIO</h6>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">estado (personas) *</label>
                                <input type="number" name="estado" class="form-control" min="1" value="<?= $datos_casa['estado'] ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Precio por Noche (€) *</label>
                                <input type="number" name="precio_noche" class="form-control" step="0.01" min="0" value="<?= $datos_casa['precio_noche'] ?>" required>
                            </div>
                        </div>

                        <!-- HABITACIONES Y BAÑOS -->
                        <h6><i class="bi bi-door-open-fill"></i> HABITACIONES Y BAÑOS</h6>
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label">Baños *</label>
                                <input type="number" name="num_banos" class="form-control" min="1" value="<?= $datos_casa['num_banos'] ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Cocinas *</label>
                                <input type="number" name="num_cocinas" class="form-control" min="1" value="<?= $datos_casa['num_cocinas'] ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Hab. Individuales</label>
                                <input type="number" name="num_hab_individuales" class="form-control" min="0" value="<?= $datos_casa['num_hab_individuales'] ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Hab. Familiares</label>
                                <input type="number" name="num_hab_familiares" class="form-control" min="0" value="<?= $datos_casa['num_hab_familiares'] ?>">
                            </div>
                        </div>

                        <!-- ELECTRODOMÉSTICOS -->
                        <h6><i class="bi bi-plug-fill"></i> ELECTRODOMÉSTICOS</h6>
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label">Lavadoras</label>
                                <input type="number" name="num_lavadora" class="form-control" min="0" value="<?= $datos_casa['num_lavadora'] ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Secadoras</label>
                                <input type="number" name="num_secadora" class="form-control" min="0" value="<?= $datos_casa['num_secadora'] ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Lavavajillas</label>
                                <input type="number" name="num_lavavajillas" class="form-control" min="0" value="<?= $datos_casa['num_lavavajillas'] ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Hornos</label>
                                <input type="number" name="num_horno" class="form-control" min="0" value="<?= $datos_casa['num_horno'] ?>">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label">Microondas</label>
                                <input type="number" name="num_microondas" class="form-control" min="0" value="<?= $datos_casa['num_microondas'] ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Neveras *</label>
                                <input type="number" name="num_nevera" class="form-control" min="1" value="<?= $datos_casa['num_nevera'] ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Congeladores</label>
                                <input type="number" name="num_congelador" class="form-control" min="0" value="<?= $datos_casa['num_congelador'] ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Aparcamientos</label>
                                <input type="number" name="num_aparcamientos" class="form-control" min="0" value="<?= $datos_casa['num_aparcamientos'] ?>">
                            </div>
                        </div>

                        <!-- AMENIDADES -->
                        <h6><i class="bi bi-stars"></i> AMENIDADES Y CARACTERÍSTICAS</h6>
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input type="checkbox" name="tiene_wifi" class="form-check-input" id="wifi" <?= $datos_casa['tiene_wifi'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="wifi">Wi-Fi</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input type="checkbox" name="tiene_calefaccion" class="form-check-input" id="calefaccion" <?= $datos_casa['tiene_calefaccion'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="calefaccion">Calefacción</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input type="checkbox" name="tiene_aire_acondicionado" class="form-check-input" id="aire" <?= $datos_casa['tiene_aire_acondicionado'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="aire">Aire Acondicionado</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input type="checkbox" name="tiene_piscina" class="form-check-input" id="piscina" <?= $datos_casa['tiene_piscina'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="piscina">Piscina</label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input type="checkbox" name="tiene_banera" class="form-check-input" id="banera" <?= $datos_casa['tiene_banera'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="banera">Bañera</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input type="checkbox" name="tiene_barbacoa" class="form-check-input" id="barbacoa" <?= $datos_casa['tiene_barbacoa'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="barbacoa">Barbacoa</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input type="checkbox" name="tiene_chimenea" class="form-check-input" id="chimenea" <?= $datos_casa['tiene_chimenea'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="chimenea">Chimenea</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input type="checkbox" name="tiene_adaptacion_discapacitados" class="form-check-input" id="adaptacion" <?= $datos_casa['tiene_adaptacion_discapacitados'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="adaptacion">Adaptación Discapacitados</label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input type="checkbox" name="tiene_jardin" class="form-check-input" id="jardin" <?= $datos_casa['tiene_jardin'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="jardin">Jardín</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input type="checkbox" name="tiene_patio" class="form-check-input" id="patio" <?= $datos_casa['tiene_patio'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="patio">Patio</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input type="checkbox" name="tiene_sala_cine" class="form-check-input" id="cine" <?= $datos_casa['tiene_sala_cine'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="cine">Sala de Cine</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input type="checkbox" name="tiene_secador_pelo" class="form-check-input" id="secador" <?= $datos_casa['tiene_secador_pelo'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="secador">Secador de Pelo</label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Ascensores</label>
                                <input type="number" name="num_ascensores" class="form-control" min="0" value="<?= $datos_casa['num_ascensores'] ?>">
                            </div>
                        </div>

                        <!-- IMAGEN -->
                        <h6><i class="bi bi-image-fill"></i> IMAGEN PRINCIPAL</h6>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Imagen Principal</label>
                                <input type="file" name="imagen_principal" class="form-control" accept="image/                          *">
                                <?php if (!empty($datos_casa['imagen_principal'])): ?>
                                    <small class="text-muted">Imagen actual: <?= htmlspecialchars($datos_casa['imagen_principal']) ?></small>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- BOTONES -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="casas2.php" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save-fill"></i> <?= $accion === 'crear' ? 'Crear Casa' : 'Actualizar Casa' ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        <?php else: ?>
            <!-- TABLA Y FILTROS (visible solo cuando NO hay accion) -->
            
            <!-- Filtros -->
            <div class="filters-card">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Provincia</label>
                        <select name="provincia" class="form-select">
                            <option value="">Todas</option>
                            <?php foreach ($provincias as $prov): ?>
                                <option value="<?= $prov['fecha_inicio'] ?>" <?= $filtro_provincia == $prov['fecha_inicio'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($prov['total_precio']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Ciudad</label>
                        <select name="ciudad" class="form-select">
                            <option value="">Todas</option>
                            <?php foreach ($ciudades as $ciudad): ?>
                                <option value="<?= $ciudad['fecha_fin'] ?>" <?= $filtro_ciudad == $ciudad['fecha_fin'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($ciudad['total_precio']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">estado Mín.</label>
                        <input type="number" name="estado" class="form-control" min="0" value="<?= $filtro_estado ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Precio Máx. (€)</label>
                        <input type="number" name="precio" class="form-control" min="0" value="<?= $filtro_precio != 999999 ? $filtro_precio : '' ?>">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-funnel-fill"></i> Filtrar
                        </button>
                    </div>
                </form>
            </div>

            <!-- Botón Añadir -->
            <div class="mb-3">
                <a href="?accion=crear" class="btn btn-primary btn-lg">
                    <i class="bi bi-plus-circle-fill"></i> Añadir Nueva Casa
                </a>
            </div>

            <!-- Tabla -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Imagen</th>
                            <th>total_precio</th>
                            <th>Ubicación</th>
                            <th>Precio/Noche</th>
                            <th>estado</th>
                            <th>Adaptación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($casas_pagina)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                    <p class="mt-2">No se encontraron casas con los filtros aplicados.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($casas_pagina as $casa): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($casa['imagen_principal'])): ?>
                                            <img src="<?= htmlspecialchars($casa['imagen_principal']) ?>" alt="Casa" width="50" height="50" style="object-fit: cover;">
                                        <?php else: ?>
                                            <span class="text-muted">Sin imagen</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($casa['total_precio']) ?></td>
                                    <td><?= htmlspecialchars($casa['ciudad'] ?? 'N/A') ?></td>
                                    <td><strong>€<?= number_format($casa['precio_noche'], 2) ?></strong></td>
                                    <td><?= $casa['estado'] ?> pers.</td>
                                    <td>
                                        <?php if ($casa['precio_noche'] >= 1000 && $casa['tiene_adaptacion_discapacitados']): ?>
                                            <span class="badge badge-vip">⭐ VIP Premium Accesible</span>
                                        <?php elseif ($casa['tiene_adaptacion_discapacitados']): ?>
                                            <span class="badge badge-accesible">♿ Accesible</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Estándar</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="?accion=editar&id=<?= $casa['id_casa'] ?>" class="btn btn-warning btn-action">
                                            <i class="bi bi-pencil-square"></i> Editar
                                        </a>
                                        <a href="?accion=eliminar&id=<?= $casa['id_casa'] ?>" 
                                           class="btn btn-danger btn-action"
                                           onclick="return confirm('¿Estás seguro de que deseas eliminar esta casa? Esta acción no se puede deshacer.');">
                                            <i class="bi bi-trash-fill"></i> Eliminar
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <?php if ($total_paginas > 1): ?>
                <nav>
                    <ul class="pagination">
                        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                            <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
                                <a class="page-link" href="?pagina=<?= $i ?>&provincia=<?= $filtro_provincia ?>&ciudad=<?= $filtro_ciudad ?>&estado=<?= $filtro_estado ?>&precio=<?= $filtro_precio ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>

        <?php endif; ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
