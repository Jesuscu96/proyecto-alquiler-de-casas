<?php
require_once "./includes/crudCasas.php";
require_once "./includes/crudUbicacion.php";
require_once "./includes/crudUsuarios.php";
require_once "./includes/sessions.php";

// Validar sesión administrador aquí si quieres

$casaObj = new Casas();
$ubicacionObj = new Ubicacion();
$usuariosObj = new Usuarios();

$casas = $casaObj->getAll();
$comunidades = $ubicacionObj->getAllComunidades();
$provincias = $ubicacionObj->getAllProvincias();
$ciudades = $ubicacionObj->getAllCiudades();
$propietarios = $usuariosObj->getAll();

$accion = $_GET['accion'] ?? null;
$id = $_GET['id'] ?? null;

$errores = [];
// Inicializamos datos por defecto
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
    'num_nevera' => 0,
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
    'imagen_principal' => null
];

// Si es editar, cargamos datos
if ($accion === "editar" && $id) {
    $datosExistentes = $casaObj->getCasaById($id);
    if ($datosExistentes) {
        $datos_casa = array_merge($datos_casa, $datosExistentes);
    }
}

// Asignar variables desde POST o mantener valores actuales
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_propietario = $_POST['id_propietario'] ?? $datos_casa['id_propietario'];
    $id_comunidad = $_POST['id_comunidad'] ?? $datos_casa['id_comunidad'];
    $id_provincia = $_POST['id_provincia'] ?? $datos_casa['id_provincia'];
    $id_ciudad = $_POST['id_ciudad'] ?? $datos_casa['id_ciudad'];
    $nombre = trim($_POST['nombre'] ?? $datos_casa['nombre']);
    $capacidad = (int)($_POST['capacidad'] ?? $datos_casa['capacidad']);
    $precio_noche = (float)($_POST['precio_noche'] ?? $datos_casa['precio_noche']);
    $num_banos = (int)($_POST['num_banos'] ?? $datos_casa['num_banos']);
    $num_cocinas = (int)($_POST['num_cocinas'] ?? $datos_casa['num_cocinas']);
    $num_hab_individuales = (int)($_POST['num_hab_individuales'] ?? $datos_casa['num_hab_individuales']);
    $num_hab_familiares = (int)($_POST['num_hab_familiares'] ?? $datos_casa['num_hab_familiares']);
    $num_aparcamientos = (int)($_POST['num_aparcamientos'] ?? $datos_casa['num_aparcamientos']);
    $num_lavadora = (int)($_POST['num_lavadora'] ?? $datos_casa['num_lavadora']);
    $num_secadora = (int)($_POST['num_secadora'] ?? $datos_casa['num_secadora']);
    $num_lavavajillas = (int)($_POST['num_lavavajillas'] ?? $datos_casa['num_lavavajillas']);
    $num_horno = (int)($_POST['num_horno'] ?? $datos_casa['num_horno']);
    $num_microondas = (int)($_POST['num_microondas'] ?? $datos_casa['num_microondas']);
    $num_nevera = (int)($_POST['num_nevera'] ?? $datos_casa['num_nevera']);
    $num_congelador = (int)($_POST['num_congelador'] ?? $datos_casa['num_congelador']);
    $tiene_wifi = isset($_POST['tiene_wifi']) ? 1 : $datos_casa['tiene_wifi'];
    $num_ascensores = (int)($_POST['num_ascensores'] ?? $datos_casa['num_ascensores']);
    $tiene_calefaccion = isset($_POST['tiene_calefaccion']) ? 1 : $datos_casa['tiene_calefaccion'];
    $tiene_aire_acondicionado = isset($_POST['tiene_aire_acondicionado']) ? 1 : $datos_casa['tiene_aire_acondicionado'];
    $tiene_piscina = isset($_POST['tiene_piscina']) ? 1 : $datos_casa['tiene_piscina'];
    $tiene_banera = isset($_POST['tiene_banera']) ? 1 : $datos_casa['tiene_banera'];
    $tiene_barbacoa = isset($_POST['tiene_barbacoa']) ? 1 : $datos_casa['tiene_barbacoa'];
    $tiene_chimenea = isset($_POST['tiene_chimenea']) ? 1 : $datos_casa['tiene_chimenea'];
    $tiene_adaptacion_discapacitados = isset($_POST['tiene_adaptacion_discapacitados']) ? 1 : $datos_casa['tiene_adaptacion_discapacitados'];
    $tiene_jardin = isset($_POST['tiene_jardin']) ? 1 : $datos_casa['tiene_jardin'];
    $tiene_patio = isset($_POST['tiene_patio']) ? 1 : $datos_casa['tiene_patio'];
    $tiene_sala_cine = isset($_POST['tiene_sala_cine']) ? 1 : $datos_casa['tiene_sala_cine'];
    $tiene_secador_pelo = isset($_POST['tiene_secador_pelo']) ? 1 : $datos_casa['tiene_secador_pelo'];
    $imagen_principal = $_POST['imagen_principal'] ?? $datos_casa['imagen_principal'];

    // Validaciones
    $errores = [];
    if (empty($nombre)) {
        $errores['nombre'] = "El nombre no puede estar vacío.";
    }
    if (!(
        $num_cocinas > 0 ||
        $num_banos >= 1 ||
        $num_hab_individuales > 0 ||
        $num_hab_familiares > 0
    )) {
        $errores['combo_habitaciones'] = "Debe tener al menos una cocina, baño o habitación.";
    }
    if ($num_banos < 1) {
        $errores['num_banos'] = "Debe tener al menos un baño.";
    }
    if ($num_nevera < 1) {
        $errores['num_nevera'] = "Debe tener al menos una nevera.";
    }


    if (empty($errores)) {
        if ($accion === 'crear') {
            $casaObj->insertarCasa(
                $id_propietario, $id_comunidad, $id_provincia, $id_ciudad,
                $nombre, $capacidad, $precio_noche,
                $num_banos, $num_cocinas, $num_hab_individuales, $num_hab_familiares,
                $num_aparcamientos, $num_lavadora, $num_secadora, $num_lavavajillas,
                $num_horno, $num_microondas, $num_nevera, $num_congelador,
                $tiene_wifi, $num_ascensores, $tiene_calefaccion, $tiene_aire_acondicionado,
                $tiene_piscina, $tiene_banera, $tiene_barbacoa, $tiene_chimenea,
                $tiene_adaptacion_discapacitados, $tiene_jardin, $tiene_patio, $tiene_sala_cine,
                $tiene_secador_pelo, $imagen_principal
            );
        } elseif ($accion === 'editar' && $id) {
            $casaObj->actualizarCasa(
                $id,
                $id_propietario, $id_comunidad, $id_provincia, $id_ciudad,
                $nombre, $capacidad, $precio_noche,
                $num_banos, $num_cocinas, $num_hab_individuales, $num_hab_familiares,
                $num_aparcamientos, $num_lavadora, $num_secadora, $num_lavavajillas,
                $num_horno, $num_microondas, $num_nevera, $num_congelador,
                $tiene_wifi, $num_ascensores, $tiene_calefaccion, $tiene_aire_acondicionado,
                $tiene_piscina, $tiene_banera, $tiene_barbacoa, $tiene_chimenea,
                $tiene_adaptacion_discapacitados, $tiene_jardin, $tiene_patio, $tiene_sala_cine,
                $tiene_secador_pelo, $imagen_principal
            );
        }
        header("Location: casas.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <title><?= $accion === 'crear' ? 'Crear Casa Vacacional' : 'Editar Casa Vacacional' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body class="p-4">

    <h1><?= $accion === 'crear' ? 'Crear Casa Vacacional' : 'Editar Casa Vacacional' ?></h1>

    <form method="POST" enctype="multipart/form-data" novalidate>

        <div class="mb-3">
            <label for="id_propietario" class="form-label">Propietario *</label>
            <input type="text" id="id_propietario" name="id_propietario" class="form-control" value="<?= htmlspecialchars($id_propietario ?? '') ?>" required>
            <?php if (isset($errores['id_propietario'])) : ?>
                <small class="text-danger"><?= $errores['id_propietario'] ?></small>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="id_comunidad" class="form-label">Comunidad Autónoma *</label>
            <input type="text" id="id_comunidad" name="id_comunidad" class="form-control" value="<?= htmlspecialchars($id_comunidad ?? '') ?>" required>
            <?php if (isset($errores['id_comunidad'])) : ?>
                <small class="text-danger"><?= $errores['id_comunidad'] ?></small>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="id_provincia" class="form-label">Provincia *</label>
            <input type="text" id="id_provincia" name="id_provincia" class="form-control" value="<?= htmlspecialchars($id_provincia ?? '') ?>" required>
            <?php if (isset($errores['id_provincia'])) : ?>
                <small class="text-danger"><?= $errores['id_provincia'] ?></small>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="id_ciudad" class="form-label">Ciudad *</label>
            <input type="text" id="id_ciudad" name="id_ciudad" class="form-control" value="<?= htmlspecialchars($id_ciudad ?? '') ?>" required>
            <?php if (isset($errores['id_ciudad'])) : ?>
                <small class="text-danger"><?= $errores['id_ciudad'] ?></small>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre *</label>
            <input type="text" id="nombre" name="nombre" class="form-control" value="<?= htmlspecialchars($nombre ?? '') ?>" required>
            <?php if (isset($errores['nombre'])) : ?>
                <small class="text-danger"><?= $errores['nombre'] ?></small>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="capacidad" class="form-label">Capacidad *</label>
            <input type="number" id="capacidad" name="capacidad" min="1" class="form-control" value="<?= htmlspecialchars($capacidad ?? '') ?>" required>
            <?php if (isset($errores['capacidad'])) : ?>
                <small class="text-danger"><?= $errores['capacidad'] ?></small>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="precio_noche" class="form-label">Precio noche (€) *</label>
            <input type="number" id="precio_noche" name="precio_noche" min="0" step="0.01" class="form-control" value="<?= htmlspecialchars($precio_noche ?? '') ?>" required>
            <?php if (isset($errores['precio_noche'])) : ?>
                <small class="text-danger"><?= $errores['precio_noche'] ?></small>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="num_banos" class="form-label">Número de baños *</label>
            <input type="number" id="num_banos" name="num_banos" min="1" class="form-control" value="<?= htmlspecialchars($num_banos ?? '') ?>" required>
            <?php if (isset($errores['num_banos'])) : ?>
                <small class="text-danger"><?= $errores['num_banos'] ?></small>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="num_cocinas" class="form-label">Número de cocinas</label>
            <input type="number" id="num_cocinas" name="num_cocinas" min="0" class="form-control" value="<?= htmlspecialchars($num_cocinas ?? '') ?>">
        </div>

        <div class="mb-3">
            <label for="num_hab_individuales" class="form-label">Número de habitaciones individuales</label>
            <input type="number" id="num_hab_individuales" name="num_hab_individuales" min="0" class="form-control" value="<?= htmlspecialchars($num_hab_individuales ?? '') ?>">
        </div>

        <div class="mb-3">
            <label for="num_hab_familiares" class="form-label">Número de habitaciones familiares</label>
            <input type="number" id="num_hab_familiares" name="num_hab_familiares" min="0" class="form-control" value="<?= htmlspecialchars($num_hab_familiares ?? '') ?>">
            <?php if (isset($errores['combo_habitaciones'])) : ?>
                <small class="text-danger"><?= $errores['combo_habitaciones'] ?></small>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="num_nevera" class="form-label">Número de neveras *</label>
            <input type="number" id="num_nevera" name="num_nevera" min="1" class="form-control" value="<?= htmlspecialchars($num_nevera ?? '') ?>" required>
            <?php if (isset($errores['num_nevera'])) : ?>
                <small class="text-danger"><?= $errores['num_nevera'] ?></small>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary"><?= $accion === 'crear' ? 'Crear' : 'Actualizar' ?></button>
        <a href="casas.php" class="btn btn-secondary ms-2">Cancelar</a>
</form>
</body>
</html>
