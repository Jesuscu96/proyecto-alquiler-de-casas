<?php
session_start();
require_once "./admin/includes/sessions.php";
require_once "./admin/includes/crudCasas.php";
require_once "./admin/includes/crudReservas.php";

$sesion = new Sessions();
$casaObj = new Casas();
$reservasObj = new Reservas();

// Obtener ID de la casa desde URL
$id_casa = isset($_GET['id']) ? (int)$_GET['id'] : null;

if (!$id_casa) {
    header("Location: index.php");
    exit();
}

// Datos por defecto de la casa
$datos_casa = [
    'id_casa' => '',
    'nombre' => '',
    'capacidad' => '',
    'precio_noche' => '',
    'num_banos' => '',
    'num_cocinas' => '',
    'num_hab_individuales' => '',
    'num_hab_familiares' => '',
    'num_aparcamientos' => '',
    'num_lavadora' => '',
    'num_secadora' => '',
    'num_lavavajillas' => '',
    'num_horno' => '',
    'num_microondas' => '',
    'num_nevera' => '',
    'num_congelador' => '',
    'tiene_wifi' => false,
    'num_ascensores' => '',
    'tiene_calefaccion' => false,
    'tiene_aire_acondicionado' => false,
    'tiene_piscina' => false,
    'tiene_banera' => false,
    'tiene_barbacoa' => false,
    'tiene_chimenea' => false,
    'tiene_adaptacion_discapacitados' => false,
    'tiene_jardin' => false,
    'tiene_patio' => false,
    'tiene_sala_cine' => false,
    'tiene_secador_pelo' => false,
    'imagen_principal' => '',
    'id_provincia' => '',
    'id_ciudad' => '',
    'provincia' => '',
    'ciudad' => '',
];
/* $datos_imagenes = [
    'id_imagen' => '',
    'id_casa' => '',
    'url' => '',
    'descripcion' => '',
]; */

// Obtener datos de la casa por ID
$casa = $casaObj->getCasaById($id_casa);

if (!$casa) {
    header("Location: index.php");
    exit();
}

// Cargar datos en el array
$datos_casa = $casa;

// Obtener todas las imágenes de la casa desde la tabla imagenes

/* $datos_imagenes = $casaObj->getImagenesByCasa($id_casa); */
$imagenes = $casaObj->getImagenesByCasa($id_casa);

// Obtener todas las reservas de esta casa
$todasLasReservas = $reservasObj->getAll();
$reservasCasa = array_filter($todasLasReservas, function($reserva) use ($id_casa) {
    return $reserva['id_casa'] == $id_casa;
});

// Procesar formulario de reserva
$errorReserva = '';
$exitoReserva = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Verificar sesión antes de procesar la reserva
    if (!$sesion->comprobarSesion()) {
        header("Location: ../login.php");
        exit();
    }

    $fecha_inicio = isset($_POST['fecha_inicio']) ? trim($_POST['fecha_inicio']) : '';
    $fecha_fin = isset($_POST['fecha_fin']) ? trim($_POST['fecha_fin']) : '';

    $errores = [];

    if (empty($fecha_inicio)) {
        $errores[] = "La fecha de inicio no puede estar vacía.";
    }

    if (empty($fecha_fin)) {
        $errores[] = "La fecha de fin no puede estar vacía.";
    }

    if (!empty($fecha_inicio) && !empty($fecha_fin)) {
        if (strtotime($fecha_fin) <= strtotime($fecha_inicio)) {
            $errores[] = "La fecha de fin debe ser posterior a la fecha de inicio.";
        }

        // Verificar que las fechas no estén reservadas
        foreach ($reservasCasa as $reserva) {
            $res_inicio = strtotime($reserva['fecha_inicio']);
            $res_fin = strtotime($reserva['fecha_fin']);
            $sol_inicio = strtotime($fecha_inicio);
            $sol_fin = strtotime($fecha_fin);

            if (($sol_inicio < $res_fin) && ($sol_fin > $res_inicio)) {
                $errores[] = "Estas fechas no están disponibles. Por favor, elige otras fechas.";
                break;
            }
        }
    }

    if (empty($errores)) {
        try {
            $id_usuario = $_SESSION['usuario']['id_usuario'];

            // Calcular precio total
            $fecha_ini = new DateTime($fecha_inicio);
            $fecha_f = new DateTime($fecha_fin);
            $dias = $fecha_ini->diff($fecha_f)->days;
            $precio_total = $dias * $datos_casa['precio_noche'];

            // Insertar reserva
            $reservasObj->insertarReserva($id_usuario, $id_casa, $fecha_inicio, $fecha_fin, 'pendiente', $precio_total);

            $exitoReserva = "¡Reserva creada exitosamente! Está en estado pendiente.";

            // Recargar las reservas
            $todasLasReservas = $reservasObj->getAll();
            $reservasCasa = array_filter($todasLasReservas, function($reserva) use ($id_casa) {
                return $reserva['id_casa'] == $id_casa;
            });
        } catch (Exception $e) {
            $errorReserva = "Error al crear la reserva: " . $e->getMessage();
        }
    } else {
        $errorReserva = implode("<br>", $errores);
    }
}

// Imagen principal para el hero
$imagenPrincipal = htmlspecialchars($datos_casa['imagen_principal'] ?? './imagenes/default.jpg');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($datos_casa['nombre']); ?> - Alquiler de Casas</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="./css/casa.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-house-heart"></i> ApartaHome
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="index.php">Volver al catálogo</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <div class="container container-main">
        <a href="index.php" class="btn-volver">
            <i class="bi bi-arrow-left"></i> Volver al catálogo
        </a>

        <!-- Información de la Casa -->
        <div class="casa-header">
            <img src="<?= $imagenPrincipal; ?>" alt="<?= htmlspecialchars($datos_casa['nombre']); ?>" class="casa-imagen">

            <div class="casa-info">
                <h1 class="casa-titulo"><?= htmlspecialchars($datos_casa['nombre']); ?></h1>

                <div>
                    <span class="ubicacion-badge">
                        <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($datos_casa['ciudad']) . " - " . htmlspecialchars($datos_casa['provincia']); ?>
                    </span>
                </div>

                <!-- Características principales -->
                <div class="caracteristicas-grid">
                    <div class="caracteristica-card">
                        <i class="bi bi-people"></i>
                        <p><?= htmlspecialchars($datos_casa['capacidad']); ?> Personas</p>
                    </div>
                    <div class="caracteristica-card">
                        <i class="bi bi-door-closed"></i>
                        <p><?= htmlspecialchars($datos_casa['num_hab_individuales'] + $datos_casa['num_hab_familiares']); ?> Habitaciones</p>
                    </div>
                    <div class="caracteristica-card">
                        <i class="bi bi-droplet"></i>
                        <p><?= htmlspecialchars($datos_casa['num_banos']); ?> Baños</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Columna izquierda: Descripción, servicios y galería -->
            <div class="col-lg-8">
                <!-- Galería de Imágenes -->
                <?php if (!empty($imagenes)): ?>
                <div class="galeria-fotos">
                    <h3><i class="bi bi-images"></i> Galería de Fotos</h3>
                    <div class="galeria-grid">
                        <?php foreach ($imagenes as $imagen): ?>
                        <div class="galeria-item">
                            <img src="<?= $imagen['url']; ?>" 
                                 alt="<?= $imagen['descripcion'] ?? 'Imagen de la casa'; ?>"
                                 class="img-fluid">
                            <?php if (!empty($imagen['descripcion'])): ?>
                            <p class="galeria-descripcion"><?= $imagen['descripcion']; ?></p>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Detalles de la Casa -->
                <div class="descripcion">
                    <h3><i class="bi bi-info-circle"></i> Detalles de la Casa</h3>
                    <div class="detalles-grid">
                        <div class="detalle-item">
                            <strong>Capacidad:</strong> <?= htmlspecialchars($datos_casa['capacidad']); ?> personas
                        </div>

                        <?php if (!empty($datos_casa['num_hab_individuales']) && $datos_casa['num_hab_individuales'] > 0): ?>
                        <div class="detalle-item">
                            <strong>Habitaciones Individuales:</strong> <?= htmlspecialchars($datos_casa['num_hab_individuales']); ?>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($datos_casa['num_hab_familiares']) && $datos_casa['num_hab_familiares'] > 0): ?>
                        <div class="detalle-item">
                            <strong>Habitaciones Familiares:</strong> <?= htmlspecialchars($datos_casa['num_hab_familiares']); ?>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($datos_casa['num_banos']) && $datos_casa['num_banos'] > 0): ?>
                        <div class="detalle-item">
                            <strong>Baños:</strong> <?= htmlspecialchars($datos_casa['num_banos']); ?>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($datos_casa['num_cocinas']) && $datos_casa['num_cocinas'] > 0): ?>
                        <div class="detalle-item">
                            <strong>Cocinas:</strong> <?= htmlspecialchars($datos_casa['num_cocinas']); ?>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($datos_casa['num_aparcamientos']) && $datos_casa['num_aparcamientos'] > 0): ?>
                        <div class="detalle-item">
                            <strong>Aparcamientos:</strong> <?= htmlspecialchars($datos_casa['num_aparcamientos']); ?>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($datos_casa['num_ascensores']) && $datos_casa['num_ascensores'] > 0): ?>
                        <div class="detalle-item">
                            <strong>Ascensores:</strong> <?= htmlspecialchars($datos_casa['num_ascensores']); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Electrodomésticos -->
                <div class="descripcion">
                    <h3><i class="bi bi-lightning-fill"></i> Electrodomésticos</h3>
                    <div class="electrodomesticos-grid">
                        <?php if (!empty($datos_casa['num_lavadora']) && $datos_casa['num_lavadora'] > 0): ?>
                        <div class="detalle-item">
                            <strong>Lavadoras:</strong> <?= htmlspecialchars($datos_casa['num_lavadora']); ?>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($datos_casa['num_secadora']) && $datos_casa['num_secadora'] > 0): ?>
                        <div class="detalle-item">
                            <strong>Secadoras:</strong> <?= htmlspecialchars($datos_casa['num_secadora']); ?>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($datos_casa['num_lavavajillas']) && $datos_casa['num_lavavajillas'] > 0): ?>
                        <div class="detalle-item">
                            <strong>Lavavajillas:</strong> <?= htmlspecialchars($datos_casa['num_lavavajillas']); ?>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($datos_casa['num_horno']) && $datos_casa['num_horno'] > 0): ?>
                        <div class="detalle-item">
                            <strong>Hornos:</strong> <?= htmlspecialchars($datos_casa['num_horno']); ?>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($datos_casa['num_microondas']) && $datos_casa['num_microondas'] > 0): ?>
                        <div class="detalle-item">
                            <strong>Microondas:</strong> <?= htmlspecialchars($datos_casa['num_microondas']); ?>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($datos_casa['num_nevera']) && $datos_casa['num_nevera'] > 0): ?>
                        <div class="detalle-item">
                            <strong>Neveras:</strong> <?= htmlspecialchars($datos_casa['num_nevera']); ?>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($datos_casa['num_congelador']) && $datos_casa['num_congelador'] > 0): ?>
                        <div class="detalle-item">
                            <strong>Congeladores:</strong> <?= htmlspecialchars($datos_casa['num_congelador']); ?>
                        </div>
                        <?php endif; ?>

                        <?php if ($datos_casa['tiene_secador_pelo']): ?>
                        <div class="detalle-item">
                            <strong>Secadores de pelo:</strong> Sí
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Servicios y características -->
                <div class="descripcion">
                    <h3><i class="bi bi-star"></i> Servicios y Características</h3>
                    <div class="servicios-lista">
                        <?php
                        $servicios = array(
                            'tiene_wifi' => array('icon' => 'bi-wifi', 'text' => 'WiFi'),
                            'tiene_piscina' => array('icon' => 'bi-water', 'text' => 'Piscina'),
                            'tiene_jardin' => array('icon' => 'bi-flower1', 'text' => 'Jardín'),
                            'tiene_patio' => array('icon' => 'bi-tree', 'text' => 'Patio'),
                            'tiene_calefaccion' => array('icon' => 'bi-fire', 'text' => 'Calefacción'),
                            'tiene_aire_acondicionado' => array('icon' => 'bi-snow', 'text' => 'Aire AC'),
                            'tiene_barbacoa' => array('icon' => 'bi-fire', 'text' => 'Barbacoa'),
                            'tiene_chimenea' => array('icon' => 'bi-fire', 'text' => 'Chimenea'),
                            'tiene_banera' => array('icon' => 'bi-droplet', 'text' => 'Bañera'),
                            'tiene_sala_cine' => array('icon' => 'bi-film', 'text' => 'Sala de Cine'),
                            'tiene_adaptacion_discapacitados' => array('icon' => 'bi-wheelchair', 'text' => 'Adaptado PMR')
                        );

                        foreach ($servicios as $campo => $datos) {
                            if ($datos_casa[$campo]) {
                                echo '<div class="servicio-item">';
                                echo '<i class="bi ' . htmlspecialchars($datos['icon']) . '"></i>';
                                echo '<span>' . htmlspecialchars($datos['text']) . '</span>';
                                echo '</div>';
                            }
                        }
                        ?>
                    </div>
                </div>

                <!-- Fechas reservadas -->
                <?php if (!empty($reservasCasa)): ?>
                <div class="reservas-disponibles">
                    <h4><i class="bi bi-calendar-x"></i> Fechas ya reservadas</h4>
                    <?php
                    foreach ($reservasCasa as $reserva) {
                        $fecha_inicio = date('d/m/Y', strtotime($reserva['fecha_inicio']));
                        $fecha_fin = date('d/m/Y', strtotime($reserva['fecha_fin']));
                        echo '<span class="fecha-reservada">' . $fecha_inicio . ' - ' . $fecha_fin . '</span>';
                    }
                    ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Columna derecha: Formulario de reserva y precio -->
            <div class="col-lg-4">
                <!-- Precio -->
                <div class="precio-section">
                    <div class="precio-noche"><?php echo htmlspecialchars($datos_casa['precio_noche']); ?>€</div>
                    <p class="precio-texto">por noche</p>
                </div>

                <!-- Formulario de reserva -->
                <div class="formulario-reserva">
                    <h3><i class="bi bi-calendar-check"></i> Reservar Casa</h3>

                    <?php if ($errorReserva): ?>
                    <div class="alerta alerta-error">
                        <?php echo $errorReserva; ?>
                    </div>
                    <?php endif; ?>

                    <?php if ($exitoReserva): ?>
                    <div class="alerta alerta-exito">
                        <?php echo $exitoReserva; ?>
                    </div>
                    <?php endif; ?>

                    <?php if (!$sesion->comprobarSesion()): ?>
                    <div class="info-sesion">
                        <i class="bi bi-info-circle"></i> Debes iniciar sesión para reservar esta casa.
                        <br><br>
                        <a href="../login.php" class="btn btn-sm btn-primary">
                            Ir al Login
                        </a>
                    </div>
                    <?php else: ?>
                    <form method="POST">
                        <div class="form-group">
                            <label for="fecha_inicio" class="form-label">Fecha de inicio</label>
                            <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="fecha_fin" class="form-label">Fecha de fin</label>
                            <input type="date" id="fecha_fin" name="fecha_fin" class="form-control" required>
                        </div>

                        <button type="submit" class="btn-reservar">
                            <i class="bi bi-check-circle"></i> Confirmar Reserva
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
