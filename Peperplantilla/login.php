<?php
// session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Login PHP - VacacionalPlus</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container mt-5">
    <?php
    // if(isset($_GET['error'])) {
    //   echo '<div class="alert alert-danger">Email o contraseña incorrectos</div>';
    // }
    ?>
    <div class="row justify-content-center">
      <div class="col-md-6">
        <h3>Iniciar Sesión (Versión PHP)</h3>
        <form method="POST" action="procesar_login.php">
          <div class="mb-3">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="password">Contraseña</label>
            <input type="password" name="password" id="password" class="form-control" required>
          </div>
          <button type="submit" class="btn btn-primary w-100">Entrar</button>
        </form>
        <p class="mt-3">NOTA: Ver code_file:313 para versión completa con diseño split-screen.</p>
      </div>
    </div>
  </div>
</body>
</html>
