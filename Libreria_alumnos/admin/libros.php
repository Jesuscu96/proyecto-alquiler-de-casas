<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<?php
require_once "./includes/sessions.php";
$sesion = new Sessions();
if (!$sesion->comprobarSesion()) {
    header("Location: ../login.php");
    exit();
}

require_once "./includes/crudCategorias.php";
$categoriaObj = new Categorias();
$listaCategorias = $categoriaObj->showCategorias();

require_once "./includes/crudLibros.php";
$libroObj = new Libros();
$libros = $libroObj->getAll();

$accion = $_GET['accion'] ?? null;
$id = $_GET['id'] ?? null;
$mensaje = "";
if($accion == "eliminar" && $id){
    $libroObj->eliminarLibro($id);
    if ($libro && $libro['portada']) {
        $rutaPortada = "../portadas/" . $libro['portada'];
        if (file_exists($rutaPortada)) {
            unlink($rutaPortada);
        }
    }
    $mensaje = "Libro eliminado correctamente.";
    header("Location: libros.php");
    exit();
}
$datos_libro = ['titulo' => '',
                'autor' => '',
                'id_categoria' => '',
                'precio' => '',
                'fecha' => '',
                'portada' => '']; //para que el value del formulario salga vaci­o

if ($accion === "editar" && $id) {
    //guarda los datos de la categoria seleccionada en $categoria
    $datos_libro = $libroObj->getLibroById($id);
    $fecha_value_editar = date("Y-m-s", strtotime($datos_libro['fecha']));
}
// Procesar el formulario de creaciÃ³n o ediciÃ³n de categorÃ­a
$errorPortada = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titulo = $_POST['titulo'] ?? '';
    $autor = $_POST['autor'] ?? '';
    $id_categoria = $_POST['id_categoria'] ?? '';
    $precio = $_POST['precio'] ?? '';
    $fecha = $_POST['fecha'] ?? '';
    $portada = $_POST['portada'] ?? '';
    $portada = '';
    if (isset($_FILES['portada']) && $_FILES['portada']['error'] === UPLOAD_ERR_OK) {
        $nombreArchivo = basename($_FILES['portada']['name']);
        $directorioDestino = "../portadas/";
        $rutaArchivo = $directorioDestino . $nombreArchivo;

        if (file_exists($rutaArchivo)) {
            $errorPortada = "La imagen ya existe.";
        } else {
            if (move_uploaded_file($_FILES['portada']['tmp_name'], $rutaArchivo)) {
                $portada = $nombreArchivo;
            } else {
                $errorPortada = "Error al subir la imagen.";
            }
        } 
        
    } else {
        $errorPortada = "Debe seleccionar una imagen para la portada.";
    }
    if ($errorPortada === "") {
        if ($accion === "crear") {
            $libroObj->insertarLibro($titulo, $autor, $id_categoria, $precio, $fecha, $portada);
        } elseif ($accion === "editar" && $id) {
            $libroObj->actualizarLibro($id, $titulo, $autor, $id_categoria, $precio, $fecha, $portada, $id);
        }
        // Redirigir a la pÃ¡gina de categorÃ­as despuÃ©s de guardar
        header("Location: libros.php");
        exit();
    }
   
}

?>



<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestion de Libros</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include("./menu.php"); ?>

    <!-- Contenedor principal -->
    <div class="container mt-4">
        <div class="row justify-content-center">
            <main class="col-md-10">
                <h2>Libros</h2>

                <a href="libros.php?accion=crear" class="btn btn-success mb-3">Añadir nuevo libro</a>

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Titulo</th>
                            <th>Autor</th>
                            <th>Categoria</th>
                            <th>Precio</th>
                            <th>Fecha</th>
                            <th>Portada</th>                            
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($libros as $libro) : ?>
                        <tr>
                            <td><?=$libro['titulo']?></td>
                            <td><?=$libro['autor']?></td>
                            <td><?=$libro['id_categoria']?></td>
                            <td><?=$libro['precio']?></td>
                            <td><?=date("d-m-Y", strtotime($libro['fecha']))?></td>
                            <td><img src="../portadas/<?=$libro['portada']?>" alt="portada" width="30px"></td>
                            
                            <td>
                                <a href="libros.php?accion=editar&id=<?=$libro['id_libro']?>" class="btn btn-sm btn-primary">
                                    Editar
                                </a>
                                <a href="libros.php?accion=eliminar&id=<?=$libro['id_libro']?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estas seguro?')">
                                    Eliminar
                                </a>
                            </td>
                        </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
                <?php if ($accion === "crear" || ($accion === "editar" && $id)): ?>
                    
                        <!-- TÃ­tulo dependiendo de si se estÃ¡ creando o editando -->
                        <h3><?= $accion === "crear" ? "Nuevo libro" : "Editar libro" ?></h3>
                        
                        <!-- Formulario para ingresar el nombre de la categorÃ­a -->
                        <form method="post" enctype="multipart/form-data" class="mb-4" style="max-width: 400px;">
                            <div class="mb-2">
                                <label class="form-label">Titulo:</label>
                                <input type="text" name="titulo" class="form-control"
                                value="<?= htmlspecialchars($datos_libro['titulo']) ?>" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Autor:</label>
                                <input type="text" name="autor" class="form-control"
                                value="<?=htmlspecialchars($datos_libro['autor'])?>" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Categoria:</label>
                                <input type="text" name="id_categoria" class="form-control"
                                value="<?= htmlspecialchars($datos_libro['id_categoria']) ?>" required>
                            </div>
                            
                            <div class="mb-2">
                                <label class="form-label">Precio:</label>
                                <input type="text" name="precio" class="form-control"
                                value="<?= htmlspecialchars($datos_libro['precio'] ?? 0) ?>" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Fecha:</label>
                                <input type="date" name="fecha" class="form-control"
                                value="<?= $accion === 'crear' ? $datos_libro['fecha'] :  $fecha_value_editar ?>" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Portada: <?= htmlspecialchars($datos_libro['portada']) ?></label>
                                <input type="file" name="portada" class="form-control"
                                value="<?= htmlspecialchars($datos_libro['portada']) ?>" required>
                                
                            </div>
                            
                            

                            <!-- Botones para guardar o cancelar -->
                            <button type="submit" class="btn btn-primary">Guardar</button>
                            <a href="libros.php" class="btn btn-secondary">Cancelar</a>
                        </form>
                    <?php endif; ?>
            </main>
        </div>
    </div>
    
    <?php include("./footer.php"); ?>