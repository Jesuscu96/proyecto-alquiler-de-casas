<?php
include("../includes/sessions.php");
include("../includes/crudUsuarios.php");

$sesion = new Sesion();

// Verificar sesión
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
    if (empty($datos_usuario)) {
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
    }
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
    $edad = !empty($_POST['edad']) ? (int)$_POST['edad'] : 0;
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $passwordConfirm = trim($_POST['passwordConfirm'] ?? '');
    $rol = trim($_POST['rol'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');

    // Validaciones - SIN USAR "required" HTML
    if (empty($username)) {
        $errores['username'] = "El username no puede estar vacío.";
    }
    
    if (empty($nombre)) {
        $errores['nombre'] = "El nombre no puede estar vacío.";
    }
    
    if (empty($apellidos)) {
        $errores['apellidos'] = "Los apellidos no pueden estar vacíos.";
    }
    
    if (empty($edad) || $edad <= 0) {
        $errores['edad'] = "La edad no puede estar vacía.";
    }
    
    if (empty($email)) {
        $errores['email'] = "El email no puede estar vacío.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores['email'] = "El email no tiene un formato válido.";
    }
    
    if (empty($rol)) {
        $errores['rol'] = "El rol no puede estar vacío.";
    }
    
    if (empty($telefono)) {
        $errores['telefono'] = "El teléfono no puede estar vacío.";
    }

    // Validaciones específicas por acción
    if ($accion === 'crear') {
        if (empty($password)) {
            $errores['password'] = "La contraseña no puede estar vacía.";
        }
        
        if (empty($passwordConfirm)) {
            $errores['passwordConfirm'] = "La confirmación de contraseña no puede estar vacía.";
        }
        
        if (!empty($password) && !empty($passwordConfirm) && $password !== $passwordConfirm) {
            $errores['password'] = "Las contraseñas no coinciden.";
            $errores['passwordConfirm'] = "Las contraseñas no coinciden.";
        }
    } elseif ($accion === 'editarPass' && $id) {
        if (empty($password)) {
            $errores['password'] = "La contraseña no puede estar vacía.";
        }
        
        if (empty($passwordConfirm)) {
            $errores['passwordConfirm'] = "La confirmación de contraseña no puede estar vacía.";
        }
        
        if (!empty($password) && !empty($passwordConfirm) && $password !== $passwordConfirm) {
            $errores['password'] = "Las contraseñas no coinciden.";
            $errores['passwordConfirm'] = "Las contraseñas no coinciden.";
        }
    }

    // Guardar si no hay errores
    if (empty($errores)) {
        try {
            if ($accion === 'crear') {
                $usuarioObj->insertarUsuario($nombre, $apellidos, $email, $username, $password, $rol);
                $mensaje = "Usuario creado exitosamente.";
                header("Refresh: 2; url=usuarios.php");
            } elseif ($accion === "editar" && $id) {
                $usuarioObj->actualizarUsuario($id, $username, $nombre, $apellidos, $edad, $email, $rol, $telefono);
                $mensaje = "Usuario actualizado exitosamente.";
                header("Refresh: 2; url=usuarios.php");
            } elseif ($accion === "editarPass" && $id) {
                $usuarioObj->actualizarPassword($id, $password);
                $mensaje = "Contraseña actualizada exitosamente.";
                header("Refresh: 2; url=usuarios.php");
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
    <link rel="stylesheet" href="../admin.css">
</head>
<body>
    <?php include("menu.php"); ?>

    <main class="container-fluid">
        <!-- Estadísticas -->
        <div class="stats-container mt-4">
            <div class="stat-card total">
                <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
                <h3><?php echo $cantidadUsuarios; ?></h3>
                <p><i class="bi bi-person-check-fill icon-primary"></i> Total de usuarios</p>
            </div>
            <div class="stat-card cantidad">
                <div class="stat-icon"><i class="bi bi-person-fill"></i></div>
                <h3><?php echo $cantidadUsuariosCliente; ?></h3>
                <p><i class="bi bi-person icon-tertiary"></i> Clientes</p>
            </div>
            <div class="stat-card vip">
                <div class="stat-icon"><i class="bi bi-shield-check"></i></div>
                <h3><?php echo $cantidadUsuariosAdmin; ?></h3>
                <p><i class="bi bi-shield-lock-fill icon-accent"></i> Administradores</p>
            </div>
            <div class="stat-card precio">
                <div class="stat-icon"><i class="bi bi-crown"></i></div>
                <h3><?php echo $cantidadUsuariosSuperAdmin; ?></h3>
                <p><i class="bi bi-crown-fill icon-secondary"></i> SuperAdmins</p>
            </div>
        </div>

        <h2 class="section-title mt-5">
            <i class="bi bi-people-fill"></i> Gestión de Usuarios
        </h2>

        <!-- Mostrar mensajes de éxito o error -->
        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle-fill"></i> <?php echo htmlspecialchars($mensaje); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($errores) && isset($errores['general'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-circle-fill"></i> <?php echo htmlspecialchars($errores['general']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Mostrar errores de validación -->
        <?php if (!empty($errores) && count($errores) > (isset($errores['general']) ? 1 : 0)): ?>
            <div class="alert alert-warning alert-dismissible fade show">
                <i class="bi bi-exclamation-triangle-fill"></i> <strong>Errores de validación:</strong>
                <ul class="mb-0 mt-2">
                    <?php foreach ($errores as $campo => $error): ?>
                        <?php if ($campo !== 'general'): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Botón para crear usuario -->
        <div class="mb-3">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalUsuario" onclick="limpiarFormulario('crear')">
                <i class="bi bi-plus-circle"></i> Crear Nuevo Usuario
            </button>
        </div>

        <!-- Tabla de usuarios -->
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Nombre Completo</th>
                        <th>Email</th>
                        <th>Edad</th>
                        <th>Teléfono</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td>
                                <i class="bi bi-person-circle"></i>
                                <?php echo htmlspecialchars($usuario['username']); ?>
                            </td>
                            <td><?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellidos']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['edad']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['telefono']); ?></td>
                            <td>
                                <?php 
                                    $rol_class = match($usuario['rol']) {
                                        'SuperAdmin' => 'badge-vip',
                                        'admin' => 'badge-info',
                                        default => 'badge-secondary'
                                    };
                                ?>
                                <span class="badge <?php echo $rol_class; ?>">
                                    <?php echo htmlspecialchars($usuario['rol']); ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-info btn-action" data-bs-toggle="modal" data-bs-target="#modalUsuario" onclick="editarUsuario(<?php echo $usuario['id_usuario']; ?>)">
                                    <i class="bi bi-pencil-square"></i> Editar
                                </button>
                                <button class="btn btn-sm btn-warning btn-action" data-bs-toggle="modal" data-bs-target="#modalPassword" onclick="editarPassword(<?php echo $usuario['id_usuario']; ?>)">
                                    <i class="bi bi-key"></i> Pass
                                </button>
                                <button class="btn btn-sm btn-danger btn-action" onclick="confirmarEliminar(<?php echo $usuario['id_usuario']; ?>)">
                                    <i class="bi bi-trash"></i> Eliminar
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Modal para crear/editar usuario -->
    <div class="modal fade" id="modalUsuario" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitulo">Crear Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="formUsuario">
                    <div class="modal-body">
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

                        <!-- Campos de contraseña solo para crear -->
                        <div id="passwordFields" style="display: none;">
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
                                        <label for="passwordConfirm" class="form-label">Confirmar <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control <?php echo isset($errores['passwordConfirm']) ? 'is-invalid' : ''; ?>" id="passwordConfirm" name="passwordConfirm">
                                        <?php if (isset($errores['passwordConfirm'])): ?>
                                            <div class="invalid-feedback d-block"><?php echo $errores['passwordConfirm']; ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para cambiar contraseña -->
    <div class="modal fade" id="modalPassword" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cambiar Contraseña</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="formPassword">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="passwordNew" class="form-label">Nueva Contraseña <span class="text-danger">*</span></label>
                            <input type="password" class="form-control <?php echo isset($errores['password']) ? 'is-invalid' : ''; ?>" id="passwordNew" name="password">
                            <?php if (isset($errores['password'])): ?>
                                <div class="invalid-feedback d-block"><?php echo $errores['password']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="passwordNewConfirm" class="form-label">Confirmar Contraseña <span class="text-danger">*</span></label>
                            <input type="password" class="form-control <?php echo isset($errores['passwordConfirm']) ? 'is-invalid' : ''; ?>" id="passwordNewConfirm" name="passwordConfirm">
                            <?php if (isset($errores['passwordConfirm'])): ?>
                                <div class="invalid-feedback d-block"><?php echo $errores['passwordConfirm']; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Actualizar Contraseña</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include("../includes/footer.php"); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function limpiarFormulario(accion) {
            document.getElementById('formUsuario').reset();
            document.getElementById('modalTitulo').textContent = 'Crear Usuario';
            document.getElementById('passwordFields').style.display = 'block';
        }

        function editarUsuario(id) {
            // Implementar carga de datos del usuario
            console.log('Editar usuario:', id);
        }

        function editarPassword(id) {
            const form = document.getElementById('formPassword');
            form.action = '?accion=editarPass&id=' + id;
        }

        function confirmarEliminar(id) {
            if (confirm('¿Estás seguro de que deseas eliminar este usuario?')) {
                window.location.href = '?accion=eliminar&id=' + id;
            }
        }
    </script>
</body>
</html>