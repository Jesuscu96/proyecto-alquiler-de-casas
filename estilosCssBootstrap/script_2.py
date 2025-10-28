
# Crear el archivo gestion-usuarios.php
gestion_usuarios = """<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "./admin/includes/crudUsuarios.php";
require_once "./admin/includes/sessions.php";

// Verificar sesión de administrador
// verificarSesionAdmin();

$usuariosObj = new Usuarios();
$usuarios = $usuariosObj->getAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Gestión de Usuarios • VacacionalPlus Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="./admin/assets/css/styles.css">
</head>
<body class="bg-light">
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
      <a class="navbar-brand" href="admin.php">Vacacional<span class="brand-highlight">Plus</span> Admin</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="mainNav">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link" href="admin.php">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link active" href="gestion-usuarios.php">Usuarios</a></li>
          <li class="nav-item"><a class="nav-link" href="gestion-casas.php">Casas</a></li>
          <li class="nav-item"><a class="nav-link" href="gestion-reservas.php">Reservas</a></li>
          <li class="nav-item"><a class="nav-link" href="logout.php">Cerrar Sesión</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Hero / Header -->
  <header class="hero">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-8">
          <h1 class="display-5 fw-bold">Gestión de Usuarios</h1>
          <p class="lead mb-3">Administra propietarios, clientes y administradores del sistema.</p>
        </div>
        <div class="col-lg-4 text-end">
          <button class="btn btn-warning btn-lg" data-bs-toggle="modal" data-bs-target="#modalNuevoUsuario">
            + Nuevo Usuario
          </button>
        </div>
      </div>
    </div>
  </header>

  <!-- Contenido principal -->
  <div class="container py-4">
    <!-- Tarjetas de estadísticas -->
    <div class="row mb-4">
      <div class="col-md-4">
        <div class="card text-white bg-primary mb-3">
          <div class="card-header">Total Usuarios</div>
          <div class="card-body">
            <h5 class="card-title"><?= count($usuarios) ?></h5>
            <p class="card-text">Registrados en el sistema</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card text-white bg-success mb-3">
          <div class="card-header">Propietarios</div>
          <div class="card-body">
            <h5 class="card-title">
              <?php
              $propietarios = array_filter($usuarios, fn($u) => $u['rol'] === 'cliente');
              echo count($propietarios);
              ?>
            </h5>
            <p class="card-text">Con casas publicadas</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card text-white bg-info mb-3">
          <div class="card-header">Administradores</div>
          <div class="card-body">
            <h5 class="card-title">
              <?php
              $admins = array_filter($usuarios, fn($u) => $u['rol'] === 'admin');
              echo count($admins);
              ?>
            </h5>
            <p class="card-text">Con acceso al panel</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Tabla de usuarios -->
    <div class="card shadow-sm">
      <div class="card-header bg-white">
        <h4 class="mb-0">Lista de Usuarios</h4>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead class="table-dark">
              <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Nombre Completo</th>
                <th>Email</th>
                <th>Edad</th>
                <th>Teléfono</th>
                <th>Rol</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($usuarios as $usuario) : ?>
              <tr>
                <td><?= $usuario['id_usuario'] ?></td>
                <td><?= htmlspecialchars($usuario['username']) ?></td>
                <td><?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellidos']) ?></td>
                <td><?= htmlspecialchars($usuario['email']) ?></td>
                <td><?= $usuario['edad'] ?></td>
                <td><?= htmlspecialchars($usuario['telefono']) ?></td>
                <td>
                  <?php if ($usuario['rol'] === 'admin') : ?>
                    <span class="badge bg-danger">Admin</span>
                  <?php else : ?>
                    <span class="badge bg-primary">Cliente</span>
                  <?php endif; ?>
                </td>
                <td>
                  <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditarUsuario<?= $usuario['id_usuario'] ?>">
                    Editar
                  </button>
                  <button class="btn btn-sm btn-outline-danger" onclick="confirmarEliminar(<?= $usuario['id_usuario'] ?>, '<?= htmlspecialchars($usuario['username']) ?>')">
                    Eliminar
                  </button>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal: Nuevo Usuario -->
  <div class="modal fade" id="modalNuevoUsuario" tabindex="-1" aria-labelledby="modalNuevoUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="modalNuevoUsuarioLabel">Crear Nuevo Usuario</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="./admin/includes/crudUsuarios.php">
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="apellidos" class="form-label">Apellidos</label>
                <input type="text" class="form-control" id="apellidos" name="apellidos" required>
              </div>
              <div class="col-md-4 mb-3">
                <label for="edad" class="form-label">Edad</label>
                <input type="number" class="form-control" id="edad" name="edad" required>
              </div>
              <div class="col-md-8 mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="telefono" class="form-label">Teléfono</label>
                <input type="tel" class="form-control" id="telefono" name="telefono" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="rol" class="form-label">Rol</label>
                <select class="form-select" id="rol" name="rol" required>
                  <option value="cliente">Cliente</option>
                  <option value="admin">Administrador</option>
                </select>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" name="crear" class="btn btn-primary">Crear Usuario</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="site-footer py-4 mt-5">
    <div class="container">
      <div class="row">
        <div class="col-md-6">
          <p class="footer-brand mb-2">VacacionalPlus Admin</p>
          <p class="footer-note">Panel de administración para gestión de alquileres vacacionales.</p>
        </div>
        <div class="col-md-6 text-md-end">
          <p class="footer-note mb-1">&copy; 2025 VacacionalPlus. Todos los derechos reservados.</p>
        </div>
      </div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function confirmarEliminar(id, username) {
      if (confirm(`¿Estás seguro de eliminar al usuario "${username}"?`)) {
        window.location.href = `./admin/includes/crudUsuarios.php?eliminar=${id}`;
      }
    }
  </script>
</body>
</html>
"""

# Guardar el archivo
with open('gestion-usuarios.php', 'w', encoding='utf-8') as f:
    f.write(gestion_usuarios)

print("✓ Archivo gestion-usuarios.php creado exitosamente")
