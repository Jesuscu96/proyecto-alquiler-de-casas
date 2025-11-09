
<?php
require_once "./admin/includes/crudCasas.php";
require_once "./admin/includes/crudUbicacion.php";
require_once "./admin/includes/crudUsuarios.php";
require_once "./admin/includes/sessions.php";
$sesion = new Sessions();
if (!$sesion->comprobarSesion()) {
    header("Location: ./login.php");
    exit();
}

$casaObj = new Casas();
$ubicacionObj = new Ubicacion();
$usuariosObj = new Usuarios();

// Obtener datos
$id_usuario = $_SESSION['usuario']['id_usuario'];

$todasLasCasas = $casaObj->getCasasByIdUsuario($id_usuario);
$comunidades = $ubicacionObj->getAllComunidades();
$provincias = $ubicacionObj->getAllProvincias();
$ciudades = $ubicacionObj->getAllCiudades();


// Parámetros de acción
$accion = $_GET['accion'] ?? null;
$id = $_GET['id'] ?? null;
$id_comunidad = $_GET['id_comunidad'] ?? null;
$id_provincia = $_GET['id_provincia'] ?? null;
$id_ciudad = $_GET['id_ciudad'] ?? null;

// Calcular estadísticas

$total_casas = $casaObj->getCantidadCasasByUsuario($id_usuario);

// Paginación
$pagina = (int)($_GET['pagina'] ?? 1);
$por_pagina = 6;
$total_paginas = ceil($total_casas / $por_pagina);
$inicio = ($pagina - 1) * $por_pagina;
$casas_pagina = array_slice($todasLasCasas, $inicio, $por_pagina);

// Datos por defecto del formulario
$datos_casa = [
    'id_propietario' => '',
    'id_comunidad' => '',
    'id_provincia' => '',
    'id_ciudad' => '',
    'nombre' => '',
    'capacidad' => 1,
    'precio_noche' => 0,
    'num_banos' => 1,
    'num_cocinas' => 1,
    'num_hab_individuales' => 0,
    'num_hab_familiares' => 0,
    'num_aparcamientos' => 0,
    'num_lavadora' => 0,
    'num_secadora' => 0,
    'num_lavavajillas' => 0,
    'num_horno' => 0,
    'num_microondas' => 0,
    'num_nevera' => 1,
    'num_congelador' => 0,
    'tiene_wifi' => 0,
    'num_ascensores' => 0,
    'tiene_calefaccion' => 0,
    'tiene_aire_acondicionado' => 0,
    'tiene_piscina' => 0,
    'tiene_banera' => 0,
    'tiene_barbacoa' => 0,
    'tiene_chimenea' => 0,
    'tiene_adaptacion_discapacitados' => 0,
    'tiene_jardin' => 0,
    'tiene_patio' => 0,
    'tiene_sala_cine' => 0,
    'tiene_secador_pelo' => 0,
    'imagen_principal' => '',
    'provincia' => '',
    'ciudad' => ''
];

// Si es editar, cargar datos
if ($accion === "editar" && $id) {
    $datos_casa = $casaObj->getCasaById($id);
}
if ($accion === 'info' && $id) {
    $datos_casa = $casaObj->getCasaById($id);
}


// Procesar eliminación
if ($accion === 'eliminar' && $id) {
    $datos_casa = $casaObj->getCasaById($id);
    $imagenes = $casaObj->getImagenesByCasa($id);
    //imagen principal
    if($datos_casa) {
        $rutaBorrado = "./imagenes/" . $datos_casa['imagen_principal'];
        if (file_exists($rutaBorrado)) {
            unlink($rutaBorrado);
        }
    }

    $rutaBorrado = "./imagenes/" . $datos_casa['imagen_principal'];
    if (file_exists($rutaBorrado)) {
        unlink($rutaBorrado);
    }
    // El bloque de imagenes de la casa
    if ($imagenes) {
        foreach ($imagenes as $imagen) {
            $rutaImagen = "../imagenes/" . $imagen['url']; // Ajusta según tu estructura
            if (file_exists($rutaImagen)) {
                unlink($rutaImagen);
            }
        }
    }
    $casaObj->eliminarImagenes($id);
    $casaObj->eliminarCasa($id);
    header("Location: anadircasa.php");
    exit();
}

if (isset($_GET['id_comunidad'])) {
    $id_comunidad = (int)$_GET['id_comunidad'];
}
if (isset($_GET['id_provincia'])) {
    $id_provincia = (int)$_GET['id_comunidad'];
}



// Procesar formulario POST
$errores = [];
$erroresId_propietario = '';
$erroresId_comunidad = '';
$erroresId_provincia = '';
$erroresId_ciudad = '';
$erroresNombre = '';
$erroresCapacidad = '';
$erroresPrecio = '';
$erroresNum_banos = '';
$erroresNum_cocinas = '';
$erroresNum_nevera = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_propietario = $id_usuario;
    $id_comunidad = $_POST['id_comunidad'] ?? ($_GET['id_comunidad'] ?? '');
    $id_provincia = $_POST['id_provincia'] ?? ($_GET['id_provincia'] ?? '');
    $id_ciudad = $_POST['id_ciudad'] ?? ($_GET['id_ciudad'] ?? '');
    $nombre = trim($_POST['nombre'] ?? '');
    $capacidad = (int)($_POST['capacidad'] ?? 1);
    $precio_noche = (float)($_POST['precio_noche'] ?? 0);
    $num_banos = (int)($_POST['num_banos'] ?? 1);
    $num_cocinas = (int)($_POST['num_cocinas'] ?? 1);
    $num_hab_individuales = (int)($_POST['num_hab_individuales'] ?? 0);
    $num_hab_familiares = (int)($_POST['num_hab_familiares'] ?? 0);
    $num_aparcamientos = (int)($_POST['num_aparcamientos'] ?? 0);
    $num_lavadora = (int)($_POST['num_lavadora'] ?? 0);
    $num_secadora = (int)($_POST['num_secadora'] ?? 0);
    $num_lavavajillas = (int)($_POST['num_lavavajillas'] ?? 0);
    $num_horno = (int)($_POST['num_horno'] ?? 0);
    $num_microondas = (int)($_POST['num_microondas'] ?? 0);
    $num_nevera = (int)($_POST['num_nevera'] ?? 1);
    $num_congelador = (int)($_POST['num_congelador'] ?? 0);
    $tiene_wifi = isset($_POST['tiene_wifi']) ? 1 : 0;
    $num_ascensores = (int)($_POST['num_ascensores'] ?? 0);
    $tiene_calefaccion = isset($_POST['tiene_calefaccion']) ? 1 : 0;
    $tiene_aire_acondicionado = isset($_POST['tiene_aire_acondicionado']) ? 1 : 0;
    $tiene_piscina = isset($_POST['tiene_piscina']) ? 1 : 0;
    $tiene_banera = isset($_POST['tiene_banera']) ? 1 : 0;
    $tiene_barbacoa = isset($_POST['tiene_barbacoa']) ? 1 : 0;
    $tiene_chimenea = isset($_POST['tiene_chimenea']) ? 1 : 0;
    $tiene_adaptacion_discapacitados = isset($_POST['tiene_adaptacion_discapacitados']) ? 1 : 0;
    $tiene_jardin = isset($_POST['tiene_jardin']) ? 1 : 0;
    $tiene_patio = isset($_POST['tiene_patio']) ? 1 : 0;
    $tiene_sala_cine = isset($_POST['tiene_sala_cine']) ? 1 : 0;
    $tiene_secador_pelo = isset($_POST['tiene_secador_pelo']) ? 1 : 0;
    
    // Manejo de imagen principal
    $imagen_guardada = $datos_casa['imagen_principal'] ?? '';
    if (!empty($_FILES['imagen_principal']['name'])) {
        $carpeta = './imagenes/';
        if (!is_dir($carpeta)) mkdir($carpeta, 0755, true);
        $nombreArchivo = basename($_FILES['imagen_principal']['name']);
        $rutaArchivo = $carpeta . $nombreArchivo;
        if (move_uploaded_file($_FILES['imagen_principal']['tmp_name'], $rutaArchivo)) {
            $imagen_guardada = $nombreArchivo;
        } else {
            $errores['imagen'] = "Error al subir la imagen.";
        }
    }
    



    // Validaciones
    if ($accion === 'crear' || $accion === 'editar') {

        if (empty($id_propietario)) $erroresId_propietario = "Selecciona un propietario.";
        if (empty($id_comunidad)) $erroresId_comunidad = "Selecciona una comunidad.";
        if (empty($id_provincia)) $erroresId_provincia = "Selecciona una provincia.";
        if (empty($id_ciudad)) $erroresId_ciudad = "Selecciona una ciudad.";
        if (empty($nombre)) $erroresNombre = "El nombre no puede estar vacío.";
        if ($capacidad < 1) $erroresCapacidad = "La capacidad debe ser al menos 1.";
        if ($precio_noche < 0) $erroresPrecio = "El precio no puede ser negativo.";
        if ($num_banos < 1) $erroresNum_banos = "Debe tener al menos 1 baño.";
        if ($num_cocinas < 1) $erroresNum_cocinas = "Debe tener al menos 1 cocina.";
        if ($num_nevera < 1) $erroresNum_nevera = "Debe tener al menos 1 nevera.";

    }
    if (!empty($erroresId_propietario)) $errores[] = $erroresId_propietario;
    if (!empty($erroresId_comunidad)) $errores[] = $erroresId_comunidad;
    if (!empty($erroresId_provincia)) $errores[] = $erroresId_provincia;
    if (!empty($erroresId_ciudad)) $errores[] = $erroresId_ciudad;
    if (!empty($erroresNombre)) $errores[] = $erroresNombre;
    if (!empty($erroresCapacidad)) $errores[] = $erroresCapacidad;
    if (!empty($erroresPrecio)) $errores[] = $erroresPrecio;
    if (!empty($erroresNum_banos)) $errores[] = $erroresNum_banos;
    if (!empty($erroresNum_cocinas)) $errores[] = $erroresNum_cocinas;
    if (!empty($erroresNum_nevera)) $errores[] = $erroresNum_nevera;

    // Guardar si no hay errores
    if (empty($errores)) {
        try {
            if ($accion === 'crear') {
                $id_casa = $casaObj->insertarCasa(
                    $id_propietario, $id_comunidad, $id_provincia, $id_ciudad,
                    $nombre, $capacidad, $precio_noche, $num_banos, $num_cocinas,
                    $num_hab_individuales, $num_hab_familiares, $num_aparcamientos,
                    $num_lavadora, $num_secadora, $num_lavavajillas, $num_horno,
                    $num_microondas, $num_nevera, $num_congelador, $tiene_wifi,
                    $num_ascensores, $tiene_calefaccion, $tiene_aire_acondicionado,
                    $tiene_piscina, $tiene_banera, $tiene_barbacoa, $tiene_chimenea,
                    $tiene_adaptacion_discapacitados, $tiene_jardin, $tiene_patio,
                    $tiene_sala_cine, $tiene_secador_pelo, $imagen_guardada
                );
            } elseif ($accion === 'editar' && $id) {
                $casaObj->actualizarCasa(
                    $id, $id_propietario, $id_comunidad, $id_provincia, $id_ciudad,
                    $nombre, $capacidad, $precio_noche, $num_banos, $num_cocinas,
                    $num_hab_individuales, $num_hab_familiares, $num_aparcamientos,
                    $num_lavadora, $num_secadora, $num_lavavajillas, $num_horno,
                    $num_microondas, $num_nevera, $num_congelador, $tiene_wifi,
                    $num_ascensores, $tiene_calefaccion, $tiene_aire_acondicionado,
                    $tiene_piscina, $tiene_banera, $tiene_barbacoa, $tiene_chimenea,
                    $tiene_adaptacion_discapacitados, $tiene_jardin, $tiene_patio,
                    $tiene_sala_cine, $tiene_secador_pelo, $imagen_guardada
                );
            }
            if (!empty($_FILES['imagenes']['name'][0])) {
                $carpeta = './imagenes/';
                if (!is_dir($carpeta)) mkdir($carpeta, 0755, true); // file_exist pude dar errores
                // determinar id real de la casa: si creamos, $id_casa viene de insertarCasa(); si editamos, usar $id
                $targetId = ($accion === 'crear') ? ($id_casa ?? null) : ($id ?? null);

                foreach ($_FILES['imagenes']['tmp_name'] as $index => $tmpName) {
                    if ($_FILES['imagenes']['error'][$index] === UPLOAD_ERR_OK) {
                        $nombreArchivo = basename($_FILES['imagenes']['name'][$index]);
                        //$rutaArchivo = $carpeta . uniqid('galeria_') . $nombreArchivo;
                        if (move_uploaded_file($tmpName, $carpeta . $nombreArchivo)) { 
                            $casaObj->insertarImagen($targetId, $nombreArchivo);
                        }
                    }
                }
            }
            header("Location: anadircasa.php");
            exit();
        } catch (Exception $e) {
            $errores['general'] = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Casas Vacacionales</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <!-- Usar la hoja global de estilos (styles.css) para el backend -->
    <link rel="stylesheet" href="./css/styles.css" />
</head>
    <body class="admin-page">
    <!-- Navbar -->
    <?php include './menu.php'; ?>

    <div class="container-fluid mt-4">
        
        <!-- Estadísticas (siempre visibles) -->
        <div class="stats-container">
            <div class="stat-card total">
                <span class="stat-icon"><i class="bi bi-house"></i></span>
                <h3><?= $total_casas ?></h3>
                <p><i class="bi bi-house-fill"></i> Total de Casas</p>
            </div>
        </div>

        <?php if ($accion === 'crear' || $accion === 'editar'): ?>

            <!-- FORMULARIO (visible solo cuando accion=crear o editar) -->
            <div class="card shadow-lg border-0">
                <div class="card-header" style="background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%); color: white;">
                    <h4 class="mb-0">
                        <i class="bi bi-<?= $accion === 'crear' ? 'plus-circle' : 'pencil-square' ?>"></i>
                        <?= $accion === 'crear' ? 'Crear Nueva Casa' : 'Editar Casa' ?>
                    </h4>
                </div>
                <div class="card-body" style="max-height: 70vh; overflow-y: auto;">
                    <?php if (!empty($errores)): ?>
                        <div class="alert alert-danger">
                            <strong><i class="bi bi-exclamation-triangle-fill"></i> Errores encontrados:</strong>
                            <ul class="mb-0">
                                <?php foreach ($errores as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
        
        
                    <form method="POST" enctype="multipart/form-data">
        
                        <?php if (!isset($_GET['id_comunidad']) && $accion === 'crear'): ?>
                            <!-- PASO 1: Seleccionar comunidad -->
                            <h6><i class="bi bi-geo-alt-fill"></i>PASO 1 SELECCIONA COMUNIDAD </h6>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Comunidad Autónoma *</label>
                                    <select name="id_comunidad" class="form-select" onchange="location.href='?accion=<?= $accion ?>&id_comunidad='+this.value;">
                                        <option value="">Seleccionar...</option>
                                        <?php foreach ($comunidades as $com): ?>
                                            <option value="<?= $com['id_comunidad'] ?>"> 
                                                <?= htmlspecialchars($com['nombre']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <p>"Selecciona una comunidad y se mostrara el siguinte paso para crear la casa"</p>
                            
                            <div class="d-flex justify-content-between mt-4">
                                <a href="anadircasa.php" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Cancelar
                                </a>
                            </div>
                        <?php elseif (isset($_GET['id_comunidad']) && !isset($_GET['id_provincia']) && $accion === 'crear'): ?>
                            <!-- PASO 2: Seleccionar provincia según la comunidad -->
                            <?php 
                                $id_comunidad = (int)$_GET['id_comunidad']; 
                                $provincias = $ubicacionObj->getProvinciasByComunidad($id_comunidad); 
                            ?>
                            <h6><i class="bi bi-geo-alt-fill"></i>PASO 2 SELECCIONA PROVINCIA</h6>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Provincia *</label>
                                    <select name="id_provincia" class="form-select" onchange="location.href='?accion=<?= $accion ?>&id_comunidad=<?= $id_comunidad ?>&id_provincia='+this.value;">
                                        <option value="">Seleccionar...</option>
                                        <?php foreach ($provincias as $prov): ?>
                                            <option value="<?= $prov['id_provincia'] ?>">
                                                <?= htmlspecialchars($prov['nombre']) ?>
                                            </option>
                                        <?php endforeach; ?> 
                                    </select>
                                </div>
                            </div>
                            <p>"Selecciona una Provincia y se mostrara el siguinte paso para crear la casa"</p>
                            <div class="d-flex justify-content-between mt-4">
                                <a href="anadircasa.php" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Cancelar
                                </a>
                            </div>

                        

                        <?php else: ?>
                            <!-- PASO 3: Seleccionar ciudad según la provincia -->
                            
                            <?php 
                                if(isset($_GET['id_provincia'])) {
                                    $id_comunidad = (int)$_GET['id_comunidad'];
                                    $id_provincia = (int)$_GET['id_provincia'];
                                    $ciudades = $ubicacionObj->getCiudadesByProvincia($id_provincia);?>
                            
                                    <input type="hidden" name="id_comunidad" value="<?= $id_comunidad ?>">
                                    <input type="hidden" name="id_provincia" value="<?= $id_provincia ?>">
                        <?php   } else {
                                    $ciudades = $ubicacionObj->getCiudadesByProvincia($datos_casa['id_provincia']);?>
                                    <input type="hidden" name="id_comunidad" value="<?= $datos_casa['id_comunidad']?>">
                                    <input type="hidden" name="id_provincia" value="<?= $datos_casa['id_provincia']?>">  
                        <?php   } ?>  
                                 

                            

                            
                            <h6><i class="bi bi-card-text"></i>PASO 3 COMPLETA EL RESTO DEL FORMULARIO</h6></br>
                            <h6><i class="bi bi-geo-alt-fill"></i> SELECCIONA CIUDAD</h6>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Ciudad *</label>
                                    <select name="id_ciudad" class="form-select">
                                        <option value="" <?= empty($datos_casa['id_ciudad']) ? 'selected' : '' ?> disabled hidden>Seleccionar...</option>
                                        <?php foreach ($ciudades as $ciudad): ?>
                                            <option value="<?= $ciudad['id_ciudad']?>" <?= isset($datos_casa['id_ciudad']) && $datos_casa['id_ciudad'] == $ciudad['id_ciudad'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($ciudad['nombre']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                           
                            <!-- PASO 4: Mostrar formulario completo -->
        
                            <h6><i class="bi bi-info-circle-fill"></i> INFORMACIÓN BÁSICA</h6>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nombre de la Casa *</label>
                                    <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($datos_casa['nombre']) ?>">
                                </div>
                            </div>

                            

                        

                            <!-- CAPACIDAD Y PRECIO -->
                            <h6><i class="bi bi-cash-stack"></i> CAPACIDAD Y PRECIO</h6>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Capacidad (personas) *</label>
                                    <input type="number" name="capacidad" class="form-control" min="1" value="<?= $datos_casa['capacidad'] ?>">
                                    <?php if (!empty($erroresCapacidad)): ?>
                                        <div class="text-danger small mt-1"><?= $erroresCapacidad ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Precio por Noche (€) *</label>
                                    <input type="number" name="precio_noche" class="form-control" step="0.01" min="0" value="<?= $datos_casa['precio_noche'] ?>">
                                    <?php if (!empty($erroresCapacidad)): ?>
                                        <div class="text-danger small mt-1"><?= htmlspecialchars($erroresNum_banos) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- HABITACIONES Y BAÑOS -->
                            <h6><i class="bi bi-door-open-fill"></i> HABITACIONES Y BAÑOS</h6>
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label class="form-label">Baños *</label>
                                    <input type="number" name="num_banos" class="form-control" min="1" value="<?= $datos_casa['num_banos'] ?>">
                                    <?php if (!empty($erroresNum_banos)): ?>
                                        <div class="text-danger small mt-1"><?= $erroresNum_banos ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Cocinas *</label>
                                    <input type="number" name="num_cocinas" class="form-control" min="1" value="<?= $datos_casa['num_cocinas'] ?>" >
                                    <?php if (!empty($erroresNum_cocinas)): ?>
                                        <div class="text-danger small mt-1"><?= $erroresNum_cocinas ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Hab. Individuales</label>
                                    <input type="number" name="num_hab_individuales" class="form-control" min="0" value="<?= $datos_casa['num_hab_individuales'] ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Hab. Familiares</label>
                                    <input type="number" name="num_hab_familiares" class="form-control" min="0" value="<?= $datos_casa['num_hab_familiares'] ?>">
                                </div>
                            </div>

                            <!-- ELECTRODOMÉSTICOS -->
                            <h6><i class="bi bi-plug-fill"></i> ELECTRODOMÉSTICOS</h6>
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label class="form-label">Lavadoras</label>
                                    <input type="number" name="num_lavadora" class="form-control" min="0" value="<?= $datos_casa['num_lavadora'] ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Secadoras</label>
                                    <input type="number" name="num_secadora" class="form-control" min="0" value="<?= $datos_casa['num_secadora'] ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Lavavajillas</label>
                                    <input type="number" name="num_lavavajillas" class="form-control" min="0" value="<?= $datos_casa['num_lavavajillas'] ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Hornos</label>
                                    <input type="number" name="num_horno" class="form-control" min="0" value="<?= $datos_casa['num_horno'] ?>">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label class="form-label">Microondas</label>
                                    <input type="number" name="num_microondas" class="form-control" min="0" value="<?= $datos_casa['num_microondas'] ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Neveras *</label>
                                    <input type="number" name="num_nevera" class="form-control" min="1" value="<?= $datos_casa['num_nevera'] ?>">
                                    <?php if (!empty($erroresNum_nevera)): ?>
                                        <div class="text-danger small mt-1"><?= $erroresNum_nevera ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Congeladores</label>
                                    <input type="number" name="num_congelador" class="form-control" min="0" value="<?= $datos_casa['num_congelador'] ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Aparcamientos</label>
                                    <input type="number" name="num_aparcamientos" class="form-control" min="0" value="<?= $datos_casa['num_aparcamientos'] ?>">
                                </div>
                            </div>

                            <!-- AMENIDADES -->
                            <h6><i class="bi bi-stars"></i> AMENIDADES Y CARACTERÍSTICAS</h6>
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="tiene_wifi" class="form-check-input" id="wifi" <?= $datos_casa['tiene_wifi'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="wifi">Wi-Fi</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="tiene_calefaccion" class="form-check-input" id="calefaccion" <?= $datos_casa['tiene_calefaccion'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="calefaccion">Calefacción</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="tiene_aire_acondicionado" class="form-check-input" id="aire" <?= $datos_casa['tiene_aire_acondicionado'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="aire">Aire Acondicionado</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="tiene_piscina" class="form-check-input" id="piscina" <?= $datos_casa['tiene_piscina'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="piscina">Piscina</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="tiene_banera" class="form-check-input" id="banera" <?= $datos_casa['tiene_banera'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="banera">Bañera</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="tiene_barbacoa" class="form-check-input" id="barbacoa" <?= $datos_casa['tiene_barbacoa'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="barbacoa">Barbacoa</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="tiene_chimenea" class="form-check-input" id="chimenea" <?= $datos_casa['tiene_chimenea'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="chimenea">Chimenea</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="tiene_adaptacion_discapacitados" class="form-check-input" id="adaptacion" <?= $datos_casa['tiene_adaptacion_discapacitados'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="adaptacion">Adaptación Discapacitados</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="tiene_jardin" class="form-check-input" id="jardin" <?= $datos_casa['tiene_jardin'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="jardin">Jardín</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="tiene_patio" class="form-check-input" id="patio" <?= $datos_casa['tiene_patio'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="patio">Patio</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="tiene_sala_cine" class="form-check-input" id="cine" <?= $datos_casa['tiene_sala_cine'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="cine">Sala de Cine</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="tiene_secador_pelo" class="form-check-input" id="secador" <?= $datos_casa['tiene_secador_pelo'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="secador">Secador de Pelo</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Ascensores</label>
                                    <input type="number" name="num_ascensores" class="form-control" min="0" value="<?= $datos_casa['num_ascensores'] ?>">
                                </div>
                            </div>

                            <!-- IMAGEN -->
                            <h6><i class="bi bi-image-fill"></i> IMAGEN PRINCIPAL</h6>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label class="form-label">Imagen Principal "portada"</label>
                                    <input type="file" name="imagen_principal" class="form-control" accept="image/*" >
                                    <?php if (!empty($datos_casa['imagen_principal'])): ?>
                                        <small class="text-muted">Imagen actual: <?= htmlspecialchars($datos_casa['imagen_principal']) ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label class="form-label">Resto de imagenes  "seleciona con el control para multiples imagenes"* </label>
                                    <input type="file" name="imagenes[]" class="form-control" accept="image/*" multiple>
                                    
                                </div>
                            </div>

                            <!-- BOTONES -->
                            <div class="d-flex justify-content-between mt-4">
                                <a href="anadircasa.php?pagina=<?= $pagina ?>" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save-fill"></i> <?= $accion === 'crear' ? 'Crear Casa' : 'Actualizar Casa' ?>
                                </button>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        <?php elseif ($accion === 'info' && !empty($datos_casa)): ?>
            <div class="card shadow-lg border-0 mt-4">
            <div class="card-header bg-info text-white">
                <h4 class="mb-0">
                <i class="bi bi-info-circle-fill"></i> Información de la Casa
                </h4>
            </div>

            <div class="card-body">
                <h5><i class="bi bi-house"></i> <?= htmlspecialchars($datos_casa['nombre']) ?></h5>
                <p><i class="bi bi-geo-alt-fill"></i><?= htmlspecialchars($datos_casa['provincia']) ?>, <?= htmlspecialchars($datos_casa['ciudad']) ?> </p>
                <p><i class="bi bi-people"></i> Capacidad: <?= $datos_casa['capacidad'] ?> personas</p>
                <p><i class="bi bi-cash-stack"></i> Precio por noche: <?= $datos_casa['precio_noche'] ?> €</p>

                <div class="amenities-grid mt-4">
                    <div class="amenity <?= !$datos_casa['num_hab_individuales'] ? 'disabled' : '' ?>">
                        <i class="bi bi-person"></i> Hab. Individuales <?= $datos_casa['num_hab_individuales'] ? htmlspecialchars($datos_casa['num_hab_individuales']) : '✗' ?>
                    </div>
                    <div class="amenity <?= !$datos_casa['num_hab_familiares'] ? 'disabled' : '' ?>">
                        <i class="bi bi-people-fill"></i> Hab. Familiares <?= $datos_casa['num_hab_familiares'] ? htmlspecialchars($datos_casa['num_hab_familiares']) : '✗' ?>
                    </div>
                    <div class="amenity <?= !$datos_casa['tiene_wifi'] ? 'disabled' : '' ?>"><i class="bi bi-wifi"></i> WiFi <?= $datos_casa['tiene_wifi'] ? '✓' : '✗' ?></div>
                    <div class="amenity <?= !$datos_casa['tiene_piscina'] ? 'disabled' : '' ?>"><i class="bi bi-water"></i> Piscina <?= $datos_casa['tiene_piscina'] ? '✓' : '✗' ?></div>
                    <div class="amenity <?= !$datos_casa['tiene_aire_acondicionado'] ? 'disabled' : '' ?>"><i class="bi bi-thermometer-snow"></i> Aire Acondicionado <?= $datos_casa['tiene_aire_acondicionado'] ? '✓' : '✗' ?></div>
                    <div class="amenity <?= !$datos_casa['tiene_calefaccion'] ? 'disabled' : '' ?>"><i class="bi bi-thermometer-sun"></i> Calefacción <?= $datos_casa['tiene_calefaccion'] ? '✓' : '✗' ?></div>
                    <div class="amenity <?= !$datos_casa['tiene_barbacoa'] ? 'disabled' : '' ?>"><i class="bi bi-fork-knife"></i> Barbacoa <?= $datos_casa['tiene_barbacoa'] ? '✓' : '✗' ?></div>
                    <div class="amenity <?= !$datos_casa['tiene_chimenea'] ? 'disabled' : '' ?>"><i class="bi bi-fire"></i> Chimenea <?= $datos_casa['tiene_chimenea'] ? '✓' : '✗' ?></div>
                    <div class="amenity <?= !$datos_casa['tiene_jardin'] ? 'disabled' : '' ?>"><i class="bi bi-tree-fill"></i> Jardín <?= $datos_casa['tiene_jardin'] ? '✓' : '✗' ?></div>
                    <div class="amenity <?= !$datos_casa['tiene_patio'] ? 'disabled' : '' ?>"><i class="bi bi-house-door"></i> Patio <?= $datos_casa['tiene_patio'] ? '✓' : '✗' ?></div>
                    <div class="amenity <?= !$datos_casa['tiene_sala_cine'] ? 'disabled' : '' ?>"><i class="bi bi-film"></i> Sala de Cine <?= $datos_casa['tiene_sala_cine'] ? '✓' : '✗' ?></div>
                    <div class="amenity <?= !$datos_casa['tiene_adaptacion_discapacitados'] ? 'disabled' : '' ?>"><i class="bi bi-person-wheelchair"></i> Adaptada <?= $datos_casa['tiene_adaptacion_discapacitados'] ? '✓' : '✗' ?></div>
                    <div class="amenity <?= !$datos_casa['tiene_secador_pelo'] ? 'disabled' : '' ?>"><i class="bi bi-wind"></i> Secador Pelo <?= $datos_casa['tiene_secador_pelo'] ? '✓' : '✗' ?></div>
                    <div class="amenity <?= !$datos_casa['num_cocinas'] ? 'disabled' : '' ?>"><i class="bi bi-fork-knife"></i> Cocinas <?= $datos_casa['num_cocinas'] ?></div>
                    <div class="amenity <?= !$datos_casa['num_microondas'] ? 'disabled' : '' ?>"><i class="bi bi-lightning"></i> Microondas <?= $datos_casa['num_microondas'] ?></div>
                    <div class="amenity <?= !$datos_casa['num_horno'] ? 'disabled' : '' ?>"><i class="bi bi-oven"></i> Hornos <?= $datos_casa['num_horno'] ?></div>
                    <div class="amenity <?= !$datos_casa['num_nevera'] ? 'disabled' : '' ?>"><i class="bi bi-snow"></i> Neveras <?= $datos_casa['num_nevera'] ?></div>
                    <div class="amenity <?= !$datos_casa['num_congelador'] ? 'disabled' : '' ?>"><i class="bi bi-snow2"></i> Congeladores <?= $datos_casa['num_congelador'] ?></div>
                    <div class="amenity <?= !$datos_casa['num_lavavajillas'] ? 'disabled' : '' ?>"><i class="bi bi-droplet"></i> Lavavajillas <?= $datos_casa['num_lavavajillas'] ?></div>
                    <div class="amenity <?= !$datos_casa['num_lavadora'] ? 'disabled' : '' ?>"><i class="bi bi-droplet-half"></i> Lavadoras <?= $datos_casa['num_lavadora'] ?></div>
                    <div class="amenity <?= !$datos_casa['num_secadora'] ? 'disabled' : '' ?>"><i class="bi bi-fan"></i> Secadoras <?= $datos_casa['num_secadora'] ?></div>
                    <div class="amenity <?= !$datos_casa['tiene_banera'] ? 'disabled' : '' ?>"><i class="bi bi-hdmi-fill"></i> Bañera <?= $datos_casa['tiene_banera'] ? '✓' : '✗' ?></div>
                    <div class="amenity <?= !$datos_casa['num_aparcamientos'] ? 'disabled' : '' ?>"><i class="bi bi-car-front"></i> Plazas de coche <?= $datos_casa['num_aparcamientos'] ?></div>
                    <div class="amenity <?= !$datos_casa['num_ascensores'] ? 'disabled' : '' ?>"><i class="bi bi-arrow-down-up"></i> Ascensores <?= $datos_casa['num_ascensores'] ?></div>
                </div>

                <div class="mt-4 text-end">
                    <a href="anadircasa.php?pagina=<?= $pagina ?>" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Cerrar información
                    </a>
                </div>
            </div>
            </div>
            

        
        <?php else: ?>
            <!-- Botón Añadir -->
            <div class="mb-3">
                <a href="?accion=crear" class="btn btn-primary btn-lg">
                    <i class="bi bi-plus-circle-fill"></i> Añadir Nueva Casa
                </a>
            </div>
            <!-- Tabla -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Imagen</th>
                            <th>Nombre</th>
                            <th>Ubicación</th>
                            <th>Precio/Noche</th>
                            <th>Capacidad</th>
                            <th>Adaptación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                            <?php foreach ($casas_pagina as $casa): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($casa['imagen_principal'])): ?>
                                            <img src="./imagenes/<?= htmlspecialchars($casa['imagen_principal']) ?>" alt="Casa" width="50" height="50" style="object-fit: cover;">
                                        <?php else: ?>
                                            <span class="text-muted">Sin imagen</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($casa['nombre']) ?></td>
                                    <td><?= htmlspecialchars($casa['ciudad'] ?? 'N/A') ?></td>
                                    <td><strong>€<?= number_format($casa['precio_noche'], 2) ?></strong></td>
                                    <td><?= $casa['capacidad'] ?> pers.</td>
                                    <td>
                                        <?php if ($casa['precio_noche'] >= 1000 && $casa['tiene_adaptacion_discapacitados']): ?>
                                            <span class="badge badge-vip"><i class="bi bi-gem"></i> VIP Premium Accesible</span>
                                        <?php elseif ($casa['tiene_adaptacion_discapacitados']): ?>
                                            <span class="badge badge-accesible"><i class="bi bi-person-wheelchair"></i> Accesible</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Estándar</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="?accion=editar&id=<?= $casa['id_casa'] ?>&pagina=<?= $pagina ?>" class="btn btn-warning btn-action">
                                            <i class="bi bi-pencil-square"></i> Editar
                                        </a>
                                        <a href="?accion=info&id=<?= $casa['id_casa'] ?>&pagina=<?= $pagina ?>" class="btn btn-info btn-action">
                                            <i class="bi bi-info-circle"></i> Más info
                                        </a>
                                        <a href="?accion=eliminar&id=<?= $casa['id_casa'] ?>&pagina=<?= $pagina ?>" 
                                            class="btn btn-danger btn-action"
                                            onclick="return confirm('¿Estás seguro de que deseas eliminar esta casa? Esta acción no se puede deshacer.');">
                                            <i class="bi bi-trash-fill"></i> Eliminar
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <?php if ($total_paginas > 1): ?>
                <nav>
                    <ul class="pagination">
                        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                            <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
                                <a class="page-link" href="?pagina=<?= $i ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>

        <?php endif; ?>

    </div>

    <?php include './footer.php'; ?>
       
</body>
</html>