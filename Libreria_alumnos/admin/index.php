<?php
require_once("./includes/sessions.php");
$sesion = new Sessions();

if (!$sesion->comprobarSesion()) {
    header("Location: ../login.php");
    exit();
}

$usuario = $_SESSION['usuario'];//array con los datos

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de administracion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include("./menu.php"); ?>

<!-- Contenido principal centrado -->
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <h2 class="mb-4">Bienvenido al panel de administracion</h2>
            <p>Hola, <strong> <?= $usuario['nombre']?> <?= $usuario['apellidos']?></strong>. Has accedido correctamente al area privada.</p>
        </div>
    </div>
</div>

<?php include("./footer.php"); ?>
