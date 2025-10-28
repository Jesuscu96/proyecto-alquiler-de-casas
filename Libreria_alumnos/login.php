<?php
require_once "./admin/includes/sessions.php";
$sesion = new Sessions();
$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['username'];
    $clave = $_POST['password'];
    $datos = $sesion->comprobarCredenciales($usuario, $clave);
    
    if ($datos) {
        $sesion->crearSesion($datos);
        header("Location: admin/index.php");
        exit();
    } else {
        $error = "Usuario o contraseña incorrectos";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acceso privado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include("menu.php"); ?>

<main class="col-md-9">
    <h2>Iniciar sesion</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    
    <form method="POST" class="mt-3" style="max-width: 400px;">
        <div class="mb-3">
            <label for="username" class="form-label">Usuario:</label>
            <input type="text" name="username" id="username" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Contraseña:</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Entrar</button>
    </form>
</main>


<?php include("footer.php"); ?>
