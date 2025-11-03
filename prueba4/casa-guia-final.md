# CASA.PHP - GUÍA COMPLETA

## 📊 Array de Datos $datos_casa

```php
$datos_casa = [
    // Información básica
    'id_casa' => '',
    'nombre' => '',
    'capacidad' => '',
    'precio_noche' => '',
    
    // Ubicación
    'id_provincia' => '',
    'id_ciudad' => '',
    'provincia' => '',
    'ciudad' => '',
    
    // Habitaciones y baños
    'num_hab_individuales' => '',
    'num_hab_familiares' => '',
    'num_banos' => '',
    
    // Servicios básicos
    'num_cocinas' => '',
    'num_aparcamientos' => '',
    'num_ascensores' => '',
    
    // Electrodomésticos
    'num_lavadora' => '',
    'num_secadora' => '',
    'num_lavavajillas' => '',
    'num_horno' => '',
    'num_microondas' => '',
    'num_nevera' => '',
    'num_congelador' => '',
    'tiene_secador_pelo' => false,
    
    // Características booleanas
    'tiene_wifi' => false,
    'tiene_piscina' => false,
    'tiene_jardin' => false,
    'tiene_patio' => false,
    'tiene_calefaccion' => false,
    'tiene_aire_acondicionado' => false,
    'tiene_barbacoa' => false,
    'tiene_chimenea' => false,
    'tiene_banera' => false,
    'tiene_sala_cine' => false,
    'tiene_adaptacion_discapacitados' => false,
    
    // Imágenes
    'imagen_principal' => '',
];
```

## 📸 Galería de Imágenes - Tabla IMAGENES

La tabla `imagenes` contiene:
- `id_imagen` - ID único de la imagen
- `id_casa` - ID de la casa a la que pertenece
- `url` - Ruta de la imagen (ej: ./imagenes/foto1.jpg)
- `descripcion` - Descripción de la imagen

### Código para obtener imágenes:

```php
$imagenes = [];
try {
    if (method_exists($casaObj, 'getImagenesByCasa')) {
        $imagenes = $casaObj->getImagenesByCasa($id_casa);
    }
} catch (Exception $e) {
    $imagenes = [];
}
```

### Mostrar en HTML:

```php
<?php if (!empty($imagenes)): ?>
<div class="galeria-fotos">
    <h3><i class="bi bi-images"></i> Galería de Fotos</h3>
    <div class="galeria-grid">
        <?php foreach ($imagenes as $imagen): ?>
        <div class="galeria-item">
            <img src="<?php echo htmlspecialchars($imagen['url']); ?>" 
                 alt="<?php echo htmlspecialchars($imagen['descripcion'] ?? 'Imagen de la casa'); ?>"
                 class="img-fluid">
            <?php if (!empty($imagen['descripcion'])): ?>
            <p class="galeria-descripcion"><?php echo htmlspecialchars($imagen['descripcion']); ?></p>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>
```

## 📋 Secciones del HTML

### 1️⃣ HERO IMAGE (Imagen Principal)
- 400px de alto en desktop
- Responsive a 250px en tablet, 200px en móvil
- Muestra: nombre, ubicación, 3 características principales

### 2️⃣ GALERÍA DE FOTOS
- Grid responsivo de imágenes de tabla imagenes
- Hover: zoom (1.05) + descripción emergente
- Columnas: 200px (desktop), 150px (tablet), 120px (móvil)

### 3️⃣ DETALLES DE LA CASA
- Capacidad total
- Habitaciones desglosadas (individuales + familiares)
- Baños, cocinas, aparcamientos, ascensores

### 4️⃣ ELECTRODOMÉSTICOS
- Lavadoras, secadoras, lavavajillas
- Hornos, microondas, neveras, congeladores
- Secadores de pelo (booleano)

### 5️⃣ SERVICIOS Y CARACTERÍSTICAS
Muestra en grid solo los que tienen valor true:
- WiFi, piscina, jardín, patio
- Calefacción, aire acondicionado
- Barbacoa, chimenea, bañera
- Sala de cine
- Adaptación para discapacitados

### 6️⃣ FECHAS RESERVADAS
- Lista todas las reservas de esa casa
- Formato: dd/mm/yyyy - dd/mm/yyyy
- Color rojo para visual de ocupación

### 7️⃣ FORMULARIO DE RESERVA
- Input date para fecha inicio
- Input date para fecha fin
- Valida: fin > inicio
- Valida: sin solapamientos
- Calcula: precio_total = días * precio_noche
- Si NO hay sesión: muestra botón "Ir al Login"
- Si hay sesión: muestra formulario

## 🎨 Clases CSS Principales

```css
.galeria-grid
  /* Grid responsivo de fotos */
  
.galeria-item
  /* Card individual de foto con hover */
  
.galeria-descripcion
  /* Texto que aparece en hover */
  
.detalles-grid
  /* Grid de características detalladas */
  
.electrodomesticos-grid
  /* Grid de electrodomésticos */
  
.servicios-lista
  /* Grid de servicios booleanos */
  
.precio-section
  /* Card del precio destacado */
  
.formulario-reserva
  /* Card del formulario */
  
.alerta-error / .alerta-exito
  /* Mensajes de validación */
```

## 🔧 Métodos CRUD Requeridos

### En crudCasas.php:
```php
// Obtener una casa por ID
public function getCasaById($id) {
    // Retorna array con todos los datos de la casa
}

// Obtener imágenes de una casa
public function getImagenesByCasa($id_casa) {
    // Retorna array de imágenes con url y descripcion
}
```

### En crudReservas.php:
```php
// Obtener todas las reservas
public function getAll() {
    // Retorna array de todas las reservas
}

// Insertar nueva reserva
public function insertarReserva($id_usuario, $id_casa, $fecha_inicio, $fecha_fin, $estado, $precio_total) {
    // Inserta en tabla reservas
}
```

### En sessions.php:
```php
// Verificar si hay sesión activa
public function comprobarSesion() {
    // Retorna true/false
}

// Crear sesión con datos del usuario
public function crearSesion($datos) {
    // Establece $_SESSION['usuario']
}
```

## ✅ Validaciones Implementadas

- ✔️ Fecha fin debe ser posterior a fecha inicio
- ✔️ No solapamiento con reservas existentes
- ✔️ htmlspecialchars() en TODAS las salidas
- ✔️ Comprobación de sesión antes de procesar formulario
- ✔️ Redirección a login si no hay sesión
- ✔️ Try-catch para inserción de reserva

## 📁 Archivos Finales

```
proyecto/
├── casa.php (→ usar casa_completo.php)
├── css/
│   └── casa.css (→ usar casa_final.css)
└── ... resto de archivos igual
```

## 🚀 Instalación

1. Renombra `casa_completo.php` a `casa.php`
2. Guarda `casa_final.css` en carpeta `css/casa.css`
3. Verifica que `index.php` tenga los botones "Ver detalles" con links a `casa.php?id=...`
4. Asegúrate de que los métodos CRUD existan en tus archivos includes
5. ¡Listo! 🎉