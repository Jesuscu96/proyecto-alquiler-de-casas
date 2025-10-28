<?php 
require_once "./admin/includes/crudLibros.php";
$libroObj = new Libros();

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Libreria Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include("menu.php"); ?>

    <main class="col-md-9">
       <?php
        if (isset($_GET['cat'])) {
            $libros = $libroObj->getByCategoria($_GET['cat']);
            echo "<h3>Libros de la categorias seleccionada</h3><div class='row'>";
        } else {
            $librosPop = $libroObj->getPopulares();
            $librosNew = $libroObj->getRecientes();
            echo "<h3>Libros mas populares</h3><div class='row'>";
            $libros = $librosPop;
        }?>
            <?php foreach ($libros as $libro) : ?>
            <div class="col-md-3 text-center mb-4">
                <a href="libro.php?id=<?=$libro["id_libro"]?>">
                    <img src="portadas/<?= $libro['portada']?>" class="img-fluid" alt="<?= $libro['titulo']?>">
                </a>
                <p><?= $libro['titulo']?></p>
            </div>
            <?php endforeach; ?>

        </div>

        <?php if (!isset($_GET['cat'])): ?>
        <h3>Novedades</h3>
            <div class="row">
                <?php foreach ($librosNew as $libro) : ?>
                <div class="col-md-3 text-center mb-4">
                    <a href="libro.php?id=<?=$libro["id_libro"]?>">
                        <img src="portadas/<?= $libro['portada']?>" class="img-fluid">
                    </a>
                    <p><?= $libro['titulo']?></p>
                </div>
                <?php endforeach?>
            </div>
            
            
        </div>
        <?php 
        endif;
        include("footer.php"); 
        ?>