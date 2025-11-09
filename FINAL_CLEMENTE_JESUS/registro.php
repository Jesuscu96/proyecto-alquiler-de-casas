<?php
session_start();
require_once "./admin/includes/crudUsuarios.php";
require_once "./admin/includes/sessions.php";
$sesion = new Sessions();
$usuariosObj = new Usuarios();

// Redirige si ya hay sesión
if ($sesion->comprobarSesion() ) {
  header("Location: ./login.php");
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
  $edad = ($edadStr === '' ? null : (int)$edadStr);

  // Validaciones campo a campo
  if ($username === '') $erroresUsername = "El nombre de usuario es obligatorio.";
  if ($nombre === '') $erroresNombre = "El nombre es obligatorio.";
  if ($apellidos === '') $erroresApellidos = "Los apellidos son obligatorios.";

  if ($edad === null || $edad < 18 || $edad > 120) $erroresEdad = "La edad debe estar entre 18 y 120.";

  if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $erroresEmail = "El correo electrónico no es válido.";

  if ($telefono === '') $erroresTelefono = "El teléfono es obligatorio.";

  if ($password === '') $erroresPassword = "La contraseña es obligatoria.";
  if ($passwordConfirm === '') $erroresPasswordConfirm = "La confirmación de contraseña es obligatoria.";
  if ($password !== '' && $passwordConfirm !== '' && $password !== $passwordConfirm) $erroresCoincidencia = "Las contraseñas no coinciden.";

  // Unicidad (solo si username/email no tienen errores previos)
  if ($erroresUsername === '' && $erroresEmail === '') {
    $usuarios = $usuariosObj->getAll();
    foreach ($usuarios as $u) {
      if ($u['username'] === $username) $erroresUsername = "El nombre de usuario ya existe.";
      if ($u['email'] === $email) $erroresEmail = "El correo electrónico ya está registrado.";
    }
  }

  // Construir resumen
  if ($erroresUsername !== '') $errores[] = $erroresUsername;
  if ($erroresNombre !== '') $errores[] = $erroresNombre;
  if ($erroresApellidos !== '') $errores[] = $erroresApellidos;
  if ($erroresEdad !== '') $errores[] = $erroresEdad;
  if ($erroresEmail !== '') $errores[] = $erroresEmail;
  if ($erroresPassword !== '') $errores[] = $erroresPassword;
  if ($erroresPasswordConfirm !== '') $errores[] = $erroresPasswordConfirm;
  if ($erroresTelefono !== '') $errores[] = $erroresTelefono;
  if ($erroresCoincidencia !== '') $errores[] = $erroresCoincidencia;

  // Insert si no hay errores
  if (empty($errores)) {
    try {
      // Orden esperado por tu CRUD:
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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Registro - ApartaHome</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="./css/styles.css" />
</head>
<body>
  <?php include("menu.php"); ?>

  <div class="main-container">
    <div class="form-container form-container-wide">
      <div class="form-header">
        <div class="form-logo"><i class="bi bi-person-plus-fill"></i></div>
        <h1 class="form-title">Crear cuenta</h1>
        <p class="form-subtitle">Únete a ApartaHome en pocos pasos</p>
      </div>

      <?php if ($exitoRegistro): ?>
        <div class="alert alert-success"><?= $exitoRegistro ?></div>
      <?php endif; ?>

      <?php if ($errorRegistro && empty($exitoRegistro)): ?>
        <div class="alert alert-error"><?= $errorRegistro ?></div>
      <?php endif; ?>

      <form method="POST">
        <!-- Fila 1: Usuario y Nombre -->
        <div class="form-grid">
          <div class="form-group">
            <label class="form-label">Usuario</label>
            <input type="text" name="username" class="form-input" value="<?= htmlspecialchars($username) ?>" placeholder="Elige tu usuario" />
            <?php if (!empty($erroresUsername)): ?><div class="form-error"><?= $erroresUsername ?></div><?php endif; ?>
          </div>

          <div class="form-group">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre" class="form-input" value="<?= htmlspecialchars($nombre) ?>" placeholder="Tu nombre" />
            <?php if (!empty($erroresNombre)): ?><div class="form-error"><?= $erroresNombre ?></div><?php endif; ?>
          </div>
        </div>

        <!-- Fila 2: Apellidos y Edad -->
        <div class="form-grid">
          <div class="form-group">
            <label class="form-label">Apellidos</label>
            <input type="text" name="apellidos" class="form-input" value="<?= htmlspecialchars($apellidos) ?>" placeholder="Tus apellidos" />
            <?php if (!empty($erroresApellidos)): ?><div class="form-error"><?= $erroresApellidos ?></div><?php endif; ?>
          </div>

          <div class="form-group">
            <label class="form-label">Edad</label>
            <input type="number" name="edad" class="form-input" value="<?= htmlspecialchars($edadStr) ?>" placeholder="Tu edad" min="18" max="120" />
            <?php if (!empty($erroresEdad)): ?><div class="form-error"><?= $erroresEdad ?></div><?php endif; ?>
          </div>
        </div>

        <!-- Fila 3: Email y Teléfono -->
        <div class="form-grid">
          <div class="form-group">
            <label class="form-label">Correo electrónico</label>
            <input type="email" name="email" class="form-input" value="<?= htmlspecialchars($email) ?>" placeholder="tu@email.com" />
            <?php if (!empty($erroresEmail)): ?><div class="form-error"><?= $erroresEmail ?></div><?php endif; ?>
          </div>

          <div class="form-group">
            <label class="form-label">Teléfono</label>
            <input type="tel" name="telefono" class="form-input" value="<?= htmlspecialchars($telefono) ?>" placeholder="Tu teléfono" />
            <?php if (!empty($erroresTelefono)): ?><div class="form-error"><?= $erroresTelefono ?></div><?php endif; ?>
          </div>
        </div>

        <!-- Fila 4: Contraseña y Confirmar -->
        <div class="form-grid">
          <div class="form-group">
            <label class="form-label">Contraseña</label>
            <input type="password" name="password" class="form-input" placeholder="Crea una contraseña fuerte" />
            <?php if (!empty($erroresPassword)): ?><div class="form-error"><?= $erroresPassword ?></div><?php endif; ?>
          </div>

          <div class="form-group">
            <label class="form-label">Confirmar contraseña</label>
            <input type="password" name="password_confirmar" class="form-input" placeholder="Confirma tu contraseña" />
            <?php if (!empty($erroresPasswordConfirm)): ?><div class="form-error"><?= $erroresPasswordConfirm ?></div><?php endif; ?>
            <?php if (!empty($erroresCoincidencia)): ?><div class="form-error"><?= $erroresCoincidencia ?></div><?php endif; ?>
          </div>
        </div>

        <button type="submit" class="btn btn-primary btn-block mt-4">Registrarse</button>
      </form>

      <p class="text-center mt-4 text-muted">
        ¿Ya tienes cuenta? <a href="login.php" class="link-text">Inicia sesión aquí</a>
      </p>
    </div>
  </div>

  <?php include("footer.php"); ?>
</body>
</html>
