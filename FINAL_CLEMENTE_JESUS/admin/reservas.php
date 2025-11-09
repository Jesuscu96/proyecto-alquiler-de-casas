<?php
require_once "./includes/crudReservas.php";
require_once "./includes/sessions.php";
$sesion = new Sessions();
if (!$sesion->comprobarSesion() || !in_array($_SESSION['usuario']['rol'], ["admin", "superAdmin"])) {
    header("Location: ../login.php");
    exit();
}

$reservaObj = new Reservas();

// Obtener datos
$reservas = $reservaObj->getAll();

// Parámetros de acción
$accion = $_GET['accion'] ?? null;
$id = $_GET['id'] ?? null;

// Calcular estadísticas
$totalReservas = $reservaObj->getTotalReservas();
$reservasConfirmadas = $reservaObj->getCantidadReservasConfirmadas();
$reservasCanceladas = $reservaObj->getCantidadReservasCanceladas();


// Paginación
$pagina = (int)($_GET['pagina'] ?? 1);
$por_pagina = 6;
$total_paginas = ceil($totalReservas / $por_pagina);
$inicio = ($pagina - 1) * $por_pagina;
$reservas_pagina = array_slice($reservas, $inicio, $por_pagina);






// Datos por defecto del formulario
$datos_reserva = [
    'id_usuario' => '',
    'id_casa' => '',
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
$erroresId_usuario = '';
$erroresId_reserva = '';
$erroresFecha_inicio = '';
$erroresFecha_fin = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_usuario = trim($_POST['id_usuario'] ?? '');
    $id_casa = trim($_POST['id_casa'] ?? '');
    $fecha_inicio = trim($_POST['fecha_inicio'] ?? '');
    $fecha_fin = trim($_POST['fecha_fin'] ?? '');
    $total_precio = (float)($_POST['total_precio'] ?? '');
    $estado = trim($_POST['estado'] ?? '');
    
   
    

    // Validaciones
    if ($accion === 'crear' || $accion === 'editar') {
        if (empty($id_usuario)) $errores['id_usuario'] = "El nombre del cliente no puede estar vacío.";
        if (empty($id_casa)) $errores['id_casa'] = "El de la reserva no puede estar vacío.";
        if (empty($fecha_inicio)) $errores['fecha_inicio'] = "La fecha de inicio no puede estar vacia.";
        if (empty($fecha_fin)) $errores['fecha_fin'] = "La fecha del fin no puede estar vacia.";
        if (empty($total_precio)) $errores['total_precio'] = "El total_precio no puede estar vacío.";
    }
    if (!empty($erroresId_usuario)) $errores[] = $erroresId_usuario;
    if (!empty($erroresId_casa)) $errores[] = $erroresId_casa;
    if (!empty($erroresFecha_inicio)) $errores[] = $erroresFecha_inicio;
    if (!empty($erroresFecha_fin)) $errores[] = $erroresFecha_fin;
    if (!empty($erroresTotal_precio)) $errores[] = $erroresTotal_precio;
    
    

    // Guardar si no hay errores
    if (empty($errores)) {
        try {
            if ($accion === 'crear') {
                $reservaObj->insertarReserva($id_usuario, $id_casa, $fecha_inicio, $fecha_fin, $total_precio, $estado);
            } elseif ($accion === 'editar' && $id) {
                $reservaObj->actualizarReserva($id, $id_usuario, $id_casa, $fecha_inicio, $fecha_fin, $total_precio, $estado);
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="./assets/css/admin.css">
</head>
<body>
    <!-- Navbar -->
    <?php include("./menu.php"); ?>

    <div class="container-fluid mt-4">
        
        <!-- Estadísticas (siempre visibles) -->
        <div class="stats-container">
            <div class="stat-card total">
                <span class="stat-icon"><i class="bi bi-calendar2-week"></i></span>
                <h3><?= $totalReservas ?></h3>
                <p><i class="bi bi-calendar2-week"></i></i> Total de reservas</p>
            </div>
            <div class="stat-card precio">
                <span class="stat-icon"><i class="bi bi-check-circle"></i></span>
                <h3><?= $reservasConfirmadas ?></h3>
                <p><i class="bi bi-check-circle"></i> Reservas Confirmadas</p>
            </div>
            <div class="stat-card cancel">
                <span class="stat-icon"><i class="bi bi-x-circle"></i></span>
                <h3><?= $reservasCanceladas ?></h3>
                <p><i class="bi bi-x-circle"></i></i> Reservas Canceldas</p>
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
                            <strong><i class="bi bi-exclamation-triangle-fill"></i> Errores encontrados:</strong>
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
                                <label class="form-label">ID Cliente *</label>
                                <input type="text" name="id_usuario" class="form-control" value="<?= htmlspecialchars($datos_reserva['id_usuario']) ?>" >
                                <?php if (isset($erroresId_usuario) && !empty($erroresId_usuario)): ?>
                                    <div class="text-danger small mt-1"><?= $erroresId_usuario ?></div>
                                <?php endif; ?> 
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"> ID Casa*</label>
                                <input type="text" name="id_casa" class="form-control" value="<?= htmlspecialchars($datos_reserva['id_casa']) ?>" >
                                <?php if (isset($erroresId_reserva) && !empty($erroresId_casa)): ?>
                                    <div class="text-danger small mt-1"><?= $erroresId_casa ?></div>
                                <?php endif; ?>
                                
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Precio Total *</label>
                                <input type="text" name="total_precio" class="form-control" value="<?= htmlspecialchars($datos_reserva['total_precio']) ?>">
                                <?php if (isset($erroresTotal_precio) && !empty($erroresTotal_precio)): ?>
                                    <div class="text-danger small mt-1"><?= $erroresTotal_precio ?></div>
                                <?php endif; ?>                                
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Estado *</label>
                                <select name="estado" class="form-select">
                                    <option value="" <?= empty($datos_reserva['estado']) ? 'selected' : '' ?> disabled hidden>Seleccionar...</option>
                                    <option value="confirmada">Confirmada</option>
                                    <option value="cancelada">Cancelada</option>
                                    
                                </select>
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
                        
                        <?php foreach ($reservas_pagina as $reserva): ?>
                            <tr>
                                <td><?= htmlspecialchars($reserva['usuario']) ?></td>
                                <td><?= htmlspecialchars($reserva['nombre_casa']) ?></td>
                                <td><?= htmlspecialchars($reserva['fecha_inicio']) ?></td>
                                <td><?= htmlspecialchars($reserva['fecha_fin']) ?></td>
                                <td><?= htmlspecialchars($reserva['total_precio']) ?></td>
                                <td><?= htmlspecialchars($reserva['estado']) ?></td>
                                <td>
                                <?php $estado = htmlspecialchars($reserva['estado']);
                                    $badgeClass = 'bg-secondary';
                                    if ($estado === 'confirmada') $badgeClass = 'badge-accesible';
                                    elseif ($estado === 'cancelada') $badgeClass = 'bg-danger';
                                ?>
                                <span class="badge <?= $badgeClass ?>"><?= ucfirst($estado) ?></span>
                                </td>
                                
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
           <?php
            if ($total_paginas > 1): ?>
                <nav>
                    <ul class="pagination">
                        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                            <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
                                <a class="page-link" href="?pagina=<?= $i ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>;

        <?php endif; ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
