<?php
session_start();
require_once "./admin/includes/sessions.php";
$sesion = new Sessions();

if ($sesion->comprobarSesion()) {
  header("Location: " . (in_array($_SESSION['usuario']['rol'], ['superAdmin','admin']) ? "./admin/index.php" : "./index.php"));
  exit();
}

$errorLogin = '';
$usuario = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $usuario  = trim($_POST['usuario'] ?? '');
  $password = trim($_POST['password'] ?? '');

  $erroresUsuario = $usuario === '' ? "El usuario es obligatorio." : '';
  $erroresPassword = $password === '' ? "La contraseña es obligatoria." : '';

  if ($erroresUsuario === '' && $erroresPassword === '') {
    $datos = $sesion->comprobarCredenciales($usuario, $password);
    if ($datos) {
      $sesion->crearSesion($datos);
      header("Location: " . (in_array($_SESSION['usuario']['rol'], ['superAdmin','admin']) ? "./admin/index.php" : "./index.php"));
      exit();
    } else {
      $errorLogin = "Usuario o contraseña incorrectos.";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Iniciar sesión - ApartaHome</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="./css/styles.css?v=<?php echo time(); ?>" />
</head>
<body>
  <?php include("menu.php"); ?>

  <div class="main-container">
    <div class="form-container">
      <div class="form-header">
        <div class="form-logo" style="color: var(--secondary);">
          <i class="bi bi-person-fill-lock bi-secondary" aria-hidden="true"></i>
        </div>
        <h1 class="form-title">Iniciar sesión</h1>
        <p class="form-subtitle">Accede a tu cuenta de ApartaHome</p>
      </div>

      <?php if ($errorLogin): ?>
        <div class="alert alert-error"><?= $errorLogin ?></div>
      <?php endif; ?>

      <form method="POST">
        <div class="form-group mb-3">
          <label class="form-label">Usuario</label>
          <input type="text" name="usuario" class="form-input" value="<?= htmlspecialchars($usuario) ?>" placeholder="Ingresa tu usuario" />
          <?php if (!empty($erroresUsuario)): ?>
            <div class="form-error"><?= $erroresUsuario ?></div>
          <?php endif; ?>
        </div>

        <div class="form-group mb-3">
          <label class="form-label">Contraseña</label>
          <input type="password" name="password" class="form-input" placeholder="Ingresa tu contraseña" />
          <?php if (!empty($erroresPassword)): ?>
            <div class="form-error"><?= $erroresPassword ?></div>
          <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary btn-block mt-4">Iniciar sesión</button>
      </form>

      <p class="text-center mt-4 text-muted">
        ¿No tienes cuenta? <a href="registro.php" class="link-text">Regístrate aquí</a>
      </p>
    </div>
  </div>

  <?php include("footer.php"); ?>
</body>
</html>
