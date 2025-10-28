<?php
// session_start();
// if(!isset($_SESSION['usuario'])) header('Location: login.php');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Agregar Casa PHP - VacacionalPlus</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container mt-4">
    <h2>Publicar Propiedad (Versión PHP Dinámica)</h2>
    <form method="POST" action="procesar_casa.php" enctype="multipart/form-data">
      <input type="hidden" name="id_usuario" value="<?php echo $_SESSION['usuario']['id_usuario']; ?>">

      <div class="mb-3">
        <label for="titulo">Nombre de la Propiedad</label>
        <input type="text" name="titulo" id="titulo" class="form-control" required>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="id_comunidad">Comunidad</label>
          <select name="id_comunidad" id="id_comunidad" class="form-select" required>
            <option value="">Selecciona...</option>
            <?php
            // $comunidades = obtenerComunidades();
            // foreach($comunidades as $com):
            ?>
            <!-- <option value="<?php echo $com['id_comunidad']; ?>"><?php echo $com['nombre']; ?></option> -->
            <?php // endforeach; ?>
          </select>
        </div>
        <div class="col-md-6 mb-3">
          <label for="precio_noche">Precio/noche (€)</label>
          <input type="number" name="precio_noche" id="precio_noche" class="form-control" required>
        </div>
      </div>

      <button type="submit" class="btn btn-success">Publicar</button>
    </form>
    <p class="mt-3">NOTA: Ver code_file:314 para wizard completo de 5 pasos.</p>
  </div>
</body>
</html>
