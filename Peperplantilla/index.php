<?php
$page_title = "Explorar Casas";
// include 'header.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Index PHP - VacacionalPlus</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container mt-4">
    <h1>Buscar Casas (Versión Dinámica)</h1>

    <!-- NOTA: Este es un resumen. Ver code_file:312 para versión completa -->

    <div class="row">
      <?php
      // $casas = obtenerCasas();
      // foreach($casas as $casa):
      ?>
      <!-- BLOQUE A REPETIR -->
      <div class="col-md-4">
        <div class="card mb-3">
          <div class="card-body">
            <h5><?php echo $casa['titulo']; ?></h5>
            <p><?php echo $casa['ciudad']; ?>, <?php echo $casa['provincia']; ?></p>
            <span><?php echo $casa['precio_noche']; ?>€/noche</span>
          </div>
        </div>
      </div>
      <?php // endforeach; ?>
    </div>
  </div>
</body>
</html>
<?php // include 'footer.php'; ?>
