<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "./admin/includes/crudReservas.php";
require_once "./admin/includes/crudCasas.php";
require_once "./admin/includes/crudUsuarios.php";
require_once "./admin/includes/sessions.php";

// Verificar sesión de administrador
// verificarSesionAdmin();

$reservasObj = new Reservas();
$casaObj = new Casas();
$usuariosObj = new Usuarios();

$reservas = $reservasObj->getAll();
$casas = $casaObj->getAll();
$usuarios = $usuariosObj->getAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Gestión de Reservas • VacacionalPlus Admin</title>
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
          <li class="nav-item"><a class="nav-link" href="gestion-usuarios.php">Usuarios</a></li>
          <li class="nav-item"><a class="nav-link" href="gestion-casas.php">Casas</a></li>
          <li class="nav-item"><a class="nav-link active" href="gestion-reservas.php">Reservas</a></li>
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
          <h1 class="display-5 fw-bold">Gestión de Reservas</h1>
          <p class="lead mb-3">Administra reservas, confirmaciones y cancelaciones del sistema.</p>
        </div>
        <div class="col-lg-4 text-end">
          <button class="btn btn-warning btn-lg" data-bs-toggle="modal" data-bs-target="#modalNuevaReserva">
            + Nueva Reserva
          </button>
        </div>
      </div>
    </div>
  </header>

  <!-- Contenido principal -->
  <div class="container py-4">
    <!-- Tarjetas de estadísticas -->
    <div class="row mb-4">
      <div class="col-md-3">
        <div class="card text-white bg-primary mb-3">
          <div class="card-header">Total Reservas</div>
          <div class="card-body">
            <h5 class="card-title"><?= count($reservas) ?></h5>
            <p class="card-text">En el sistema</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-white bg-success mb-3">
          <div class="card-header">Confirmadas</div>
          <div class="card-body">
            <h5 class="card-title">
              <?php
              $confirmadas = array_filter($reservas, fn($r) => isset($r['estado']) && $r['estado'] === 'confirmada');
              echo count($confirmadas);
              ?>
            </h5>
            <p class="card-text">Activas</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-white bg-warning mb-3">
          <div class="card-header">Pendientes</div>
          <div class="card-body">
            <h5 class="card-title">
              <?php
              $pendientes = array_filter($reservas, fn($r) => isset($r['estado']) && $r['estado'] === 'pendiente');
              echo count($pendientes);
              ?>
            </h5>
            <p class="card-text">Por confirmar</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-white bg-danger mb-3">
          <div class="card-header">Canceladas</div>
          <div class="card-body">
            <h5 class="card-title">
              <?php
              $canceladas = array_filter($reservas, fn($r) => isset($r['estado']) && $r['estado'] === 'cancelada');
              echo count($canceladas);
              ?>
            </h5>
            <p class="card-text">Historial</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Filtros rápidos -->
    <div class="card shadow-sm mb-4">
      <div class="card-body">
        <form class="row g-3">
          <div class="col-md-3">
            <label class="form-label small">Estado</label>
            <select class="form-select" name="estado">
              <option selected>Todos</option>
              <option value="pendiente">Pendiente</option>
              <option value="confirmada">Confirmada</option>
              <option value="cancelada">Cancelada</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label small">Fecha desde</label>
            <input type="date" class="form-control" name="fecha_desde">
          </div>
          <div class="col-md-3">
            <label class="form-label small">Fecha hasta</label>
            <input type="date" class="form-control" name="fecha_hasta">
          </div>
          <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Filtrar</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Tabla de reservas -->
    <div class="card shadow-sm">
      <div class="card-header bg-white">
        <h4 class="mb-0">Lista de Reservas</h4>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead class="table-dark">
              <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Casa</th>
                <th>Fecha Inicio</th>
                <th>Fecha Fin</th>
                <th>Huéspedes</th>
                <th>Precio Total</th>
                <th>Estado</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($reservas as $reserva) : ?>
              <tr>
                <td><?= $reserva['id_reserva'] ?></td>
                <td>
                  <?php
                  $usuario = array_filter($usuarios, fn($u) => $u['id_usuario'] == $reserva['id_usuario']);
                  $usuario = reset($usuario);
                  echo $usuario ? htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellidos']) : 'N/A';
                  ?>
                </td>
                <td>
                  <?php
                  $casa = array_filter($casas, fn($c) => $c['id_casa'] == $reserva['id_casa']);
                  $casa = reset($casa);
                  echo $casa ? htmlspecialchars($casa['nombre']) : 'N/A';
                  ?>
                </td>
                <td><?= date('d/m/Y', strtotime($reserva['fecha_inicio'])) ?></td>
                <td><?= date('d/m/Y', strtotime($reserva['fecha_fin'])) ?></td>
                <td><?= $reserva['num_huespedes'] ?> pers.</td>
                <td><?= number_format($reserva['total_precio'], 2) ?>€</td>
                <td>
                  <?php
                  $estado = $reserva['estado'];
                  $badgeClass = 'bg-secondary';
                  if ($estado === 'confirmada') $badgeClass = 'bg-success';
                  elseif ($estado === 'pendiente') $badgeClass = 'bg-warning text-dark';
                  elseif ($estado === 'cancelada') $badgeClass = 'bg-danger';
                  ?>
                  <span class="badge <?= $badgeClass ?>"><?= ucfirst($estado) ?></span>
                </td>
                <td>
                  <?php if ($estado === 'pendiente') : ?>
                    <button class="btn btn-sm btn-success" onclick="cambiarEstado(<?= $reserva['id_reserva'] ?>, 'confirmada')">
                      Confirmar
                    </button>
                  <?php endif; ?>
                  <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditarReserva<?= $reserva['id_reserva'] ?>">
                    Editar
                  </button>
                  <button class="btn btn-sm btn-outline-danger" onclick="confirmarEliminar(<?= $reserva['id_reserva'] ?>)">
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

  <!-- Modal: Nueva Reserva -->
  <div class="modal fade" id="modalNuevaReserva" tabindex="-1" aria-labelledby="modalNuevaReservaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="modalNuevaReservaLabel">Crear Nueva Reserva</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="./admin/includes/crudReservas.php">
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="id_usuario" class="form-label">Usuario/Cliente</label>
                <select class="form-select" id="id_usuario" name="id_usuario" required>
                  <option value="">Seleccionar...</option>
                  <?php foreach ($usuarios as $user) : ?>
                    <option value="<?= $user['id_usuario'] ?>"><?= htmlspecialchars($user['nombre'] . ' ' . $user['apellidos']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-6 mb-3">
                <label for="id_casa" class="form-label">Casa</label>
                <select class="form-select" id="id_casa" name="id_casa" required>
                  <option value="">Seleccionar...</option>
                  <?php foreach ($casas as $casa) : ?>
                    <option value="<?= $casa['id_casa'] ?>"><?= htmlspecialchars($casa['nombre']) ?> - <?= $casa['precio_noche'] ?>€/noche</option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-6 mb-3">
                <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="fecha_fin" class="form-label">Fecha Fin</label>
                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="num_huespedes" class="form-label">Número de Huéspedes</label>
                <input type="number" class="form-control" id="num_huespedes" name="num_huespedes" min="1" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="total_precio" class="form-label">Precio Total (€)</label>
                <input type="number" step="0.01" class="form-control" id="total_precio" name="total_precio" required>
              </div>
              <div class="col-md-12 mb-3">
                <label for="estado" class="form-label">Estado</label>
                <select class="form-select" id="estado" name="estado" required>
                  <option value="pendiente" selected>Pendiente</option>
                  <option value="confirmada">Confirmada</option>
                  <option value="cancelada">Cancelada</option>
                </select>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" name="crear" class="btn btn-primary">Crear Reserva</button>
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
    function confirmarEliminar(id) {
      if (confirm(`¿Estás seguro de eliminar esta reserva (ID: ${id})?`)) {
        window.location.href = `./admin/includes/crudReservas.php?eliminar=${id}`;
      }
    }

    function cambiarEstado(id, nuevoEstado) {
      if (confirm(`¿Confirmar esta reserva (ID: ${id})?`)) {
        window.location.href = `./admin/includes/crudReservas.php?cambiar_estado=${id}&estado=${nuevoEstado}`;
      }
    }
  </script>
</body>
</html>
