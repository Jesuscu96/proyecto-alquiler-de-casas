<?php
session_start();
require_once "./admin/includes/crudUsuarios.php";

$usuariosObj = new Usuarios();

// Redirige si ya hay sesión
if (isset($_SESSION['usuario'])) {
    header("Location: " . (in_array($_SESSION['usuario']['rol'], ['superAdmin', 'admin']) ? "./admin/index.php" : "./index.php"));
    exit();
}

// Datos para repintar
$nombre = $apellidos = $username = $email = $telefono = '';
$edadStr = '';

// Errores por campo y resumen
$errores = [];
$erroresUsername = '';
$erroresNombre = '';
$erroresApellidos = '';
$erroresEdad = '';
$erroresEmail = '';
$erroresPassword = '';
$erroresPasswordConfirm = '';
$erroresTelefono = '';
$erroresCoincidencia = '';

$exitoRegistro = '';
$errorRegistro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lectura (sin required en HTML)
    $nombre = trim($_POST['nombre'] ?? '');
    $apellidos = trim($_POST['apellidos'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $passwordConfirm = trim($_POST['password_confirmar'] ?? '');
    $edadStr = trim($_POST['edad'] ?? '');
    //$edad       = ($edadStr === '' ? null : (int)$edadStr); // edad INT

    // Validaciones campo a campo
    if ($username === '') {
        $erroresUsername = "El nombre de usuario es obligatorio.";
    }
    if ($nombre === '') {
        $erroresNombre = "El nombre es obligatorio.";
    }
    if ($apellidos === '') {
        $erroresApellidos = "Los apellidos son obligatorios.";
    }

    if ($edad === null || $edad < 18 || $edad > 120) {
        $erroresEdad = "La edad debe estar entre 18 y 120.";
    }

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erroresEmail = "El correo electrónico no es válido.";
    }

    if ($telefono === '') {
        $erroresTelefono = "El teléfono es obligatorio.";
    }

    if ($password === '') {
        $erroresPassword = "La contraseña es obligatoria.";
    }
    if ($passwordConfirm === '') {
        $erroresPasswordConfirm = "La confirmación de contraseña es obligatoria.";
    }
    if ($password !== '' && $passwordConfirm !== '' && $password !== $passwordConfirm) {
        $erroresCoincidencia = "Las contraseñas no coinciden.";
    }

    // Unicidad (solo si username/email no tienen errores previos)
    if ($erroresUsername === '' && $erroresEmail === '') {
        $usuarios = $usuariosObj->getAll(); // ; importante
        foreach ($usuarios as $u) {
            if ($u['username'] === $username) {
                $erroresUsername = "El nombre de usuario ya existe.";
            }
            if ($u['email'] === $email) {
                $erroresEmail = "El correo electrónico ya está registrado.";
            }
        }
    }

    // Construir resumen
    if ($erroresUsername !== '')
        $errores[] = $erroresUsername;
    if ($erroresNombre !== '')
        $errores[] = $erroresNombre;
    if ($erroresApellidos !== '')
        $errores[] = $erroresApellidos;
    if ($erroresEdad !== '')
        $errores[] = $erroresEdad;
    if ($erroresEmail !== '')
        $errores[] = $erroresEmail;
    if ($erroresPassword !== '')
        $errores[] = $erroresPassword;
    if ($erroresPasswordConfirm !== '')
        $errores[] = $erroresPasswordConfirm;
    if ($erroresTelefono !== '')
        $errores[] = $erroresTelefono;
    if ($erroresCoincidencia !== '')
        $errores[] = $erroresCoincidencia;

    // Insert si no hay errores
    if (empty($errores)) {
        try {
            // MISMO ORDEN que en admin/usuarios.php:
            // (username, nombre, apellidos, edad, email, password, rol, telefono)
            $usuariosObj->insertarUsuario($username, $nombre, $apellidos, $edad, $email, $password, 'cliente', $telefono);

            $exitoRegistro = "¡Registro exitoso! Ahora puedes iniciar sesión.";

            // Limpiar
            $nombre = $apellidos = $username = $email = $telefono = '';
            $password = $passwordConfirm = '';
            $edadStr = '';
        } catch (Exception $e) {
            $errorRegistro = "Error al registrar usuario: " . $e->getMessage();
        }
    } else {
        $errorRegistro = "Corrige los errores indicados en el formulario.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - ApartaHome</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="./css/login.css" rel="stylesheet">
</head>

<body>
    <div class="login-container">
        <div class="login-header">
            <div class="login-logo">🏠</div>
            <h1 class="login-titulo">ApartaHome</h1>
            <p class="login-subtitulo">Crea tu cuenta</p>
        </div>

        <?php if (!empty($errores)): ?>
            <div class="alert alert-danger">
                <strong>⚠️ Errores encontrados:</strong>
                <ul class="mb-0">
                    <?php foreach ($errores as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($exitoRegistro): ?>
            <div class="alerta alerta-exito">
                <i class="bi bi-check-circle"></i> <?= $exitoRegistro ?>
            </div>
            <p><a href="login.php" class="btn btn-primary"><i class="bi bi-box-arrow-in-right me-2"></i> Ir al login</a></p>
        <?php endif; ?>

        <?php if ($errorRegistro && !$exitoRegistro): ?>
            <div class="alerta alerta-error">
                <i class="bi bi-exclamation-circle"></i> <?= $errorRegistro ?>
            </div>
        <?php endif; ?>

        <div class="card shadow-lg border-0">
            <div class="card-header"
                style="background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%); color: white;">
                <h4 class="mb-0">
                    <i class="bi bi-person-plus"></i> Registro
                </h4>
            </div>

            <div class="card-body" style="max-height: 70vh; overflow-y: auto;">
                <form method="POST">
                    <h6><i class="bi bi-info-circle-fill"></i> Información del usuario</h6>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Usuario *</label>
                            <input type="text" name="username" class="form-control"
                                value="<?= htmlspecialchars($username ?? '') ?>">
                            <?php if (!empty($erroresUsername)): ?>
                                <div class="text-danger small mt-1"><?= $erroresUsername ?></div><?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nombre *</label>
                            <input type="text" name="nombre" class="form-control"
                                value="<?= htmlspecialchars($nombre ?? '') ?>">
                            <?php if (!empty($erroresNombre)): ?>
                                <div class="text-danger small mt-1"><?= $erroresNombre ?></div><?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Apellidos *</label>
                            <input type="text" name="apellidos" class="form-control"
                                value="<?= htmlspecialchars($apellidos ?? '') ?>">
                            <?php if (!empty($erroresApellidos)): ?>
                                <div class="text-danger small mt-1"><?= $erroresApellidos ?></div><?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Edad *</label>
                            <input type="number" name="edad" class="form-control" min="18" max="120"
                                value="<?= $edadStr !== '' ? (int) $edadStr : '' ?>">
                            <?php if (!empty($erroresEdad)): ?>
                                <div class="text-danger small mt-1"><?= $erroresEdad ?></div><?php endif; ?>
                        </div>
                    </div>

                    <h6><i class="bi bi-card-text"></i> Contacto y credenciales</h6>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Correo electrónico *</label>
                            <input type="email" name="email" class="form-control"
                                value="<?= htmlspecialchars($email ?? '') ?>">
                            <?php if (!empty($erroresEmail)): ?>
                                <div class="text-danger small mt-1"><?= $erroresEmail ?></div><?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Teléfono *</label>
                            <input type="tel" name="telefono" class="form-control"
                                value="<?= htmlspecialchars($telefono ?? '') ?>">
                            <?php if (!empty($erroresTelefono)): ?>
                                <div class="text-danger small mt-1"><?= $erroresTelefono ?></div><?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Contraseña *</label>
                            <input type="password" name="password" class="form-control">
                            <?php if (!empty($erroresPassword)): ?>
                                <div class="text-danger small mt-1"><?= $erroresPassword ?></div><?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Confirmación *</label>
                            <input type="password" name="password_confirmar" class="form-control">
                            <?php if (!empty($erroresPasswordConfirm)): ?>
                                <div class="text-danger small mt-1"><?= $erroresPasswordConfirm ?></div><?php endif; ?>
                            <?php if (!empty($erroresCoincidencia)): ?>
                                <div class="text-danger small mt-1"><?= $erroresCoincidencia ?></div><?php endif; ?>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <a href="login.php" class="btn btn-ghost-primary">
                            <i class="bi bi-arrow-left me-2"></i> Volver
                        </a>
                        <button type="submit" class="btn btn-primary-gradient">
                            <i class="bi bi-person-plus me-2"></i> Crear usuario
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</body>

</html>