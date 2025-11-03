<?php
session_start();
require_once "./admin/includes/sessions.php";
require_once "./admin/includes/crudUsuarios.php";

$sesion = new Sessions();
$usuariosObj = new Usuarios();

// Si ya hay sesión, redirigir por rol
if ($sesion->comprobarSesion()) {
    if ($_SESSION['usuario']['rol'] === 'superAdmin' || $_SESSION['usuario']['rol'] === 'admin') {
        header("Location: ./admin/index.php");
    } else {
        header("Location: ./index.php");
    }
    exit();
}

// Estado de la vista
$mostrar_registro = isset($_GET['registro']) ? true : false;

// Mensajes y datos
$errorLogin = '';
$errorRegistro = '';
$exitoRegistro = '';

// Variables para repintar (registro)
$nombre = $apellidos = $username = $email = $telefono = '';
$edadStr = '';

// ======================= LOGIN =======================
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['accion']) && $_POST['accion'] === 'login') {
    // Nombres del form de login: usuario, password
    $usuario  = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if ($usuario === '' || $password === '') {
        $errorLogin = "Usuario y contraseña son obligatorios.";
    } else {
        // Sessions::comprobarCredenciales busca por username y hace password_verify
        $datos = $sesion->comprobarCredenciales($usuario, $password);
        if ($datos) {
            $sesion->crearSesion($datos);
            if ($_SESSION['usuario']['rol'] === 'superAdmin' || $_SESSION['usuario']['rol'] === 'admin') {
                header("Location: ./admin/index.php");
                exit();
            } else {
                header("Location: ./index.php");
                exit();
            }
        } else {
            $errorLogin = "Usuario o contraseña incorrectos.";
        }
    }
}

// ===================== REGISTRO ======================
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['accion']) && $_POST['accion'] === 'registro') {
    // Sin required en HTML: validar en backend
    $nombre     = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $apellidos  = isset($_POST['apellidos']) ? trim($_POST['apellidos']) : '';
    $username   = isset($_POST['username']) ? trim($_POST['username']) : '';
    $email      = isset($_POST['email']) ? trim($_POST['email']) : '';
    $telefono   = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
    $password   = isset($_POST['password']) ? trim($_POST['password']) : '';
    $password2  = isset($_POST['password_confirmar']) ? trim($_POST['password_confirmar']) : '';
    $edadStr    = isset($_POST['edad']) ? trim($_POST['edad']) : '';

    // Edad como int (mismo criterio que admin/usuarios.php)
    $edad = ($edadStr === '' ? null : (int)$edadStr);

    $errores = [];

    // Validaciones por campo (sin required)
    if ($username === '')   { $errores[] = "El nombre de usuario es obligatorio."; }
    if ($nombre === '')     { $errores[] = "El nombre es obligatorio."; }
    if ($apellidos === '')  { $errores[] = "Los apellidos son obligatorios."; }
    if ($edad === null || $edad < 1 || $edad > 120) { $errores[] = "La edad debe estar entre 1 y 120."; }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) { $errores[] = "El correo electrónico no es válido."; }
    if ($password === '')   { $errores[] = "La contraseña es obligatoria."; }
    if (strlen($password) < 6) { $errores[] = "La contraseña debe tener al menos 6 caracteres."; }
    if ($password !== $password2) { $errores[] = "Las contraseñas no coinciden."; }

    // Unicidad básica (puedes migrar a COUNT/UNIQUE + try/catch INSERT)
    $usuarios_existentes = $usuariosObj->getAll();
    foreach ($usuarios_existentes as $user) {
        if ($user['username'] === $username) { $errores[] = "El nombre de usuario ya existe."; break; }
        if ($user['email'] === $email)       { $errores[] = "El correo electrónico ya está registrado."; break; }
    }

    if (empty($errores)) {
        try {
            // Firma coherente con admin/usuarios.php:
            // (username, nombre, apellidos, edad, email, password, rol, telefono)
            $usuariosObj->insertarUsuario($username, $nombre, $apellidos, $edad, $email, $password, 'cliente', $telefono);

            $exitoRegistro    = "¡Registro exitoso! Ahora puedes iniciar sesión.";
            $mostrar_registro = false;

            // Limpiar campos
            $nombre = $apellidos = $username = $email = $telefono = '';
            $password = $password2 = '';
            $edadStr = '';
        } catch (Exception $e) {
            $errorRegistro = "Error al registrar usuario: " . $e->getMessage();
        }
    } else {
        $errorRegistro = implode("<br>", $errores);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $mostrar_registro ? 'Registro' : 'Login'; ?> - ApartaHome</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="./css/login.css" rel="stylesheet">
</head>
<body>
<div class="login-container">
    <div class="login-header">
        <div class="login-logo">🏠</div>
        <h1 class="login-titulo">ApartaHome</h1>
        <p class="login-subtitulo">
            <?= $mostrar_registro ? 'Crea tu cuenta' : 'Inicia sesión'; ?>
        </p>
    </div>

    <?php if ($exitoRegistro): ?>
        <div class="alerta alerta-exito">
            <i class="bi bi-check-circle"></i> <?= $exitoRegistro ?>
        </div>
    <?php endif; ?>

    <?php if (!$mostrar_registro): ?>
        <!-- ============== LOGIN ============== -->
        <form method="POST">
            <input type="hidden" name="accion" value="login">

            <?php if ($errorLogin): ?>
                <div class="alerta alerta-error">
                    <i class="bi bi-exclamation-circle"></i> <?= $errorLogin ?>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="usuario" class="form-label">Usuario</label>
                <input type="text" id="usuario" name="usuario" class="form-control" placeholder="Nombre de usuario">
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Contraseña">
            </div>

            <button type="submit" class="btn-login">
                <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
            </button>

            <div class="separador">
                <span>¿Nuevo usuario?</span>
            </div>

            <div class="login-footer">
                <p>¿No tienes cuenta? <a href="?registro=1">Regístrate aquí</a></p>
            </div>
        </form>
    <?php else: ?>
        <!-- ============= REGISTRO ============= -->
        <form method="POST">
            <input type="hidden" name="accion" value="registro">

            <?php if ($errorRegistro): ?>
                <div class="alerta alerta-error">
                    <i class="bi bi-exclamation-circle"></i> <?= $errorRegistro ?>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" id="nombre" name="nombre" class="form-control" value="<?= htmlspecialchars($nombre ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="apellidos" class="form-label">Apellidos</label>
                <input type="text" id="apellidos" name="apellidos" class="form-control" value="<?= htmlspecialchars($apellidos ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="username" class="form-label">Usuario</label>
                <input type="text" id="username" name="username" class="form-control" value="<?= htmlspecialchars($username ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="edad" class="form-label">Edad</label>
                <input type="number" id="edad" name="edad" class="form-control" min="1" max="120" value="<?= $edadStr !== '' ? (int)$edadStr : '' ?>">
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Correo electrónico</label>
                <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($email ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="telefono" class="form-label">Teléfono</label>
                <input type="tel" id="telefono" name="telefono" class="form-control" value="<?= htmlspecialchars($telefono ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="password_reg" class="form-label">Contraseña</label>
                <input type="password" id="password_reg" name="password" class="form-control" placeholder="Mínimo 6 caracteres">
            </div>

            <div class="form-group">
                <label for="password_confirmar" class="form-label">Confirmar contraseña</label>
                <input type="password" id="password_confirmar" name="password_confirmar" class="form-control" placeholder="Repite tu contraseña">
            </div>

            <button type="submit" class="btn-login">
                <i class="bi bi-person-plus"></i> Crear Cuenta
            </button>

            <div class="separador">
                <span>¿Ya tienes cuenta?</span>
            </div>

            <div class="login-footer">
                <p><a href="login.php">Volver al login</a></p>
            </div>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
