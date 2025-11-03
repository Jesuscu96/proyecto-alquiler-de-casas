<?php
session_start();
require_once "./admin/includes/sessions.php";
require_once "./admin/includes/crudUsuarios.php";

$sesion = new Sessions();
$usuariosObj = new Usuarios();

// Si ya está logueado, redirigir
if ($sesion->comprobarSesion()) {
    if ($_SESSION['usuario']['rol'] === 'superAdmin' || $_SESSION['usuario']['rol'] === 'admin') {
        header("Location: ./admin/index.php");
    } else {
        header("Location: ./index.php");
    }
    exit();
}

$mostrar_registro = isset($_GET['registro']) ? true : false;
$errorLogin = '';
$errorRegistro = '';
$exitoRegistro = '';

// Procesar login
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['accion']) && $_POST['accion'] === 'login') {
    $usuario = isset($_POST['username']) ? trim($_POST['usurname']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (empty($usuario) || empty($password)) {
        $errorLogin = "Usuario y contraseña son obligatorios.";
    } else {
        $datos = $usuariosObj->comprobarCredenciales($usuario, $password);
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

// Procesar registro
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['accion']) && $_POST['accion'] === 'registro') {
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $password_confirmar = isset($_POST['password_confirmar']) ? trim($_POST['password_confirmar']) : '';

    $errores = [];

    // Validaciones
    if (empty($nombre)) {
        $errores[] = "El nombre es obligatorio.";
    }
    if (empty($username)) {
        $errores[] = "El nombre de usuario es obligatorio.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El correo electrónico no es válido.";
    }
    if (empty($password)) {
        $errores[] = "La contraseña es obligatoria.";
    }
    if ($password !== $password_confirmar) {
        $errores[] = "Las contraseñas no coinciden.";
    }
    if (strlen($password) < 6) {
        $errores[] = "La contraseña debe tener al menos 6 caracteres.";
    }

    // Verificar si el usuario ya existe
    $usuarios_existentes = $usuariosObj->getAll();
    foreach ($usuarios_existentes as $user) {
        if ($user['username'] === $username) {
            $errores[] = "El nombre de usuario ya existe.";
            break;
        }
        if ($user['email'] === $email) {
            $errores[] = "El correo electrónico ya está registrado.";
            break;
        }
    }

    if (empty($errores)) {
        try {
            // Crear usuario con rol 'cliente' por defecto
            $usuariosObj->insertarUsuario($nombre, $username, $email, $password, 'cliente');
            $exitoRegistro = "¡Registro exitoso! Ahora puedes iniciar sesión.";
            $mostrar_registro = false; // Volver al formulario de login

            // Limpiar los campos
            $nombre = '';
            $username = '';
            $email = '';
            $password = '';
            $password_confirmar = '';
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
    <title><?php echo $mostrar_registro ? 'Registro' : 'Login'; ?> - ApartaHome</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="./css/login.css" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <!-- Header -->
        <div class="login-header">
            <div class="login-logo">🏠</div>
            <h1 class="login-titulo">ApartaHome</h1>
            <p class="login-subtitulo">
                <?php echo $mostrar_registro ? 'Crea tu cuenta' : 'Inicia sesión'; ?>
            </p>
        </div>

        <?php if ($exitoRegistro): ?>
        <div class="alerta alerta-exito">
            <i class="bi bi-check-circle"></i> <?php echo $exitoRegistro; ?>
        </div>
        <?php endif; ?>

        <!-- FORMULARIO DE LOGIN -->
        <?php if (!$mostrar_registro): ?>
        <form method="POST">
            <input type="hidden" name="accion" value="login">

            <?php if ($errorLogin): ?>
            <div class="alerta alerta-error">
                <i class="bi bi-exclamation-circle"></i> <?php echo $errorLogin; ?>
            </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="usuario" class="form-label">Usuario</label>
                <input type="text" id="usuario" name="usuario" class="form-control" placeholder="Nombre de usuario" required>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Contraseña" required>
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

        <!-- FORMULARIO DE REGISTRO -->
        <?php else: ?>
        <form method="POST">
            <input type="hidden" name="accion" value="registro">

            <?php if ($errorRegistro): ?>
            <div class="alerta alerta-error">
                <i class="bi bi-exclamation-circle"></i> <?php echo $errorRegistro; ?>
            </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="nombre" class="form-label">Nombre completo</label>
                <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Tu nombre" value="<?php echo htmlspecialchars($nombre); ?>" required>
            </div>

            <div class="form-group">
                <label for="username" class="form-label">Nombre de usuario</label>
                <input type="text" id="username" name="username" class="form-control" placeholder="Nombre de usuario" value="<?php echo htmlspecialchars($username); ?>" required>
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Correo electrónico</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="tu@email.com" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>

            <div class="form-group">
                <label for="password_reg" class="form-label">Contraseña</label>
                <input type="password" id="password_reg" name="password" class="form-control" placeholder="Mínimo 6 caracteres" required>
            </div>

            <div class="form-group">
                <label for="password_confirmar" class="form-label">Confirmar contraseña</label>
                <input type="password" id="password_confirmar" name="password_confirmar" class="form-control" placeholder="Repite tu contraseña" required>
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
