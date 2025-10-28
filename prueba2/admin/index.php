<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once "./includes/sessions.php";
$sesion = new Sessions();

if (!$sesion->comprobarSesion()) {
    header("Location: ../login.php");
    exit();
}
$usuario = $_SESSION['usuario'];

require_once "./includes/crudCasas.php";
require_once "./includes/crudUbicacion.php";
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
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Alquiler Casas Vacacionales • Catálogo</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="./assets/css/styles.css">
</head>
<body class="bg-light">
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
      <a class="navbar-brand" href="#">Vacacional<span style="color:#FFD700;">Plus</span></a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="mainNav">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link active" href="#">Inicio</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Buscar Casas</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Favoritos</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Login</a></li>
          
        </ul>
      </div>
    </div>
  </nav>

  <!-- Hero / encabezado atractivo -->
  <header class="hero">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-7">
          <h1 class="display-5 fw-bold">Bievenido <?= $usuario['nombre']?> <?= $usuario['apellidos']?></h1>
          <p class="lead mb-3">Busca por provincia, ciudad, capacidad y servicios. Resultados reales y disponibilidad en tiempo real (demo).</p>
          
        </div>
        <div class="col-lg-5">
          <div class="card filters-card shadow-sm">
            <div class="card-body">
              <form class="filters">
                <div class="row g-2">
                  <div class="col-12 col-md-6">
                    <label class="form-label small">Provincia</label>
                    <select class="form-select" aria-label="Provincia">
                      <option selected>Seleccionar</option>
                      <option>Málaga</option>
                      <option>Cádiz</option>
                      <option>Barcelona</option>
                      <option>Valencia</option>
                    </select>
                  </div>
                  <div class="col-12 col-md-6">
                    <label class="form-label small">Ciudad</label>
                    <select class="form-select" aria-label="Ciudad">
                      <option selected>Seleccionar</option>
                      <option>Marbella</option>
                      <option>Tarifa</option>
                      <option>Barcelona</option>
                      <option>Valencia</option>
                    </select>
                  </div>
                  <div class="col-6 col-md-4">
                    <label class="form-label small">Capacidad</label>
                    <select class="form-select" aria-label="Capacidad">
                      <option selected>--</option>
                      <option>2</option>
                      <option>4</option>
                      <option>6</option>
                      <option>8+</option>
                    </select>
                  </div>
                  <div class="col-6 col-md-4">
                    <label class="form-label small">Precio noche</label>
                    <select class="form-select" aria-label="Precio">
                      <option selected>--</option>
                      <option>&lt; 80€</option>
                      <option>80€ - 140€</option>
                      <option>&gt; 140€</option>
                    </select>
                  </div>
                  <div class="col-12 col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Buscar</button>
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

  <div class="container py-3">
    
    <h2 class="mb-4 text-center">Catálogo de Casas Vacacionales</h2>

    <!-- Grupo 1: Viviendas Vip (3 tarjetas) -->
    <section class="section-group" role="region" aria-labelledby="vip-title">
      <h2 id="vip-title" class="mb-4 text-center">Viviendas Vip</h2>
      <div class="row row-cols-1 row-cols-md-3 g-4 mb-5">
      <?php foreach ($casasVip as $casa) : ?>
      <!-- Card example 1 -->
      <div class="col">
        <div class="card h-100 shadow-sm">
          <img src="./admin/imagenes/<?=$casa['imagen_principal']?>" class="card-img-top" alt="Casa en Málaga">
          <div class="card-body">
            <h5 class="card-title"><?=$casa['nombre']?></h5>
            <p class="card-text mb-0"><strong>Provincia:</strong> <?=$casa['provincia']?></p>
            <p class="card-text mb-1"><strong>Ciudad:</strong> <?=$casa['ciudad']?></p>
            <p class="card-text">Capacidad: <?=$casa['capacidad']?> pers. · <span class="text-success">Disponible</span></p>
            <p class="card-text">Wifi · Piscina · Jardín · Parking</p>
            <p class="card-text mb-2"><strong>Precio noche:</strong> <?=$casa['precio_noche']?></p>
            <a href="#" class="btn btn-primary w-100">Ver detalles</a>
          </div>
        </div>
      </div>

      <?php endforeach; ?>
      
      </div>
    </section>
    
    
    
    <!-- /////////////////////////////////////////////////////////////////// -->    
    <!-- Grupo 2: Viviendas en Valencia (otro bloque de 3 tarjetas) -->
    <section class="section-group" role="region" aria-labelledby="valencia-1-title">
      <h2 id="valencia-1-title" class="mb-4 text-center">Viviendas en Valencia</h2>
      <div class="row row-cols-1 row-cols-md-3 g-4">
      <!-- Card example 4 -->
      <div class="col">
        <div class="card h-100 shadow-sm">
          <img src="casa1.jpg" class="card-img-top" alt="Casa en Málaga">
          <div class="card-body">
            <h5 class="card-title">Casa Familiar en Málaga</h5>
            <p class="card-text mb-0"><strong>Provincia:</strong> Málaga</p>
            <p class="card-text mb-1"><strong>Ciudad:</strong> Marbella</p>
            <p class="card-text">Capacidad: 6 pers. · <span class="text-success">Disponible</span></p>
            <p class="card-text">Wifi · Piscina · Jardín · Parking</p>
            <p class="card-text mb-2"><strong>Precio noche:</strong> 120€</p>
            <a href="#" class="btn btn-primary w-100">Ver detalles</a>
          </div>
        </div>
      </div>

      <!-- Card example 5 -->
      <div class="col">
        <div class="card h-100 shadow-sm">
          <img src="casa2.jpg" class="card-img-top" alt="Casa en Cádiz">
          <div class="card-body">
            <h5 class="card-title">Villa Costa Cádiz</h5>
            <p class="card-text mb-0"><strong>Provincia:</strong> Cádiz</p>
            <p class="card-text mb-1"><strong>Ciudad:</strong> Tarifa</p>
            <p class="card-text">Capacidad: 8 pers. · <span class="text-danger">No disponible</span></p>
            <p class="card-text">Aire AC · Barbacoa · Wifi · Vistas mar</p>
            <p class="card-text mb-2"><strong>Precio noche:</strong> 150€</p>
            <a href="#" class="btn btn-secondary w-100 disabled">No disponible</a>
          </div>
        </div>
      </div>

      <!-- Card example 6 -->
      <div class="col">
        <div class="card h-100 shadow-sm">
          <img src="casa3.jpg" class="card-img-top" alt="Casa en Barcelona">
          <div class="card-body">
            <h5 class="card-title">Apartamento City Barcelona</h5>
            <p class="card-text mb-0"><strong>Provincia:</strong> Barcelona</p>
            <p class="card-text mb-1"><strong>Ciudad:</strong> Barcelona</p>
            <p class="card-text">Capacidad: 4 pers. · <span class="text-success">Disponible</span></p>
            <p class="card-text">Wifi · Ascensor · Adaptado PMR</p>
            <p class="card-text mb-2"><strong>Precio noche:</strong> 90€</p>
            <a href="#" class="btn btn-primary w-100">Ver detalles</a>
          </div>
        </div>
      </div>
      </div>
    </section>
    
    <section class="section-group" role="region" aria-labelledby="valencia-2-title">
      <h2 id="valencia-2-title" class="mb-4 text-center">Viviendas en Valencia</h2>
      <div class="row row-cols-1 row-cols-md-3 g-4">
      <!-- Card example 4 -->
      <div class="col">
        <div class="card h-100 shadow-sm">
          <img src="casa1.jpg" class="card-img-top" alt="Casa en Málaga">
          <div class="card-body">
            <h5 class="card-title">Casa Familiar en Málaga</h5>
            <p class="card-text mb-0"><strong>Provincia:</strong> Málaga</p>
            <p class="card-text mb-1"><strong>Ciudad:</strong> Marbella</p>
            <p class="card-text">Capacidad: 6 pers. · <span class="text-success">Disponible</span></p>
            <p class="card-text">Wifi · Piscina · Jardín · Parking</p>
            <p class="card-text mb-2"><strong>Precio noche:</strong> 120€</p>
            <a href="#" class="btn btn-primary w-100">Ver detalles</a>
          </div>
        </div>
      </div>

      <!-- Card example 5 -->
      <div class="col">
        <div class="card h-100 shadow-sm">
          <img src="casa2.jpg" class="card-img-top" alt="Casa en Cádiz">
          <div class="card-body">
            <h5 class="card-title">Villa Costa Cádiz</h5>
            <p class="card-text mb-0"><strong>Provincia:</strong> Cádiz</p>
            <p class="card-text mb-1"><strong>Ciudad:</strong> Tarifa</p>
            <p class="card-text">Capacidad: 8 pers. · <span class="text-danger">No disponible</span></p>
            <p class="card-text">Aire AC · Barbacoa · Wifi · Vistas mar</p>
            <p class="card-text mb-2"><strong>Precio noche:</strong> 150€</p>
            <a href="#" class="btn btn-secondary w-100 disabled">No disponible</a>
          </div>
        </div>
      </div>

      <!-- Card example 6 -->
      <div class="col">
        <div class="card h-100 shadow-sm">
          <img src="casa3.jpg" class="card-img-top" alt="Casa en Barcelona">
          <div class="card-body">
            <h5 class="card-title">Apartamento City Barcelona</h5>
            <p class="card-text mb-0"><strong>Provincia:</strong> Barcelona</p>
            <p class="card-text mb-1"><strong>Ciudad:</strong> Barcelona</p>
            <p class="card-text">Capacidad: 4 pers. · <span class="text-success">Disponible</span></p>
            <p class="card-text">Wifi · Ascensor · Adaptado PMR</p>
            <p class="card-text mb-2"><strong>Precio noche:</strong> 90€</p>
            <a href="#" class="btn btn-primary w-100">Ver detalles</a>
          </div>
        </div>
      </div>
      </div>
    </section>
  </div>
  
  <!-- Footer mejorado -->
  <footer class="site-footer mt-5 pt-5 pb-4">
    <div class="container">
      <div class="row gy-4">
        <div class="col-md-4">
          <div class="mb-2">
            <span class="footer-brand h5">Vacacional<span class="text-warning">Plus</span></span>
          </div>
          <p class="footer-note">Plataforma educativa de ejemplo para mostrar listados de alquiler vacacional. Diseño basado en Bootstrap y CSS, pensado para prácticas y prototipos.</p>
          <div class="social mt-3" aria-label="Redes sociales">
            <a href="#" aria-label="Facebook" title="Facebook">
              <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M22 12a10 10 0 1 0-11.5 9.9v-7h-2.2v-2.9h2.2V9.3c0-2.2 1.3-3.4 3.3-3.4.95 0 1.95.17 1.95.17v2.1h-1.07c-1.05 0-1.37.65-1.37 1.32v1.6h2.34l-.37 2.9h-1.97v7A10 10 0 0 0 22 12z"/></svg>
            </a>
            <a href="#" aria-label="Instagram" title="Instagram">
              <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M7 2h10a5 5 0 0 1 5 5v10a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5V7a5 5 0 0 1 5-5zm5 6.3A4.7 4.7 0 1 0 16.7 13 4.7 4.7 0 0 0 12 8.3zm6.8-3.5a1.1 1.1 0 1 1-1.1 1.1 1.1 1.1 0 0 1 1.1-1.1zM12 10.2A1.8 1.8 0 1 1 10.2 12 1.8 1.8 0 0 1 12 10.2z"/></svg>
            </a>
            <a href="#" aria-label="Twitter" title="Twitter">
              <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M22 5.9c-.6.3-1.3.5-2 .6.7-.4 1.3-1 1.6-1.8-.7.4-1.5.6-2.3.8A4 4 0 0 0 12 9v.5A10 10 0 0 1 3 4.7a4 4 0 0 0 1.2 5.3c-.6 0-1.2-.2-1.7-.5v.1A4 4 0 0 0 6 14a4 4 0 0 1-1.8.1 4 4 0 0 0 3.7 2.8A8 8 0 0 1 2 19.5 11 11 0 0 0 8 21c7.5 0 11.6-6.5 11.6-12.2v-.6c.8-.6 1.4-1.3 1.9-2.1-.7.3-1.5.5-2.3.6z"/></svg>
            </a>
          </div>
        </div>

        <div class="col-md-2">
          <h6 class="mb-3">Enlaces</h6>
          <ul class="list-unstyled">
            <li><a href="#">Inicio</a></li>
            <li><a href="#">Buscar casas</a></li>
            <li><a href="#">Favoritos</a></li>
            <li><a href="#">Blog</a></li>
          </ul>
        </div>

        <div class="col-md-3">
          <h6 class="mb-3">Soporte</h6>
          <ul class="list-unstyled">
            <li><a href="#">Ayuda</a></li>
            <li><a href="#">Política de privacidad</a></li>
            <li><a href="#">Términos y condiciones</a></li>
            <li><a href="#">Contacto</a></li>
          </ul>
        </div>

        <div class="col-md-3">
          <h6 class="mb-3">Contacto</h6>
          <address class="mb-2">C/ Ejemplo 123, 29001 Málaga</address>
          <div class="mb-2">Tel: <a href="tel:+34911222333">+34 911 22 2333</a></div>
          <div class="mb-3">Email: <a href="mailto:info@vacacionalplus.local">info@vacacionalplus.local</a></div>
          <form class="newsletter d-flex" method="post" action="#" aria-label="Suscribirse al newsletter">
            <input type="email" name="email" placeholder="Tu email" class="form-control me-2" required>
            <button class="btn btn-sm" type="submit">Suscribirse</button>
          </form>
        </div>
      </div>

      <hr class="mt-4" style="border-color: rgba(255,255,255,0.06)">
      <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center pt-3">
        <small class="text-white-50">© 2025 VacacionalPlus — Proyecto educativo</small>
        <small class="text-white-50 mt-2 mt-sm-0">Diseñado con Bootstrap • Maqueta sin fines comerciales</small>
      </div>
    </div>
  </footer>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
