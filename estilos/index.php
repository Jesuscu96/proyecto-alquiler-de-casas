<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "./admin/includes/crudCasas.php";
require_once "./admin/includes/crudUbicacion.php";

$casaObj = new Casas();
$ubicacionObj = new Ubicacion();

$comunidad = $ubicacionObj->getAllComunidades();
$provincia = $ubicacionObj->getAllProvincias();
$ciudad = $ubicacionObj->getAllCiudades();

$casas = $casaObj->getAll();
$casasVip = $casaObj->getCasasVip();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Alquiler Casas Vacacionales • Catálogo</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"  />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="./css/styles.css" />
</head>
<body class="bg-light">
  <?php include("menu.php"); ?>

  <!-- Hero / encabezado -->
  <header class="hero" >
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-7">
          <h1 class="display-5 fw-bold">Encuentra la casa vacacional perfecta</h1>
          <p class="lead mb-3 hero-text">
            Busca por provincia, ciudad, capacidad y servicios. Resultados reales y disponibilidad en tiempo real (demo).
          </p>
        </div>
        <div class="col-lg-5">
          <div class="card filters-card shadow-sm" style="border-radius: 12px;">
            <div class="card-body">
              <form class="filters">
                <div class="row g-2">
                  <div class="col-12 col-md-6">
                    <label class="form-label small fw-600">Provincia</label>
                    <select class="form-select" aria-label="Provincia">
                      <option selected>Seleccionar</option>
                      <option>Málaga</option>
                      <option>Cádiz</option>
                      <option>Barcelona</option>
                      <option>Valencia</option>
                    </select>
                  </div>
                  <div class="col-12 col-md-6">
                    <label class="form-label small fw-600">Ciudad</label>
                    <select class="form-select" aria-label="Ciudad">
                      <option selected>Seleccionar</option>
                      <option>Marbella</option>
                      <option>Tarifa</option>
                      <option>Barcelona</option>
                      <option>Valencia</option>
                    </select>
                  </div>
                  <div class="col-6 col-md-4">
                    <label class="form-label small fw-600">Capacidad</label>
                    <select class="form-select" aria-label="Capacidad">
                      <option selected>--</option>
                      <option>2</option>
                      <option>4</option>
                      <option>6</option>
                      <option>8+</option>
                    </select>
                  </div>
                  <div class="col-6 col-md-4">
                    <label class="form-label small fw-600">Precio noche</label>
                    <select class="form-select" aria-label="Precio">
                      <option selected>--</option>
                      <option>&lt; 80€</option>
                      <option>80€ - 140€</option>
                      <option>&gt; 140€</option>
                    </select>
                  </div>
                  <div class="col-12 col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100 fw-600">Buscar</button>
                  </div>
                </div>
                <div class="mt-3 small text-muted">Filtros básicos — esta maqueta no realiza búsqueda real.</div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>

  <div class="container py-5">
    <h2 class="mb-4 text-center fw-bold section-title">Catálogo de Casas Vacacionales</h2>

    <!-- Grupo 1: Viviendas VIP -->
    <section class="section-group mb-5" role="region" aria-labelledby="vip-title">
      <h2 id="vip-title" class="mb-4 text-center fw-bold section-subtitle"><i class="bi bi-gem"></i> Viviendas VIP</h2>
      <div class="row row-cols-1 row-cols-md-3 g-4 mb-5">
        <?php foreach ($casasVip as $casa) : ?>
          <div class="col">
            <div class="card h-100 shadow-sm card-vip">
              <div class="image-container">
                <img src="./imagenes/<?= htmlspecialchars($casa['imagen_principal']) ?>" class="card-img-top" alt="<?= htmlspecialchars($casa['nombre']) ?>">
                <span class="badge bg-warning text-dark vip-badge"><i class="bi bi-gem"></i> VIP</span>
              </div>
              <div class="card-body">
                <h5 class="card-title fw-bold" style="color: #4f46e5;"><?= htmlspecialchars($casa['nombre']) ?></h5>
                <p class="card-text mb-1"><strong><i class="bi bi-geo"></i> Provincia:</strong> <?= htmlspecialchars($casa['provincia']) ?></p>
                <p class="card-text mb-1"><strong><i class="bi bi-geo-alt"></i> Ciudad:</strong> <?= htmlspecialchars($casa['ciudad']) ?></p>
                <p class="card-text mb-1"><i class="bi bi-person-standing"></i> Capacidad: <strong><?= $casa['capacidad'] ?></strong> pers. · <span class="text-success fw-600">✓ Disponible</span></p>
                <p class="card-text mb-2 amenities"><i class="bi bi-wifi"></i> Wifi · <i class="bi bi-water"></i> Piscina · <i class="bi bi-tree-fill"></i> Jardín · <i class="bi bi-car-front"></i> Parking</p>
                <p class="card-text mb-3">
                  <strong class="card-price card-price-vip"><?= number_format($casa['precio_noche'], 2, ',', '.') ?>€</strong>
                  <span class="price-suffix">/noche</span>
                </p>
                <a href="casa.php?id=<?= $casa['id_casa']; ?>" class="btn btn-primary w-100 fw-600">Ver detalles →</a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- Grupo 2: Todas las casas -->
    <section class="section-group" role="region" aria-labelledby="todas-title">
      <h2 id="todas-title" class="mb-4 text-center fw-bold" style="color: #4f46e5; font-size: 1.8rem;"><i class="bi bi-house"></i> Todas las Viviendas</h2>
      <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php foreach ($casas as $casa) : ?>
          <div class="col">
            <div class="card h-100 shadow-sm card-regular">
              <div class="image-container">
                <img src="./imagenes/<?= htmlspecialchars($casa['imagen_principal']) ?>" class="card-img-top" alt="<?= htmlspecialchars($casa['nombre']) ?>">
              </div>
              <div class="card-body">
                <h5 class="card-title fw-bold" style="color: #4f46e5;"><?= htmlspecialchars($casa['nombre']) ?></h5>
                <p class="card-text mb-1"><strong><i class="bi bi-geo"></i> Provincia:</strong> <?= htmlspecialchars($casa['provincia']) ?></p>
                <p class="card-text mb-1"><strong><i class="bi bi-geo-alt"></i> Ciudad:</strong> <?= htmlspecialchars($casa['ciudad']) ?></p>
                <p class="card-text mb-1"><i class="bi bi-person-standing"></i> Capacidad: <strong><?= $casa['capacidad'] ?></strong> pers. · <span class="text-success fw-600">✓ Disponible</span></p>
                <p class="card-text mb-2 amenities"><i class="bi bi-wifi"></i> Wifi · <i class="bi bi-water"></i> Piscina · <i class="bi bi-tree-fill"></i> Jardín · <i class="bi bi-car-front"></i> Parking</p>
                <p class="card-text mb-3">
                  <strong class="card-price card-price-normal"><?= number_format($casa['precio_noche'], 2, ',', '.') ?>€</strong>
                  <span class="price-suffix">/noche</span>
                </p>
                <a href="casa.php?id=<?= $casa['id_casa']; ?>" class="btn btn-primary w-100 fw-600">Ver detalles →</a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
  </div>

  <?php include("footer.php"); ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
