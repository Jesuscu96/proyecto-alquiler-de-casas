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
    <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Login - ApartaHome</title>
    <link rel="stylesheet" href="./css/login.css">
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css"> -->
    
</head>
<body>
  <form method="POST" class="card">
    <?php if ($errorLogin): ?><div class="alerta alerta-error"><?= $errorLogin ?></div><?php endif; ?>

    <label class="form-label">Usuario</label>
    <input type="text" name="usuario" class="form-control" value="<?= htmlspecialchars($usuario) ?>">
    <?php if (!empty($erroresUsuario)): ?><div class="text-danger small mt-1"><?= $erroresUsuario ?></div><?php endif; ?>

    <label class="form-label">Contraseña</label>
    <input type="password" name="password" class="form-control">
    <?php if (!empty($erroresPassword)): ?><div class="text-danger small mt-1"><?= $erroresPassword ?></div><?php endif; ?>

    <button type="submit" class="btn btn-primary mt-3">Iniciar sesión</button>

    <p class="mt-3">¿No tienes cuenta? <a href="registro.php">Regístrate</a></p>
  </form>
</body>
</html>
