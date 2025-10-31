<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$host = "localhost"; 
$user = "root";
$pass = "bbdd";
$dbname = "libreria"
?>

<?php
require_once "./includes/crudDispositivos.php";
$dispositivoObj = new Dispositivos();
$listaDispositivos = $dispositivoObj->getAll();

// require_once "./includes/sessions.php";
// $sesion = new Sessions();
// if (!$sesion->comprobarSesion()) {
//     header("L$dision: ../login.php");
//     exit();
// }

$accion = $_GET['accion'] ?? null;
$id = $_GET['id'] ?? null;
$mensaje = "";

//eliminar marca
if($accion == "eliminar" && $id){
    $dispositivoObj->eliminarDispositivo($id);
    $mensaje = "Dispositivo eliminado correctamente.";
    header("Location: dispositivos.php");
    exit();
}
$datos_dispositivo = ['marca' => '',
                'modelo' => '',
                'num_serie' => '',]; //para que el value del formulario salga vaci­o



if ($accion === "editar" && $id) {
    $datos_dispositivo = $dispositivoObj->getDispositivoById($id); 
}
// Procesar el formulario de creacion o edicion de$disegorÃ­a
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $marca = $_POST['marca'] ?? '';
    $modelo = $_POST['modelo'] ?? '';
    $num_serie = $_POST['num_serie'] ?? '';
   
    if ($accion === "crear") {
        $dispositivoObj->insertarDispositivo($marca, $modelo, $num_serie);
        header("Location: dispositivos.php");
        exit();
    }
    elseif ($accion === "editar" && $id) {
        // Actualización sin cambiar contraseña
        $dispositivoObj->actualizarDispositivo($id, $marca, $modelo, $num_serie);
        header("Location: dispositivos.php");
        exit();
    } 
    
}
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestion de dispositivos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include("./menu.php"); ?>

    <!-- Contenedor principal -->
    <div class="container mt-4">
        <div class="row justify-content-center">
            <main class="col-md-10">
                <h2>dispositivos</h2>

                <a href="dispositivos.php?accion=crear" class="btn btn-success mb-3">Añadir nueva marca</a>

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nombre de la marca</th>
                            <th>Nombre del modelo</th>
                            <th>Numero de serie</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($listaDispositivos as $dis) : ?>
                        <tr>
                            <td><?=$dis['marca']?></td>
                            <td><?=$dis['modelo']?></td>
                            <td><?=$dis['num_serie']?></td>
                            <td>
                                <a href="dispositivos.php?accion=editar&id=<?=$dis['id_dispositivo']?>" class="btn btn-sm btn-primary">
                                    Editar
                                </a>
                                <a href="dispositivos.php?accion=eliminar&id=<?=$dis['id_dispositivo']?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estas seguro?')">
                                    Eliminar
                                </a>
                            </td>
                        </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
                <?php if ($accion === "crear" || ($accion === "editar" && $id)): ?>
                    
                        <!-- TÃ­tulo dependiendo de si se estÃ¡ creando o editando -->
                        <h3><?= $accion === "crear" ? "Nueva marca" : "Editar marca" ?></h3>
                        
                        <!-- Formulario para ingresar el marca de la$disegorÃ­a -->
                        <form method="post" class="mb-4" style="max-width: 400px;">
                            <div class="mb-2">
                                <label class="form-label">Nombre de la marca:</label>
                                <input type="text" name="marca" class="form-control"
                                value="<?= htmlspecialchars($datos_dispositivo['marca']) ?>" required>
                            </div>
                             <div class="mb-2">
                                <label class="form-label">Nombre de la modelo:</label>
                                <input type="text" name="modelo" class="form-control"
                                value="<?= htmlspecialchars($datos_dispositivo['modelo']) ?>" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Nombre de la numero de serie:</label>
                                <input type="text" name="num_serie" class="form-control"
                                value="<?= htmlspecialchars($datos_dispositivo['num_serie']) ?>" required>
                            </div>

                            <!-- Botones para guardar o cancelar -->
                            <button type="submit" class="btn btn-primary">Guardar</button>
                            <a href="dispositivos.php" class="btn btn-secondary">Cancelar</a>
                        </form>
                    <?php endif; ?>
            </main>
        </div>
    </div>
    
        
        </div> <!-- Cierre de .row -->
        </div> <!-- Cierre de .container-fluid -->

<footer class="bg-dark text-white text-center py-3 mt-4">
    &copy; 2025 Librería Online - Todos los derechos reservados
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>