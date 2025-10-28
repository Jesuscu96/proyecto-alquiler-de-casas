<?php
// session_start();
// $id_casa = $_GET['id'];
// $casa = obtenerCasaPorId($id_casa);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Realizar Reserva PHP - VacacionalPlus</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.js" rel="stylesheet">
</head>
<body>
  <div class="container mt-4">
    <h2>Completa tu Reserva (Versión PHP)</h2>
    <form method="POST" action="procesar_reserva.php">
      <input type="hidden" name="id_casa" value="<?php echo $casa['id_casa']; ?>">
      <input type="hidden" name="id_usuario" value="<?php echo $_SESSION['usuario']['id_usuario']; ?>">

      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="fecha_inicio">Entrada</label>
          <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" required>
        </div>
        <div class="col-md-6 mb-3">
          <label for="fecha_fin">Salida</label>
          <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" required>
        </div>
      </div>

      <div class="mb-3">
        <label for="num_huespedes">Huéspedes</label>
        <select name="num_huespedes" id="num_huespedes" class="form-select" required>
          <?php for($i = 1; $i <= $casa['capacidad']; $i++): ?>
          <option value="<?php echo $i; ?>"><?php echo $i; ?> huésped<?php echo $i > 1 ? 'es' : ''; ?></option>
          <?php endfor; ?>
        </select>
      </div>

      <button type="submit" class="btn btn-primary">Confirmar Reserva</button>
    </form>
    <p class="mt-3">NOTA: Ver code_file:315 para versión con resumen sticky y cálculo JS.</p>
  </div>
</body>
</html>
