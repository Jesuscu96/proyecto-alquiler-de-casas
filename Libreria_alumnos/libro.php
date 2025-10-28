<?php 
require_once "./admin/includes/crudLibros.php";
$libroObj = new Libros();
if (isset($_GET['id'])) { //si hay id en la url
    $id = $_GET['id'];
    $libroObj->sumarVisita($id);
    $libro = $libroObj->getLibroById($id);
   
} else {//si no hay id en la url
    header("Location:index.php");
    exit;
}
// Formateamos la fecha al uso horario de España
$fechaOriginal = new Datetime($libro['fecha']);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include("menu.php"); ?>

<main class="col-md-9">
    <a href="index.php" class="btn btn-secondary mb-3">Volver</a>

    <div class="row">
        <div class="col-md-4">
            <img src="portadas/<?= $libro['portada']?>" class="img-fluid" alt="<?= $libro['titulo']?>">
        </div>
        <div class="col-md-8">
            <h2><?= $libro['titulo']?></h2>
            <p><strong>Autor: </strong><?= $libro['autor']?></p>
            <p><strong>Categoría: </strong><?= $libro['categoria']?></p>
            <p><strong>Precio: </strong><?= $libro['precio']?></p>
            <p><strong>Visitas: </strong><?= $libro['visitas']?></p>
            <p><strong>Fecha de alta: </strong><?= $fechaOriginal->format("d/m/Y")?></p> <!--Formateamos la fecha al uso horario de España -->
        </div>
    </div>
    <?php include("footer.php"); ?>
