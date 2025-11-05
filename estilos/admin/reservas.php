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




 // Paginación
$pagina = (int)($_GET['pagina'] ?? 1);
$por_pagina = 8;
$total_reservas = count($reservas);
$total_paginas = ceil($total_reservas / $por_pagina);
$inicio = ($pagina - 1) * $por_pagina;
$reservas_pagina = array_slice($reservas, $inicio, $por_pagina);

// Calcular estadísticas

$totalReservas = count($reservas);


// Datos por defecto del formulario
$datos_reserva = [
    'id_usuario' => '',
    'id_reserva' => '',
    'fecha_inicio' => '',
    'fecha_fin' => '',
    'total_precio' => '',
    'estado' => '',
];

// Si es editar, cargar datos
if ($accion === "editar" && $id) {
    $datos_reserva = $reservaObj->getReservaById($id);
}

// Procesar eliminación
if ($accion === 'eliminar' && $id) {
    $reservaObj->eliminarReserva($id);
    header("Location: reservas.php");
    exit();
}

// Procesar formulario POST
$errores = [];
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_usuario = trim($_POST['id_usuario'] ?? '');
    $id_reserva = trim($_POST['id_reserva'] ?? '');
    $fecha_inicio = trim($_POST['fecha_inicio'] ?? '');
    $fecha_fin = trim($_POST['fecha_fin'] ?? '');
    $total_precio = (float)($_POST['total_precio'] ?? '');
    $estado = trim($_POST['estado'] ?? '');
    
   
    

    // Validaciones
    if ($accion === 'crear' || $accion === 'editar') {
        if (empty($id_usuario)) $errores['id_usuario'] = "El nombre del cliente no puede estar vacío.";
        if (empty($id_reserva)) $errores['id_reserva'] = "El de la reserva no puede estar vacío.";
        if (empty($fecha_inicio)) $errores['fecha_inicio'] = "La fecha de inicio no puede estar vacia.";
        if (empty($fecha_fin)) $errores['fecha_fin'] = "La fecha del fin no puede estar vacia.";
        if (empty($total_precio)) $errores['total_precio'] = "El total_precio no puede estar vacío.";
    }
    if (!empty($erroresId_usuario)) $errores[] = $erroresId_usuario;
    if (!empty($erroresId_reserva)) $errores[] = $erroresId_reserva;
    if (!empty($erroresFecha_inicio)) $errores[] = $erroresFecha_inicio;
    if (!empty($erroresFecha_fin)) $errores[] = $erroresFecha_fin;
    if (!empty($erroresTotal_precio)) $errores[] = $erroresTotal_precio;
    
    

    // Guardar si no hay errores
    if (empty($errores)) {
        try {
            if ($accion === 'crear') {
                $reservaObj->insertarReserva(
                    $id_usuario, $id_reserva, $fecha_inicio, $fecha_fin,
                    $total_precio, $estado);
            } elseif ($accion === 'editar' && $id) {
                $reservaObj->actualizarReserva(
                    $id, $id_usuario, $id_reserva, $fecha_inicio, $fecha_fin,
                    $total_precio, $estado);
            }
            header("Location: reservas.php");
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
    <title>Gestión de reservas Vacacionales</title>
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
                <h3><?= $totalReservas ?></h3>
                <p><i class="bi bi-house-fill"></i> Total de reservas</p>
            </div>
            <div class="stat-card vip">
                <span class="stat-icon">⭐</span>
                <h3>En desarollo</h3>
                <p><i class="bi bi-star-fill"></i> reservas Premium Accesibles</p>
            </div>
            <div class="stat-card precio">
                <span class="stat-icon">💰</span>
                <h3>En desarollo</h3>
                <p><i class="bi bi-cash-coin"></i> Precio Promedio</p>
            </div>
        </div>

        <?php if ($accion === 'crear' || $accion === 'editar'): ?>
            <!-- FORMULARIO (visible solo cuando accion=crear o editar) -->
            <div class="card shadow-lg border-0">
                <div class="card-header" style="background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%); color: white;">
                    <h4 class="mb-0">
                        <i class="bi bi-<?= $accion === 'crear' ? 'plus-circle' : 'pencil-square' ?>"></i>
                        <?= $accion === 'crear' ? 'Crear Nueva reserva' : 'Editar reserva' ?>
                    </h4>
                </div>
                <div class="card-body" style="max-height: 70vh; overflow-y: auto;">
                    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($errores)): ?>
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
                                <label class="form-label">Cliente *</label>
                                <input type="text" name="id_usuario" class="form-control" value="<?= htmlspecialchars($datos_reserva['id_usuario']) ?>">
                                <?php if (isset($erroresId_usuario) && !empty($erroresId_usuario)): ?>
                                    <div class="text-danger small mt-1"><?= $erroresId_usuario ?></div>
                                <?php endif; ?> 
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"> Nombre de la reservas*</label>
                                <input type="text" name="id_reserva" class="form-control" value="<?= htmlspecialchars($datos_reserva['id_reserva']) ?>">
                                <?php if (isset($erroresId_reserva) && !empty($erroresId_reserva)): ?>
                                    <div class="text-danger small mt-1"><?= $erroresId_reserva ?></div>
                                <?php endif; ?>
                                
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Precio Total *</label>
                                <input type="text" name="total_precio" class="form-control" value="<?= htmlspecialchars($datos_reserva['total_precio']) ?>">
                                <?php if (isset($erroresTotal_precio) && !empty($erroresTotal_precio)): ?>
                                    <div class="text-danger small mt-1"><?= $erroresTotal_precio ?></div>
                                <?php endif; ?>
                                
                            </div>
                        </div>                   

                        <!-- FECHAS -->
                        <h6><i class="bi bi-info-circle-fill"></i> FECHAS DE RESERVAS</h6>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Fecha Incio Reserva *</label>
                                
                                <input type="date" name="fecha_inicio" class="form-control" value="<?= htmlspecialchars($datos_reserva['fecha_inicio']) ?>">
                                <?php if (isset($erroresFecha_inicio) && !empty($erroresFecha_inicio)): ?>
                                    <div class="text-danger small mt-1"><?= $erroresFecha_inicio ?></div>
                                <?php endif; ?> 
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fecha Fim Reserva *</label>
                                
                                <input type="date" name="fecha_fin" class="form-control" value="<?= htmlspecialchars($datos_reserva['fecha_fin']) ?>">
                                <?php if (isset($erroresFecha_fin) && !empty($erroresFecha_fin)): ?>
                                    <div class="text-danger small mt-1"><?= $erroresFecha_fin ?></div>
                                <?php endif; ?>
                                
                            </div>
                        </div>

                        

                        <!-- BOTONES -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="reservas.php" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save-fill"></i> <?= $accion === 'crear' ? 'Crear reserva' : 'Actualizar Reserva' ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        <?php else: ?>
            <!-- TABLA Y FILTROS (visible solo cuando NO hay accion) -->
            
          

            <!-- Botón Añadir -->
            <div class="mb-3">
                <a href="?accion=crear" class="btn btn-primary btn-lg">
                    <i class="bi bi-plus-circle-fill"></i> Añadir Nueva Reserva
                </a>
            </div>

            <!-- Tabla -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Nombre Casa</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                            <th>Precio Total</th>
                            <th>Estado</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        
                        <?php foreach ($reservas as $reserva): ?>
                            <tr>
                                <td><?= htmlspecialchars($reserva['id_usuario']) ?></td>
                                <td><?= htmlspecialchars($reserva['id_casa']) ?></td>
                                <td><?= htmlspecialchars($reserva['fecha_inicio']) ?></td>
                                <td><?= htmlspecialchars($reserva['fecha_fin']) ?></td>
                                <td><?= htmlspecialchars($reserva['total_precio']) ?></td>
                                <td><?= htmlspecialchars($reserva['estado']) ?></td>
                                
                                <td>
                                    <a href="?accion=editar&id=<?= $reserva['id_reserva'] ?>" class="btn btn-warning btn-action">
                                        <i class="bi bi-pencil-square"></i> Editar
                                    </a>
                                    <a href="?accion=eliminar&id=<?= $reserva['id_reserva'] ?>" 
                                        class="btn btn-danger btn-action"
                                        onclick="return confirm('¿Estás seguro de que deseas eliminar esta reserva? Esta acción no se puede deshacer.');">
                                        <i class="bi bi-trash-fill"></i> Eliminar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        
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
