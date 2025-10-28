<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>VacacionalPlus - Inicio</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <style>
    :root {
      --primary-color: #FF5A5F;
      --secondary-color: #00A699;
      --light-gray: #fafafa;
    }
    body {
      font-family: 'Circular', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background-color: var(--light-gray);
      margin-left: 280px;
      padding: 2rem 3rem;
    }

    h2 {
      border-bottom: 3px solid var(--primary-color);
      padding-bottom: 0.5rem;
      margin-bottom: 2rem;
      font-weight: 700;
    }

    .property-card {
      background: white;
      border-radius: 16px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.08);
      transition: all 0.4s ease;
      overflow: hidden;
    }

    .property-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 20px 40px rgba(0,0,0,0.14);
    }

    .property-img {
      width: 100%;
      height: 180px;
      object-fit: cover;
      transition: transform 0.4s ease;
    }

    .property-card:hover .property-img {
      transform: scale(1.05);
    }

    .card-body {
      padding: 1rem 1.5rem;
    }

    .price-badge {
      background: var(--primary-color);
      color: white;
      padding: 0.5rem 1rem;
      border-radius: 25px;
      font-weight: 600;
      display: inline-block;
      margin-top: 0.5rem;
    }
  </style>
</head>
<body>

<h1>Bienvenido a VacacionalPlus</h1>

<section>
  <h2>Más reservadas en 2025</h2>
  <div class="row g-4 mb-5">
    <!-- CASA 1 -->
    <div class="col-md-4">
      <div class="property-card">
        <img src="https://source.unsplash.com/400x300/?house,1" alt="Villa Mediterránea" class="property-img">
        <div class="card-body">
          <h3>Villa Mediterránea</h3>
          <p>Marbella, Málaga</p>
          <p>Capacidad: 6 huéspedes</p>
          <div class="price-badge">120 € / noche</div>
        </div>
      </div>
    </div>
    <!-- CASA 2 -->
    <div class="col-md-4">
      <div class="property-card">
        <img src="https://source.unsplash.com/400x300/?house,2" alt="Apartamento Costa Brava" class="property-img">
        <div class="card-body">
          <h3>Apartamento Costa Brava</h3>
          <p>Lloret de Mar, Girona</p>
          <p>Capacidad: 4 huéspedes</p>
          <div class="price-badge">85 € / noche</div>
        </div>
      </div>
    </div>
    <!-- CASA 3 -->
    <div class="col-md-4">
      <div class="property-card">
        <img src="https://source.unsplash.com/400x300/?house,3" alt="Chalet Alicante" class="property-img">
        <div class="card-body">
          <h3>Chalet Alicante</h3>
          <p>Benidorm, Alicante</p>
          <p>Capacidad: 5 huéspedes</p>
          <div class="price-badge">110 € / noche</div>
        </div>
      </div>
    </div>
    <!-- CASA 4 -->
    <div class="col-md-4">
      <div class="property-card">
        <img src="https://source.unsplash.com/400x300/?house,4" alt="Casa Rural" class="property-img">
        <div class="card-body">
          <h3>Casa Rural</h3>
          <p>Jaca, Huesca</p>
          <p>Capacidad: 8 huéspedes</p>
          <div class="price-badge">95 € / noche</div>
        </div>
      </div>
    </div>
    <!-- CASA 5 -->
    <div class="col-md-4">
      <div class="property-card">
        <img src="https://source.unsplash.com/400x300/?house,5" alt="Apartamento Sevilla" class="property-img">
        <div class="card-body">
          <h3>Apartamento Sevilla</h3>
          <p>Sevilla, Sevilla</p>
          <p>Capacidad: 3 huéspedes</p>
          <div class="price-badge">75 € / noche</div>
        </div>
      </div>
    </div>
  </div>
</section>

<section>
  <h2>Más baratas</h2>
  <div class="row g-4 mb-5">
    <!-- 5 casas diferentes con precios bajos aquí -->
    <div class="col-md-4">
      <div class="property-card">
        <img src="https://source.unsplash.com/400x300/?house,6" alt="Casa Económica" class="property-img">
        <div class="card-body">
          <h3>Casa Económica</h3>
          <p>Salamanca, Castilla y León</p>
          <p>Capacidad: 4 huéspedes</p>
          <div class="price-badge">60 € / noche</div>
        </div>
      </div>
    </div>
    <!-- Más casas aquí -->
  </div>
</section>

<section>
  <h2>Cerca de ti</h2>
  <div class="row g-4 mb-5">
    <!-- 5 casas cercanas con datos ficticios -->
    <div class="col-md-4">
      <div class="property-card">
        <img src="https://source.unsplash.com/400x300/?house,7" alt="Casa Local" class="property-img">
        <div class="card-body">
          <h3>Casa Local</h3>
          <p>Ciudad Real, Castilla-La Mancha</p>
          <p>Capacidad: 5 huéspedes</p>
          <div class="price-badge">80 € / noche</div>
        </div>
      </div>
    </div>
  </div>
</section>

<section>
  <h2>Nuevas incorporaciones</h2>
  <div class="row g-4 mb-5">
    <!-- 5 casas nuevas aquí -->
    <div class="col-md-4">
      <div class="property-card">
        <img src="https://source.unsplash.com/400x300/?house,8" alt="Casa Nueva" class="property-img">
        <div class="card-body">
          <h3>Casa Nueva</h3>
          <p>Oviedo, Asturias</p>
          <p>Capacidad: 7 huéspedes</p>
          <div class="price-badge">130 € / noche</div>
        </div>
      </div>
    </div>
  </div>
</section>

<section>
  <h2>Sugerencias para ti</h2>
  <div class="row g-4 mb-5">
    <!-- 5 sugerencias con datos inventados -->
    <div class="col-md-4">
      <div class="property-card">
        <img src="https://source.unsplash.com/400x300/?house,9" alt="Casa Sugerencia" class="property-img">
        <div class="card-body">
          <h3>Casa Sugerencia</h3>
          <p>Toledo, Castilla-La Mancha</p>
          <p>Capacidad: 4 huéspedes</p>
          <div class="price-badge">90 € / noche</div>
        </div>
      </div>
    </div>
  </div>
</section>

</body>
</html>