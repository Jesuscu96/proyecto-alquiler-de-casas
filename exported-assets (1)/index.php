<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - ApartaHome</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/styles.css">
</head>
<body>
    <?php include("menu.php"); ?>

    <div class="main-container">
        <div class="card mb-4">
            <div class="card-header">
                <h2>Bienvenido a ApartaHome</h2>
                <p class="mb-0 mt-2" style="color: rgba(255,255,255,0.9);">Encuentra los mejores apartamentos vacacionales</p>
            </div>
            <div class="card-body">
                <p>Selecciona una opción del menú para comenzar.</p>
                <?php if (isset($_SESSION['usuario'])): ?>
                    <p>¡Bienvenido, <strong><?= htmlspecialchars($_SESSION['usuario']['nombre']) ?></strong>!</p>
                <?php else: ?>
                    <p><a href="login.php" class="link-text">Inicia sesión</a> para acceder a todas las funcionalidades.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include("footer.php"); ?>
</body>
</html>
