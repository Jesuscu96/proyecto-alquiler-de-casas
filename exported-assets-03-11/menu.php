<?php
session_start();

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit();
}

$usuario = $_SESSION['usuario'];
$rol = $usuario['rol'] ?? 'cliente';
$nombre_usuario = $usuario['nombre'] ?? 'Usuario';
$username = $usuario['username'] ?? 'Usuario';
?>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="./index.php">
            <i class="bi bi-house-door-fill"></i> CasasApp Admin
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="./index.php">
                        <i class="bi bi-graph-up"></i> Dashboard
                    </a>
                </li>

                <?php if ($rol === 'SuperAdmin' || $rol === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="./usuarios.php">
                            <i class="bi bi-people-fill"></i> Usuarios
                        </a>
                    </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a class="nav-link" href="./casas2.php">
                        <i class="bi bi-houses"></i> Casas
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="./reservas.php">
                        <i class="bi bi-calendar-check"></i> Reservas
                    </a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($nombre_usuario); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="./perfil.php">
                                <i class="bi bi-person-check"></i> Mi Perfil
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="../includes/logout.php">
                                <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>