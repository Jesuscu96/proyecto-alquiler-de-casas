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
$todasLasCasas = $casaObj->getAll();
$comunidades = $ubicacionObj->getAllComunidades();
$provincias = $ubicacionObj->getAllProvincias();
$ciudades = $ubicacionObj->getAllCiudades();
$propietarios = $usuariosObj->getAll();

// Filtros
$filtro_provincia = $_GET['provincia'] ?? '';
$filtro_ciudad = $_GET['ciudad'] ?? '';
$filtro_capacidad = (int)($_GET['capacidad'] ?? 0);
$filtro_precio = (float)($_GET['precio'] ?? 999999);

// Aplicar filtros
$casas = array_filter($todasLasCasas, function($casa) {
    global $filtro_provincia, $filtro_ciudad, $filtro_capacidad, $filtro_precio;

    if ($filtro_provincia && $casa['id_provincia'] != $filtro_provincia) return false;
    if ($filtro_ciudad && $casa['id_ciudad'] != $filtro_ciudad) return false;
    if ($casa['capacidad'] < $filtro_capacidad) return false;
    if ($casa['precio_noche'] > $filtro_precio) return false;

    return true;
});

$casas = array_values($casas); // Reindexar array

// Paginación - 8 casas por página
$pagina = (int)($_GET['pagina'] ?? 1);
$por_pagina = 8;
$total_casas = count($casas);
$total_paginas = ceil($total_casas / $por_pagina);
$inicio = ($pagina - 1) * $por_pagina;
$casas_pagina = array_slice($casas, $inicio, $por_pagina);

// Calcular estadísticas
$casas_vip = array_filter($todasLasCasas, function($casa) {
    return $casa['precio_noche'] >= 1000 && $casa['tiene_adaptacion_discapacitados'];
});
$total_casas_todos = count($todasLasCasas);
$total_casas_vip = count($casas_vip);
$precio_promedio = !empty($todasLasCasas) ? array_sum(array_column($todasLasCasas, 'precio_noche')) / $total_casas_todos : 0;

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
    
    if (!empty($_FILES['imagen_principal']['name'])) {
        $carpeta = './imagenes/';
        $nombreArchivo = basename($_FILES['imagen_principal']['name']);
        $rutaArchivo = $carpeta . $nombreArchivo;

        if (move_uploaded_file($_FILES['imagen_principal']['tmp_name'], $rutaArchivo)) {
            $imagen_guardada = $rutaArchivo;
        } else {
            $errores[] = "Error al subir la imagen.";
        }
    } else {
        $imagen_guardada = $datos_casa['imagen_principal'] ?? '';
    }


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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="./assets/css/styles.css">
    <link rel="stylesheet" href="./assets/css/admin.css">
</head>
<body>
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="casasamedia.php">
                <i class="bi bi-house-heart"></i> ADMINISTRACIÓN DE CASAS
            </a>
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
        <h2 class="section-title">
            <i class="bi bi-graph-up"></i> Panel de Control
        </h2>
        <div class="stats-container">
            <div class="stat-card total">
                <i class="bi bi-houses stat-icon"></i>
                <h3><?= $total_casas_todos ?></h3>
                <p><i class="bi bi-house"></i> Total de Casas</p>
            </div>
            <div class="stat-card vip">
                <i class="bi bi-crown stat-icon"></i>
                <h3><?= $total_casas_vip ?></h3>
                <p><i class="bi bi-star-fill"></i> Casas Premium Accesibles</p>
            </div>
            <div class="stat-card precio">
                <i class="bi bi-currency-euro stat-icon"></i>
                <h3>€<?= number_format($precio_promedio, 2) ?></h3>
                <p><i class="bi bi-graph-up"></i> Precio Promedio</p>
            </div>
        </div>

        <!-- FILTROS -->
        
        <!-- BOTÓN CREAR CASA -->
        <h2 class="section-title">
            <i class="bi bi-tools"></i> Gestión de Casas
        </h2>
        <div class="mb-3">
            <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#modalCasa" onclick="abrirModalCrear()">
                <i class="bi bi-plus-circle"></i> Crear Nueva Casa
            </button>
            <span class="ms-3 text-muted">
                Mostrando <strong><?= count($casas_pagina) ?></strong> de <strong><?= $total_casas ?></strong> casas
            </span>
        </div>

        <!-- TABLA DE CASAS -->
        <?php if (!empty($casas_pagina)): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th><i class="bi bi-image"></i> Imagen</th>
                            <th><i class="bi bi-tags"></i> Nombre</th>
                            <th><i class="bi bi-geo-alt"></i> Ubicación</th>
                            <th><i class="bi bi-euro"></i> Precio Por Noche</th>
                            <th><i class="bi bi-people"></i> Capacidad</th>
                            <th><i class="bi bi-star"></i> Adaptación para discapacitados</th>
                            <th><i class="bi bi-gear"></i> Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($casas_pagina as $casa): 
                            $es_vip = $casa['precio_noche'] >= 1000 && $casa['tiene_adaptacion_discapacitados'];
                        ?>
                            <tr>
                                <td>
                                    <?php if (!empty($casa['imagen_principal'])): ?>
                                        <img src="./imagenes/<?= htmlspecialchars($casa['imagen_principal']) ?>" 
                                             alt="<?= htmlspecialchars($casa['nombre']) ?>" 
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Sin imagen</span>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?= htmlspecialchars($casa['nombre']) ?></strong></td>
                                <td><?= htmlspecialchars($casa['ciudad'] ?? 'N/A') ?></td>
                                <td><strong>€<?= number_format($casa['precio_noche'], 2) ?></strong></td>
                                <td><?= $casa['capacidad'] ?> pers.</td>
                                <td>
                                    <?php if ($es_vip): ?>
                                        <span class="badge badge-vip"><i class="bi bi-crown-fill"></i> VIP Premium</span>
                                    <?php endif; ?>
                                    <?php if ($casa['tiene_adaptacion_discapacitados']): ?>
                                        <span class="badge badge-accesible"><i class="bi bi-wheelchair"></i> Accesible</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-info btn-action" 
                                            onclick="verDetalles(<?= htmlspecialchars(json_encode($casa)) ?>)">
                                        ℹ️ Info
                                    </button>
                                    <button type="button" class="btn btn-warning btn-action" 
                                            onclick="abrirModalEditar(<?= $casa['id_casa'] ?>)"value="accion=editar&id=<?=$casa['id_casa']?>">
                                        ✏️ Editar <?=$accion="editar"?>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-action" 
                                            onclick="confirmarEliminar(<?= $casa['id_casa'] ?>, '<?= htmlspecialchars($casa['nombre']) ?>')">
                                        🗑️ Eliminar
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- PAGINACIÓN -->
            <?php if ($total_paginas > 1): ?>
                <nav aria-label="Paginación">
                    <ul class="pagination">
                        <?php 
                        $query_params = http_build_query([
                            'provincia' => $filtro_provincia,
                            'ciudad' => $filtro_ciudad,
                            'capacidad' => $filtro_capacidad,
                            'precio' => $filtro_precio != 999999 ? $filtro_precio : ''
                        ]);
                        ?>
                        <?php if ($pagina > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?= $query_params ?>&pagina=1">
                                    <i class="bi bi-chevron-double-left"></i> Primera
                                </a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="?<?= $query_params ?>&pagina=<?= $pagina - 1 ?>">
                                    <i class="bi bi-chevron-left"></i> Anterior
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php 
                        $inicio_pag = max(1, $pagina - 2);
                        $fin_pag = min($total_paginas, $pagina + 2);

                        for ($i = $inicio_pag; $i <= $fin_pag; $i++):
                        ?>
                            <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
                                <a class="page-link" href="?<?= $query_params ?>&pagina=<?= $i ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($pagina < $total_paginas): ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?= $query_params ?>&pagina=<?= $pagina + 1 ?>">
                                    Siguiente <i class="bi bi-chevron-right"></i>
                                </a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="?<?= $query_params ?>&pagina=<?= $total_paginas ?>">
                                    Última <i class="bi bi-chevron-double-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="bi bi-inbox"></i> No hay casas registradas. 
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
                        <span id="modalTitulo">Crear Nueva Casa</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formCasa" method="POST">
                    <div class="modal-body">
                        <div id="erroresModal"></div>

                        <h6><i class="bi bi-info-circle"></i> Información Básica</h6>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="nombre" class="form-label">Nombre *</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" value ="<?=$datos_casa["nombre"]?>" required>
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
                                <input type="number" class="form-control" id="precio_noche" name="precio_noche" min="0" step="0.01" value="<?=$datos_casa['precio_noche']?>" required>
                            </div>
                        </div>

                        <h6><i class="bi bi-geo-alt"></i> Ubicación</h6>
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

                        <h6><i class="bi bi-door-closed"></i> Habitaciones</h6>
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

                        <h6><i class="bi bi-cup-straw"></i> Electrodomésticos</h6>
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

                        <h6><i class="bi bi-gear"></i> Servicios</h6>
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

                        <h6><i class="bi bi-tree"></i> Espacios Exteriores</h6>
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

                        <h6><i class="bi bi-heart"></i> Amenidades Extra</h6>
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
                                    <label class="form-check-label" for="tiene_secador_pelo">Secador Pelo</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="tiene_adaptacion_discapacitados" name="tiene_adaptacion_discapacitados">
                                <label class="form-check-label" for="tiene_adaptacion_discapacitados">
                                    <strong><i class="bi bi-wheelchair"></i> Adaptación para Discapacitados</strong>
                                </label>
                            </div>
                        </div>

                        <h6><i class="bi bi-image"></i> Imagen</h6>
                        <div class="mb-3">
                            <label for="imagen_principal" class="form-label">Imagen Principal</label>
                            <?php if (!empty($datos_casa['imagen_principal'])): ?>
                                <div class="mb-2">
                                    <img src="<?= htmlspecialchars($datos_casa['imagen_principal']) ?>" alt="Imagen actual" style="max-width: 150px; height: auto; border-radius: 6px;">
                                </div>
                            <?php endif; ?>
                            <input type="file" class="form-control" id="imagen_principal" name="imagen_principal" accept="image/*">
                            <small class="text-muted">Sube una imagen para la casa. La imagen se guardará en /admin/imagenes</small>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="btnSubmit">
                            <i class="bi bi-save"></i> Guardar
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
                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle"></i> Confirmar Eliminación</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro de que desea eliminar la casa?</p>
                    <p class="fw-bold" id="casaNombreEliminar"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="btnConfirmarEliminar">
                        <i class="bi bi-trash"></i> Sí, Eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL MAS INFORMACION -->
    <div class="modal fade" id="modalDetalles" tabindex="-1" aria-labelledby="modalDetallesLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
        <div class="modal-header bg-primary text-white">
            <h5 class="modal-title" id="modalDetallesLabel">Detalles de la Casa</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body" style="white-space: pre-wrap;" id="contenidoDetalles"></div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
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
            document.getElementById('modalTitulo').textContent = 'Crear Nueva Casa';
            document.getElementById('btnSubmit').innerHTML = '<i class="bi bi-save"></i> Guardar';
            document.getElementById('formCasa').reset();
            document.getElementById('formCasa').action = '';
            document.getElementById('formCasa').setAttribute('data-accion', 'crear');
            document.getElementById('erroresModal').innerHTML = '';
            idCasaActual = null;
        }

        function abrirModalEditar(id) {
            document.getElementById('modalTitulo').textContent = 'Editar Casa';
            document.getElementById('btnSubmit').innerHTML = '<i class="bi bi-save"></i> Guardar';
            document.getElementById('formCasa').setAttribute('data-accion', 'editar');
            idCasaActual = id;
            modalCasa.show();
        }

        /* function verDetalles(casa) {
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
                🎭 EXTRAS: Chimenea: ${casa.tiene_chimenea ? '✅' : '❌'} | Adaptación para Discapacitados: ${casa.tiene_adaptacion_discapacitados ? '✅' : '❌'}`;
            alert(detalles);
        } */
        function verDetalles(casa) {
            let detalles = `
            📋 BÁSICA: ${casa.nombre} | €${casa.precio_noche}/noche | ${casa.capacidad} pers.
            📍 UBICACIÓN: ${casa.ciudad}, ${casa.provincia}
            👤 PROPIETARIO: ${casa.propietario}
            🏠 HABITACIONES: ${casa.num_banos} baños | ${casa.num_cocinas} cocinas
            🍳 ELECTRODOMÉSTICOS: ${casa.num_lavadora} lavadora(s) | ${casa.num_lavavajillas} lavavajillas
            ⚙️ SERVICIOS: WiFi: ${casa.tiene_wifi ? '✅' : '❌'} | Calefacción: ${casa.tiene_calefaccion ? '✅' : '❌'}
            🏞️ EXTERIORES: Piscina: ${casa.tiene_piscina ? '✅' : '❌'} | Jardín: ${casa.tiene_jardin ? '✅' : '❌'}
            🎭 EXTRAS: Chimenea: ${casa.tiene_chimenea ? '✅' : '❌'} | Adaptación para Discapacitados: ${casa.tiene_adaptacion_discapacitados ? '✅' : '❌'}`;

            const modal = new bootstrap.Modal(document.getElementById('modalDetalles'));
            document.getElementById('contenidoDetalles').textContent = detalles;
            modal.show();
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
