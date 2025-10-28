<?php
// session_start();
// if(!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 'admin') exit;
// $usuarios = obtenerUsuarios();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestión Usuarios PHP - VacacionalPlus</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container mt-4">
    <h2>Gestión de Usuarios (Versión PHP Dinámica)</h2>
    <table class="table table-striped">
      <thead>
        <tr>
          <th>ID</th><th>Nombre</th><th>Email</th><th>Rol</th><th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($usuarios as $user): ?>
        <!-- BLOQUE A REPETIR -->
        <tr>
          <td><?php echo $user['id_usuario']; ?></td>
          <td><?php echo $user['nombre']; ?></td>
          <td><?php echo $user['email']; ?></td>
          <td><span class="badge bg-info"><?php echo $user['rol']; ?></span></td>
          <td>
            <a href="editar_usuario.php?id=<?php echo $user['id_usuario']; ?>" class="btn btn-sm btn-primary">Editar</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <p class="mt-3">NOTA: Ver code_file:316 para versión con estadísticas, filtros y paginación.</p>
  </div>
</body>
</html>
