<?php
require_once "./includes/crudUsuarios.php";
require_once "./includes/sessions.php";
$sesion = new Sessions();
if (!$sesion->comprobarSesion() || !in_array($_SESSION['usuario']['rol'], ["admin", "superAdmin"])) {
    header("Location: ../login.php");
    exit();
}
$usuarioObj = new Usuarios();

// Obtener datos
$usuarios = $usuarioObj->getAll();
// Calcular estadísticas
$cantidadUsuarios = $usuarioObj->getCantidadUsuarios();
$cantidadUsuariosCliente = $usuarioObj->getCantidadUsuariosCliente();
$cantidadUsuariosAdmin = $usuarioObj->getCantidadUsuariosAdmin();
$cantidadUsuariosSuperAdmin = $usuarioObj->getCantidadUsuariosSuperAdmin();
// Parámetros de acción
$accion = $_GET['accion'] ?? null;
$id = $_GET['id'] ?? null;





// Paginación
$pagina = (int)($_GET['pagina'] ?? 1);
$por_pagina = 6;
$total_paginas = ceil($cantidadUsuarios / $por_pagina);
$inicio = ($pagina - 1) * $por_pagina;
$usuarios_pagina = array_slice($usuarios, $inicio, $por_pagina);

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
$erroresUsername = '';
$erroresNombre = '';
$erroresApellidos = '';
$erroresEdad = '';
$erroresEmail = '';
$erroresPassword = '';
$erroresPasswordConfirm = '';
$erroresRol = '';
$erroresTelefono = '';
$erroresCoincidencia = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    $username = trim($_POST['username'] ?? '');
    $nombre = trim($_POST['nombre'] ?? '');
    $apellidos = trim($_POST['apellidos'] ?? '');
    $edad = (int)($_POST['edad'] ?? '');
    //$edad = isset($_POST['edad']) ? trim($_POST['edad']) : '';
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $passwordConfirm = trim($_POST['passwordConfirm'] ?? '');
    $rol = trim($_POST['rol'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
   
    

   // Validaciones según acción
if ($accion === 'crear' || $accion === 'editar') {
    // Validaciones generales SOLO en crear y editar usuario (no en editarPass)
    if (empty($username)) $erroresUsername = "El username no puede estar vacío.";
    if (empty($nombre)) $erroresNombre = "El nombre no puede estar vacío.";
    if (empty($apellidos)) $erroresApellidos = "Los apellidos no pueden estar vacíos.";
    if (empty($edad)) $erroresEdad = "La edad no puede estar vacía.";
    if (empty($email)) $erroresEmail = "El email no puede estar vacío.";
    if (empty($rol)) $erroresRol = "El rol no puede estar vacío.";
    if (empty($telefono)) $erroresTelefono = "El teléfono no puede estar vacío.";
}

// Validaciones de password SOLO en crear y editarPass
if ($accion === 'crear' || $accion === 'editarPass') {
    if (empty($password)) $erroresPassword = "La contraseña no puede estar vacía.";
    if (empty($passwordConfirm)) $erroresPasswordConfirm = "La confirmación de contraseña no puede estar vacía.";
    if ($password !== $passwordConfirm) $erroresCoincidencia = "Las contraseñas no coinciden.";
}


    // Recopilar errores formulario
    if (!empty($erroresUsername)) $errores[] = $erroresUsername;
    if (!empty($erroresNombre)) $errores[] = $erroresNombre;
    if (!empty($erroresApellidos)) $errores[] = $erroresApellidos;
    if (!empty($erroresEdad)) $errores[] = $erroresEdad;
    if (!empty($erroresEmail)) $errores[] = $erroresEmail;
    if (!empty($erroresPassword)) $errores[] = $erroresPassword;
    if (!empty($erroresPasswordConfirm)) $errores[] = $erroresPasswordConfirm;
    if (!empty($erroresRol)) $errores[] = $erroresRol;
    if (!empty($erroresTelefono)) $errores[] = $erroresTelefono;
    if (!empty($erroresCoincidencia)) $errores[] = $erroresCoincidencia;
    
    if (empty($errores)) {
        try {
            if ($accion === 'crear') {
                $usuarioObj->insertarUsuario($username, $nombre, $apellidos, $edad, $email, $password, $rol, $telefono);

                $mensaje = "Usuario creado exitosamente.";
                header("Location: usuarios.php");
                exit;
            } elseif ($accion === 'editar' && $id) {
                $usuarioObj->actualizarUsuario($id, $username, $nombre, $apellidos, $edad, $email, $rol, $telefono);
                header("Location: usuarios.php");
                exit;
            } elseif ($accion === 'editarPass' && $id) {
                $usuarioObj->actualizarPassword($id, $password);; 
                
                header("Location: usuarios.php");
                exit;
            }
        } catch (Exception $e) {
            $errores[] = "Error: " . $e->getMessage();
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

        <?php if ($accion === 'crear'): ?>
            <!-- FORMULARIO (visible solo cuando accion=crear o editar) -->
            <div class="card shadow-lg border-0">
                <div class="card-header" style="background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%); color: white;">
                    <h4 class="mb-0">
                        <i class="bi bi-<?= $accion === 'crear' ? 'plus-circle' : 'pencil-square' ?>"></i>
                        <?= $accion === 'crear' ? 'Crear Nueva usuario' : 'Editar usuario' ?>
                    </h4>
                </div>
                <div class="card-body" style="max-height: 70vh; overflow-y: auto;">
                    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($errores)): ?>
                        <div class="alert alert-danger">
                            <strong><i class="bi bi-exclamation-triangle-fill"></i>Errores encontrados:</strong>
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
                                <input type="text" name="username" class="form-control" value="">
                                <?php if (isset($erroresUsername) && !empty($erroresUsername)): ?>
                                    <div class="text-danger small mt-1"><?= $erroresUsername ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nombre *</label>
                                <input type="text" name="nombre" class="form-control" value="">
                                <?php if (isset($erroresNombre) && !empty($erroresNombre)): ?>
                                    <div class="text-danger small mt-1"><?= $erroresNombre ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Apellidos *</label>
                                <input type="text" name="apellidos" class="form-control" value="">
                                <?php if (isset($erroresApellidos) && !empty($erroresApellidos)): ?>
                                    <div class="text-danger small mt-1"><?= $erroresApellidos ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Edad *</label>
                                <input type="number" name="edad" class="form-control" min="1" value="">
                                <?php if (isset($erroresEdad) && !empty($erroresEdad)): ?>
                                    <div class="text-danger small mt-1"><?= $erroresEdad ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Telefono *</label>
                                <input type="tel" name="telefono" class="form-control" value="">
                                <?php if (isset($erroresTelefono) && !empty($erroresTelefono)): ?>
                                    <div class="text-danger small mt-1"><?= $erroresTelefono ?></div>
                                <?php endif; ?>
                            </div>
                            
                        </div>
                        <!-- PARTE2 -->
                        <h6><i class="bi bi-card-text"></i> CREDENCIALES</h6>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <!-- solo sale el rol de superAdmin
                                 si el usuario es superAdmin para poder asignarlo -->>
                                <label class="form-label">Rol de usuario *</label>
                                <select class="form-select" name="rol">
                                    <option value="" selected disabled hidden>Selecciona una opción</option>
                                    <option value="cliente">Cliente</option>
                                    <option value="admin">admin</option>
                                <?php if($_SESSION['usuario']['rol'] === "superAdmin" ) { ?>
                                    <option value="superAdmin">SuperAdmin</option>
                                <?php } ?>
                                </select>
                                <?php if (isset($erroresRol) && !empty($erroresRol)): ?>
                                    <div class="text-danger small mt-1"><?= $erroresRol ?></div>
                                <?php endif; ?>

                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Dirección de correo electronico *</label>
                                <input type="email" name="email" class="form-control" value="">
                                <?php if (isset($erroresEmail) && !empty($erroresEmail)): ?>
                                    <div class="text-danger small mt-1"><?= $erroresEmail ?></div>
                                <?php endif; ?>
                                
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contraseña *</label>
                                <input type="password" name="password" class="form-control" value="">
                                <?php if (isset($erroresPassword) && !empty($erroresPassword)): ?>
                                    <div class="text-danger small mt-1"><?= $erroresPassword ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contraseña confirmación *</label>
                                <input type="password" name="passwordConfirm" class="form-control" value="">
                            </div>
                            <?php if (isset($erroresPasswordConfirm) && !empty($erroresPasswordConfirm)): ?>
                                <div class="text-danger small mt-1"><?= $erroresPasswordConfirm ?></div>
                            <?php endif; ?>
                            <?php if (isset($erroresCoincidencia) && !empty($erroresCoincidencia)): ?>
                                <div class="text-danger small mt-1"><?= $erroresCoincidencia ?></div>
                            <?php endif; ?>

                            
                        </div>
                        <!-- BOTONES -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="usuarios.php" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save-fill"></i> <?= $accion === 'crear' ? 'Crear usuario' : 'Actualizar usuario' ?>
                            </button>
                        </div>
        <?php elseif ($accion === 'editar'): ?>
            <!-- FORMULARIO (visible solo cuando accion=crear o editar) -->
            <div class="card shadow-lg border-0">
                <div class="card-header" style="background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%); color: white;">
                    <h4 class="mb-0">
                        <i class="bi bi-<?= $accion === 'crear' ? 'plus-circle' : 'pencil-square' ?>"></i>
                        <?= $accion === 'crear' ? 'Crear Nueva usuario' : 'Editar usuario' ?>
                    </h4>
                </div>
                <div class="card-body" style="max-height: 70vh; overflow-y: auto;">
                    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($errores)): ?>
                        <div class="alert alert-danger">
                            <strong><i class="bi bi-exclamation-triangle-fill"></i>Errores encontrados:</strong>
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
                                <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($datos_usuario['username']) ?>">
                                <?php if (isset($erroresUsername) && !empty($erroresUsername)): ?>
                                    <div class="text-danger small mt-1"><?= $erroresUsername ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nombre *</label>
                                <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($datos_usuario['nombre']) ?>">
                                <?php if (isset($erroresNombre) && !empty($erroresNombre)): ?>
                                    <div class="text-danger small mt-1"><?= $erroresNombre ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Apellidos *</label>
                                <input type="text" name="apellidos" class="form-control" value="<?= htmlspecialchars($datos_usuario['apellidos']) ?>">
                                <?php if (isset($erroresApellidos) && !empty($erroresApellidos)): ?>
                                    <div class="text-danger small mt-1"><?= $erroresApellidos ?></div>
                                <?php endif; ?>
                                
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Edad *</label>
                                <input type="number" name="edad" class="form-control" min="18" value="<?= htmlspecialchars($datos_usuario['edad']) ?>">
                                <?php if (isset($erroresEdad) && !empty($erroresEdad)): ?>
                                    <div class="text-danger small mt-1"><?= htmlspecialchars($erroresEdad) ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Telefono *</label>
                                <input type="tel" name="telefono" class="form-control" value="<?= htmlspecialchars($datos_usuario['telefono']) ?>">
                                <?php if (isset($erroresTelefono) && !empty($erroresTelefono)): ?>
                                    <div class="text-danger small mt-1"><?= $erroresTelefono ?></div>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($mensaje)) { ?>
                            <div class="mb-2">
                                <p style="color:red; font-weight:bold;"><?= htmlspecialchars($mensaje) ?></p>
                            </div>
                            <?php } ?>
                        </div>
                        <!-- PARTE2 -->
                        <h6><i class="bi bi-card-text"></i> CREDENCIALES</h6>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                
                                <!-- Cargo los datos del rol de cada uno y solo sale el rol de superAdmin
                                 si el usuario es superAdmin para poder asignarlo y un usuario admin no puede cambiar el rol de un usuario superAdmin -->

                                <label class="form-label">Rol de usuario *</label>
                                <select class="form-select" name="rol">
                                <?php if($datos_usuario['rol'] !== "cliente") { ?>
                                    <option value="cliente">cliente</option>
                                <?php } ?>
                                    
                                <?php if($datos_usuario['rol'] !== "admin") { ?>
                                    <option value="admin">admin</option>
                                <?php } ?>    
                                    <option value="<?=htmlspecialchars($datos_usuario['rol']) ?>" selected><?= htmlspecialchars($datos_usuario['rol']) ?></option>
                                <?php if($_SESSION['usuario']['rol'] === "superAdmin" && $datos_usuario['rol'] !== "superAdmin" ) { ?>
                                    <option value="superAdmin">SuperAdmin</option>
                                <?php } ?>
                                </select>
                                <?php if (isset($erroresRol) && !empty($erroresRol)): ?>
                                    <div class="text-danger small mt-1"><?= $erroresRol ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Dirección de correo electronico *</label>
                                <input type="email" name="email" class="form-control" value="<?=htmlspecialchars($datos_usuario['email']) ?>">
                                <?php if (isset($erroresEmail) && !empty($erroresEmail)): ?>
                                    <div class="text-danger small mt-1"><?= $erroresEmail ?></div>
                                <?php endif; ?>
                            </div>
                        
                            
                        </div>
                        <!-- BOTONES -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="usuarios.php" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save-fill"></i> <?= $accion === 'crear' ? 'Crear usuario' : 'Actualizar usuario' ?>
                            </button>
                        </div>                
        <?php elseif (($accion === "editarPass" && $id)): ?>
            <div class="card shadow-lg border-0">
                <div class="card-header" style="background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%); color: white;">
                    <h4 class="mb-0">
                        <i class="bi bi-<?= $accion === 'crear' ? 'plus-circle' : 'pencil-square' ?>"></i>
                        <?= $accion === 'crear' ? 'Crear Nueva usuario' : 'Editar contraseña' ?>
                    </h4>
                </div>
                <div class="card-body" style="max-height: 70vh; overflow-y: auto;">
                    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($errores)): ?>
                        <div class="alert alert-danger">
                            <strong><i class="bi bi-exclamation-triangle-fill"></i>Errores encontrados:</strong>
                            <ul class="mb-0">
                                <?php foreach ($errores as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>                
                    <form method="POST" enctype="multipart/form-data">
                        <h6><i class="bi bi-card-text"></i> CONTRASEÑAS</h6>
                        <div class="card-body" style="max-height: 70vh; overflow-y: auto;">
                            <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($errores)): ?>
                                <div class="alert alert-danger">
                                    <strong><i class="bi bi-exclamation-triangle-fill"></i>Errores encontrados:</strong>
                                    <ul class="mb-0">
                                        <?php foreach ($errores as $error): ?>
                                            <li><?= htmlspecialchars($error) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Contraseña *</label>
                                <input type="password" name="password" class="form-control" value="">
                                <?php if (isset($erroresPassword) && !empty($erroresPassword)): ?>
                                    <div class="text-danger small mt-1"><?= $erroresPassword ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contraseña confirmación *</label>
                                <input type="password" name="passwordConfirm" class="form-control" value="">
                                <?php if (isset($erroresPasswordConfirm) && !empty($erroresPasswordConfirm)): ?>
                                    <div class="text-danger small mt-1"><?= $erroresPasswordConfirm ?></div>
                                <?php endif; ?>
                                <?php if (isset($erroresCoincidencia) && !empty($erroresCoincidencia)): ?>
                                    <div class="text-danger small mt-1"><?= $erroresCoincidencia ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- BOTONES -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="usuarios.php" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save-fill"></i> <?= $accion === 'editarPass' ? 'Actualizar contraseña' : 'Crear contraseña' ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        <?php else: ?>
           

            <!-- Botón Añadir -->
            <div class="mb-3">
                <a href="?accion=crear" class="btn btn-primary btn-lg">
                    <i class="bi bi-plus-circle-fill"></i> Añadir Nueva usuario
                </a>
            </div>

            <!-- Tabla -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Nombre</th>
                            <th>Apellidos</th>
                            <th>Edad</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>telefono</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        <?php foreach ($usuarios_pagina as $usuario): ?>
                            <tr>
                                <td><?= htmlspecialchars($usuario['username']) ?></td>
                                <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                                <td><?= htmlspecialchars($usuario['apellidos']) ?></td>
                                <td><?= htmlspecialchars($usuario['edad']) ?></td>
                                <td><?= htmlspecialchars($usuario['email']) ?></td>
                                <td><?= htmlspecialchars($usuario['rol']) ?></td>
                                <td><?= htmlspecialchars($usuario['telefono']) ?></td>                
                                <td>
                                    <a href="?accion=editar&id=<?= $usuario['id_usuario'] ?>" class="btn btn-warning btn-action">
                                        <i class="bi bi-pencil-square"></i> Editar
                                    </a>
                                    <?php if (( ($_SESSION['usuario']['rol'] === "admin" && $usuario['rol'] !== "superAdmin"  || 
                                    $_SESSION['usuario']['rol'] === "superAdmin")) || ( $_SESSION['usuario']['rol'] === "superAdmin")) { ?>
                                        <a href="usuarios.php?accion=editarPass&id=<?=$usuario['id_usuario']?>" class="btn btn-outline-dark btn-action" >
                                        <i class="bi bi-key"></i> Editar Pass
                                        </a>
                                    <?php } ?>
                                    
                                    <?php if (($_SESSION['usuario']['id_usuario'] !== (int)$usuario['id_usuario'] && ($_SESSION['usuario']['rol'] === "admin" || 
                                    $_SESSION['usuario']['rol'] === "superAdmin")) || ($_SESSION['usuario']['id_usuario'] !== (int)$usuario['id_usuario'] && $_SESSION['usuario']['rol'] === "superAdmin")) { ?>
                                        <a href="?accion=eliminar&id=<?= $usuario['id_usuario'] ?>" 
                                        class="btn btn-danger btn-action"
                                        onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario? Esta acción no se puede deshacer.');">
                                            <i class="bi bi-trash-fill"></i> Eliminar
                                        </a>
                                    <?php } ?>

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