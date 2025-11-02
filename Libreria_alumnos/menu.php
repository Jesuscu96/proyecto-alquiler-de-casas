<?php
require_once "./admin/includes/crudCategorias.php";

//instanciamos
$categoriasObj = new Categorias();

//obtenemos categorias
$categorias = $categoriasObj->showCategorias();
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">
            <img src="./portadas/nuevas_portadas/pngegg.png" alt="Logo" height="40">
        </a>
        <ul class="navbar-nav me-auto">
            <li class="nav-item"><a class="nav-link" href="index.php">Inicio</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Contacto</a></li>
            <li class="nav-item"><a class="nav-link" href="login.php">Acceso privado</a></li>
        </ul>
    </div>
</nav>

<div class="container-fluid mt-4">
    <div class="row">
        <!-- Menu lateral de categorias -->
        <aside class="col-md-3">
            <h4>Categori­as</h4>
            <ul class="list-group">
            <?php foreach ($categorias as $cat) : ?>
                <li class="list-group-item">
                    <a href="index.php?cat=<?=$cat["id_categoria"]?>" class="text-decoration-none">
                        <?= $cat['categoria'] ?>
                    </a>
                </li>
            <?php endforeach; ?>        
            </ul>
        </aside>
    