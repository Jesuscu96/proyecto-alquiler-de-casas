<?php
require_once "./includes/crudUsuarios.php";
require_once "./includes/sessions.php";
$sesion = new Sessions();
if (!$sesion->comprobarSesion()) {
    header("Location: ../login.php");
    exit();
}
$usuarioObj = new Usuarios();

// Obtener datos
$allUsuarios = $usuarioObj->getAll();
$cantidadUsuarios = $usuarioObj->getCantidadUsuarios();
$cantidadUsuariosCliente = $usuarioObj->getCantidadUsuariosCliente();
$cantidadUsuariosAdmin = $usuarioObj->getCantidadUsuariosAdmin();
$cantidadUsuariosSuperAdmin = $usuarioObj->getCantidadUsuariosSuperAdmin();
// Parámetros de acción
$accion = $_GET['accion'] ?? null;
$id = $_GET['id'] ?? null;





// Paginación
/* $pagina = (int)($_GET['pagina'] ?? 1);
$por_pagina = 8;
$total_casas = count($casas);
$total_paginas = ceil($total_casas / $por_pagina);
$inicio = ($pagina - 1) * $por_pagina;
$casas_pagina = array_slice($casas, $inicio, $por_pagina);
 */

// Calcular estadísticas
/* $casas_vip = array_filter($todasLasCasas, function($casa) {
    return $casa['precio_noche'] >= 1000 && $casa['tiene_adaptacion_discapacitados'];
}); */
//$total_casas_todos = count($todasLasCasas);
//$total_casas_vip = count($casas_vip);
//$precio_promedio = !empty($todasLasCasas) ? array_sum(array_column($todasLasCasas, 'precio_noche')) / $total_casas_todos : 0;

// Datos por defecto del formulario
$datos_usuario = [
    'unsername' => '',
    'nombre' => '',
    'apellidos' => '',
    'edad' => '',
    'email' => '',
    'password' => '',
    'rol' => '',
    'telefono' => '',
];

// Si es editar, cargar datos
if ($accion === "editar" && $id) {
    $datos_usuario = $usuarioObj->getUsuarioById($id);
}

// Procesar eliminación
if ($accion === 'eliminar' && $id) {
    $usuarioObj->eliminarUsuario($id);
    header("Location: usuarios.php");
    exit();
}

// Procesar formulario POST
$errores = [];
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username'] ?? '');
    $nombre = trim($_POST['nombre'] ?? '');
    $apellidos = trim($_POST['apellidos'] ?? '');
    $edad = (int)($_POST['edad'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $rol = trim($_POST['rol'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    
    

    // Validaciones
    if (empty($username)) $errores['username'] = "El username no puede estar vacío.";
    if (empty($nombre)) $errores['nombre'] = "El nombre no puede estar vacío.";
    if (empty($apellidos)) $errores['apellidos'] = "Los apellidos no puede estar vacíos.";
    if (empty($edad)) $errores['edad'] = "La edad no puede estar vacía.";
    if (empty($eamil)) $errores['email'] = "El email no puede estar vacío.";
    if (empty($password)) $errores['password'] = "La contraseña no puede estar vacía.";
    if (empty($rol)) $errores['rol'] = "El rol no puede estar vacío.";
    if (empty($telefono)) $errores['telefono'] = "El telefono no puede estar vacío.";
    

    // Guardar si no hay errores
    if (empty($errores)) {
        try {
            if ($accion === 'crear') {
               if ($password !== $passwordConfirm) {
                    $mensaje = "Las contraseñas no coinciden.";
                } else {
                    $usuarioObj->insertarUsuario($nombre, $apellidos, $email, $username, $password);
                    header("Location: usuarios.php");
                    exit();
                } 
            }elseif ($accion === "editar" && $id) {
                // Actualización sin cambiar contraseña
                $usuarioObj->actualizarUsuario($id, $nombre, $apellidos, $email, $username);
                header("Location: usuarios.php");
                exit();
            } elseif ($accion === "editarPass" && $id) {
                // Validar contraseña antes de actualizar
                if ($password !== $passwordConfirm) {
                    $mensaje = "Las contraseñas no coinciden.";
                } else {
                    $usuarioObj->actualizarPassword($id, $password);
                    header("Location: usuarios.php");
                    exit();
                }
            }
        
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
    <title>Gestión de Usuarios</title>
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
                <span class="stat-icon"><i class="bi bi-person-fill icon-primary fs-2"></i></i></span>
                <h3><?= $cantidadUsuarios ?></h3>
                <p><i class="bi bi-person-fill icon-primary fs-2"></i> Total de usuarios</p>
            </div>
            <div class="stat-card vip">
                <span class="stat-icon"><i class="bi bi-person-fill icon-accent fs-2"></i></span>
                <h3><?= $cantidadUsuariosCliente ?></h3>
                <p><i class="bi bi-person-fill icon-accent fs-2"></i> Total de usuarios cliente</p>
            </div>
            <div class="stat-card precio">
                <span class="stat-icon"><i class="bi bi-person-fill icon-secondary fs-2"></i></span>
                <h3><?= $cantidadUsuariosAdmin ?></h3>
                <p><i class="bi bi-person-fill icon-secondary fs-2"></i> Total de usuarios admin</p>
            </div>
            <div class="stat-card cantidad">
                <span class="stat-icon"><i class="bi bi-person-fill icon-tertiary fs-2"></i></span>
                <h3><?= $cantidadUsuariosSuperAdmin ?></h3>
                <p><i class="bi bi-person-fill icon-tertiary fs-2"></i> Total de usuarios super admin</p>
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
                        
                        <!-- PARTE1 -->
                        <h6><i class="bi bi-info-circle-fill"></i> INFORMACIÓN DEL USUARIO </h6>
                        <div class="row mb-3">
                            
                            <div class="col-md-6">
                                <label class="form-label">Nombre de Usuario*</label>
                                <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($datos_usuario['username']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nombre *</label>
                                <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($datos_usuario['nombre']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Apellidos *</label>
                                <input type="text" name="apellidos" class="form-control" value="<?= htmlspecialchars($datos_usuario['apellidos']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Edad *</label>
                                <input type="number" name="edad" class="form-control" min="1" value="<?= $datos_usuario['edad'] ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Telefono *</label>
                                <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($datos_usuario['telefono']) ?>" required>
                            </div>
                        </div>
                        <!-- PARTE2 -->
                        <h6><i class="bi bi-cash-stack"></i> CREDENCIALES</h6>
                        <div class="row mb-3">
                             <div class="col-md-6">
                                <label class="form-label">Rol de usuario *</label>
                                <input type="text" name="rol" class="form-control" value="<?= htmlspecialchars($datos_usuario['rol']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Dirección de correo electronico *</label>
                                <input type="text" name="email" class="form-control" value="<?= htmlspecialchars($datos_usuario['email']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contraseña *</label>
                                <input type="password" name="password" class="form-control" value="" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contraseña confirmación *</label>
                                <input type="password" name="passwordConfirm" class="form-control" value="" required>
                            </div>
                        </div>
                        

                        

                        <!-- BOTONES -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="usuarios.php" class="btn btn-secondary">
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
                            <th>Nombre</th>
                            <th>Ubicación</th>
                            <th>Precio/Noche</th>
                            <th>Capacidad</th>
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
                                    <td><?= htmlspecialchars($casa['nombre']) ?></td>
                                    <td><?= htmlspecialchars($casa['ciudad'] ?? 'N/A') ?></td>
                                    <td><strong>€<?= number_format($casa['precio_noche'], 2) ?></strong></td>
                                    <td><?= $casa['capacidad'] ?> pers.</td>
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
                                    <?php if($_SESSION['usuario']['id'] !== (int)$usuario['id'] && $_SESSION['usuario']['rol'] === "cliente" || $_SESSION['usuario']['id'] !== (int)$usuario['id'] && $_SESSION['usuario']['rol'] === "superAdmin") { ?>
                                        <a href="?accion=eliminar&id=<?= $casa['id_casa'] ?>" 
                                           class="btn btn-danger btn-action"
                                           onclick="return confirm('¿Estás seguro de que deseas eliminar esta casa? Esta acción no se puede deshacer.');">
                                            <i class="bi bi-trash-fill"></i> Eliminar
                                        </a>
                                    <?php } ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <?php /*hay que borrar esta apertura y cierre de php 
            if ($total_paginas > 1): ?>
                <nav>
                    <ul class="pagination">
                        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                            <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
                                <a class="page-link" href="?pagina=<?= $i ?>&provincia=<?= $filtro_provincia ?>&ciudad=<?= $filtro_ciudad ?>&capacidad=<?= $filtro_usuarios ?>&precio=<?= $filtro_precio ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>; 
            */ ?>


        <?php endif; ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
