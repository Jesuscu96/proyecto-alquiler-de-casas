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
$usuarios = $usuarioObj->getAll();
$cantidadUsuarios = $usuarioObj->getCantidadUsuarios();
$cantidadUsuariosCliente = $usuarioObj->getCantidadUsuariosCliente();
$cantidadUsuariosAdmin = $usuarioObj->getCantidadUsuariosAdmin();
$cantidadUsuariosSuperAdmin = $usuarioObj->getCantidadUsuariosSuperAdmin();

// Parámetros de acción
$accion = $_GET['accion'] ?? null;
$id = $_GET['id'] ?? null;

// Datos por defecto del formulario
$datos_usuario = [
    'username' => '',
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
$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username'] ?? '');
    $nombre = trim($_POST['nombre'] ?? '');
    $apellidos = trim($_POST['apellidos'] ?? '');
    $edad = (int)($_POST['edad'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $passwordConfirm = $_POST['passwordConfirm'] ?? '';
    $rol = trim($_POST['rol'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');

    // Validaciones
    if (empty($username)) $errores['username'] = "El username no puede estar vacío.";
    if (empty($nombre)) $errores['nombre'] = "El nombre no puede estar vacío.";
    if (empty($apellidos)) $errores['apellidos'] = "Los apellidos no puede estar vacíos.";
    if (empty($edad)) $errores['edad'] = "La edad no puede estar vacía.";
    if (empty($email)) $errores['email'] = "El email no puede estar vacío.";
    if (empty($rol)) $errores['rol'] = "El rol no puede estar vacío.";
    if (empty($telefono)) $errores['telefono'] = "El telefono no puede estar vacío.";
    
    // Validaciones específicas por acción
    if ($accion === 'crear' || $accion === 'editarPass') {
        if (empty($password)) $errores['password'] = "La contraseña no puede estar vacía.";
        if (empty($passwordConfirm)) $errores['passwordConfirm'] = "La confirmación de contraseña no puede estar vacía.";
    }

    // Guardar si no hay errores
    if (empty($errores)) {
        try {
            if ($accion === 'crear') {
                if ($password !== $passwordConfirm) {
                    $mensaje = "Las contraseñas no coinciden.";
                } else {
                    $usuarioObj->insertarUsuario($username, $nombre, $apellidos, $edad, $email, $password, $rol, $telefono);
                    header("Location: usuarios.php");
                    exit();
                }
            } elseif ($accion === "editar" && $id) {
                // Actualización sin cambiar contraseña
                $usuarioObj->actualizarUsuario($id, $username, $nombre, $apellidos, $edad, $email, $rol, $telefono);
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
    <title>Gestión de Usuarios - CasasApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="./assets/css/admin.css">
    <link rel="stylesheet" href="./assets/css/styles.css">
</head>
<body>
    <?php include("menu.php"); ?>

    <main class="container-fluid">
        <!-- Estadísticas -->
        <div class="stats-container mt-4">
            <div class="stat-card total">
                <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
                <h3><?php echo $cantidadUsuarios; ?></h3>
                <p>Total de usuarios</p>
            </div>
            <div class="stat-card cantidad">
                <div class="stat-icon"><i class="bi bi-person-fill"></i></div>
                <h3><?php echo $cantidadUsuariosCliente; ?></h3>
                <p>Total de usuarios cliente</p>
            </div>
            <div class="stat-card vip">
                <div class="stat-icon"><i class="bi bi-shield-check"></i></div>
                <h3><?php echo $cantidadUsuariosAdmin; ?></h3>
                <p>Total de usuarios admin</p>
            </div>
            <div class="stat-card precio">
                <div class="stat-icon"><i class="bi bi-crown"></i></div>
                <h3><?php echo $cantidadUsuariosSuperAdmin; ?></h3>
                <p>Total de usuarios super admin</p>
            </div>
        </div>

        <h2 class="section-title mt-5">
            <i class="bi bi-people-fill"></i> Gestión de Usuarios
        </h2>

        <!-- Mostrar errores de validación -->
        <?php if (!empty($errores)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle-fill"></i> <strong>Errores de validación:</strong>
                <ul class="mb-0 mt-2">
                    <?php foreach ($errores as $campo => $error): ?>
                        <?php if ($campo !== 'general'): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Mostrar mensaje de error general -->
        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i> <?php echo htmlspecialchars($mensaje); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Botones de acción -->
        <div class="mb-3">
            <a href="?accion=crear" class="btn btn-primary btn-lg">
                <i class="bi bi-plus-circle"></i> Crear Nuevo Usuario
            </a>
        </div>

        <!-- Tabla de usuarios -->
        <div class="table-responsive">
            <table class="table table-striped">
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
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td>
                                <i class="bi bi-person-circle"></i>
                                <?php echo htmlspecialchars($usuario['username']); ?>
                            </td>
                            <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                            <td><?= htmlspecialchars($usuario['apellidos']) ?></td>
                            <td><?= htmlspecialchars($usuario['edad']) ?></td>
                            <td><?= htmlspecialchars($usuario['email']) ?></td>
                            <td><?= htmlspecialchars($usuario['rol']) ?></td>
                            <td><?= htmlspecialchars($usuario['telefono']) ?></td>
                            <td>
                                <a href="?accion=editar&id=<?php echo $usuario['id_usuario']; ?>" class="btn btn-warning btn-action">
                                    <i class="bi bi-pencil-square"></i> Editar
                                </a>
                                <a href="?accion=editarPass&id=<?php echo $usuario['id_usuario']; ?>" class="btn btn-outline-warning btn-action">
                                    <i class="bi bi-key"></i> Editar Password
                                </a>
                                <a href="?accion=eliminar&id=<?php echo $usuario['id_usuario']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario?')">
                                    <i class="bi bi-trash"></i> Eliminar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>    
        </div>

        <!-- Formulario para crear/editar usuario -->
        <?php if ($accion === 'crear' || ($accion === 'editar' && $id)): ?>
            <div class="card mt-5">
                <div class="card-header">
                    <h5 class="mb-0">
                        <?php echo ($accion === 'crear') ? '➕ Crear Nuevo Usuario' : '✏️ Editar Usuario'; ?>
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <input type="hidden" name="accion" value="<?php echo htmlspecialchars($accion); ?>">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($id ?? ''); ?>">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?php echo isset($errores['username']) ? 'is-invalid' : ''; ?>" id="username" name="username" value="<?php echo htmlspecialchars($datos_usuario['username'] ?? ''); ?>">
                                    <?php if (isset($errores['username'])): ?>
                                        <div class="invalid-feedback d-block"><?php echo $errores['username']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control <?php echo isset($errores['email']) ? 'is-invalid' : ''; ?>" id="email" name="email" value="<?php echo htmlspecialchars($datos_usuario['email'] ?? ''); ?>">
                                    <?php if (isset($errores['email'])): ?>
                                        <div class="invalid-feedback d-block"><?php echo $errores['email']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?php echo isset($errores['nombre']) ? 'is-invalid' : ''; ?>" id="nombre" name="nombre" value="<?php echo htmlspecialchars($datos_usuario['nombre'] ?? ''); ?>">
                                    <?php if (isset($errores['nombre'])): ?>
                                        <div class="invalid-feedback d-block"><?php echo $errores['nombre']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="apellidos" class="form-label">Apellidos <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?php echo isset($errores['apellidos']) ? 'is-invalid' : ''; ?>" id="apellidos" name="apellidos" value="<?php echo htmlspecialchars($datos_usuario['apellidos'] ?? ''); ?>">
                                    <?php if (isset($errores['apellidos'])): ?>
                                        <div class="invalid-feedback d-block"><?php echo $errores['apellidos']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edad" class="form-label">Edad <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control <?php echo isset($errores['edad']) ? 'is-invalid' : ''; ?>" id="edad" name="edad" value="<?php echo htmlspecialchars($datos_usuario['edad'] ?? ''); ?>">
                                    <?php if (isset($errores['edad'])): ?>
                                        <div class="invalid-feedback d-block"><?php echo $errores['edad']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="telefono" class="form-label">Teléfono <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control <?php echo isset($errores['telefono']) ? 'is-invalid' : ''; ?>" id="telefono" name="telefono" value="<?php echo htmlspecialchars($datos_usuario['telefono'] ?? ''); ?>">
                                    <?php if (isset($errores['telefono'])): ?>
                                        <div class="invalid-feedback d-block"><?php echo $errores['telefono']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="rol" class="form-label">Rol <span class="text-danger">*</span></label>
                            <select class="form-select <?php echo isset($errores['rol']) ? 'is-invalid' : ''; ?>" id="rol" name="rol">
                                <option value="">Selecciona un rol...</option>
                                <option value="cliente" <?php echo (($datos_usuario['rol'] ?? '') === 'cliente') ? 'selected' : ''; ?>>Cliente</option>
                                <option value="admin" <?php echo (($datos_usuario['rol'] ?? '') === 'admin') ? 'selected' : ''; ?>>Admin</option>
                                <option value="SuperAdmin" <?php echo (($datos_usuario['rol'] ?? '') === 'SuperAdmin') ? 'selected' : ''; ?>>SuperAdmin</option>
                            </select>
                            <?php if (isset($errores['rol'])): ?>
                                <div class="invalid-feedback d-block"><?php echo $errores['rol']; ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Campos de contraseña solo para crear y editarPass -->
                        <?php if ($accion === 'crear' || $accion === 'editarPass'): ?>
                            <h6><i class="bi bi-lock"></i> Contraseña</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Contraseña <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control <?php echo isset($errores['password']) ? 'is-invalid' : ''; ?>" id="password" name="password">
                                        <?php if (isset($errores['password'])): ?>
                                            <div class="invalid-feedback d-block"><?php echo $errores['password']; ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="passwordConfirm" class="form-label">Confirmar Contraseña <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control <?php echo isset($errores['passwordConfirm']) ? 'is-invalid' : ''; ?>" id="passwordConfirm" name="passwordConfirm">
                                        <?php if (isset($errores['passwordConfirm'])): ?>
                                            <div class="invalid-feedback d-block"><?php echo $errores['passwordConfirm']; ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-check-circle"></i> Guardar
                            </button>
                            <a href="usuarios.php" class="btn btn-secondary btn-lg">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>

    </main>

    <?php include("../includes/footer.php"); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>