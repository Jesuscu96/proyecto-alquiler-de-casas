<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<?php
require_once "./includes/crudCategorias.php";
$categoriaObj = new Categorias();
$listaCategorias = $categoriaObj->showCategorias();

require_once "./includes/sessions.php";
$sesion = new Sessions();
if (!$sesion->comprobarSesion()) {
    header("Location: ../login.php");
    exit();
}

$accion = $_GET['accion'] ?? null;
$id = $_GET['id'] ?? null;
$mensaje = "";

//eliminar categoria
if($accion == "eliminar" && $id){
    $categoriaObj->eliminarCategoria($id);
    $mensaje = "Categoria eliminada correctamente.";
    header("Location: categorias.php");
    exit();
}

//Preparar datos del formulario
$categoria = ['categoria' => '']; //para que el value del formulario salga vacÃ­o
if ($accion === "editar" && $id) {
    //guarda los datos de la categoria seleccionada en $categoria
    $categoria = $categoriaObj->getById($id); 
}

// Procesar el formulario de creaciÃ³n o ediciÃ³n de categorÃ­a
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $_POST['categoria'] ?? '';
    
    if ($accion === "crear") {
        $categoriaObj->insertarCategoria($nombre);
    } elseif ($accion === "editar" && $id) {
        $categoriaObj->actualizarCategoria($id, $nombre);
    }
    // Redirigir a la pÃ¡gina de categorÃ­as despuÃ©s de guardar
    header("Location: categorias.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestion de Categorias</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include("./menu.php"); ?>

    <!-- Contenedor principal -->
    <div class="container mt-4">
        <div class="row justify-content-center">
            <main class="col-md-10">
                <h2>Categorias</h2>

                <a href="categorias.php?accion=crear" class="btn btn-success mb-3">Añadir nueva categoria</a>

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nombre de la categoria</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($listaCategorias as $cat) : ?>
                        <tr>
                            <td><?=$cat['categoria']?></td>
                            <td>
                                <a href="categorias.php?accion=editar&id=<?=$cat['id_categoria']?>" class="btn btn-sm btn-primary">
                                    Editar
                                </a>
                                <a href="categorias.php?accion=eliminar&id=<?=$cat['id_categoria']?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estas seguro?')">
                                    Eliminar
                                </a>
                            </td>
                        </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
                <?php if ($accion === "crear" || ($accion === "editar" && $id)): ?>
                    
                        <!-- TÃ­tulo dependiendo de si se estÃ¡ creando o editando -->
                        <h3><?= $accion === "crear" ? "Nueva categoria" : "Editar categoria" ?></h3>
                        
                        <!-- Formulario para ingresar el nombre de la categorÃ­a -->
                        <form method="post" class="mb-4" style="max-width: 400px;">
                            <div class="mb-2">
                                <label class="form-label">Nombre de la categoria:</label>
                                <input type="text" name="categoria" class="form-control"
                                value="<?= htmlspecialchars($categoria['categoria']) ?>" required>
                            </div>

                            <!-- Botones para guardar o cancelar -->
                            <button type="submit" class="btn btn-primary">Guardar</button>
                            <a href="categorias.php" class="btn btn-secondary">Cancelar</a>
                        </form>
                    <?php endif; ?>
            </main>
        </div>
    </div>
    
    <?php include("./footer.php"); ?>