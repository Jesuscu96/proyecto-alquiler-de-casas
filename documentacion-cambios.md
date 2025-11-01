# Documentación - Modificación de casas2.php

## 📋 Resumen Ejecutivo

Se ha modificado el archivo **casas2.php** siguiendo el patrón de **dispositivos.php** para eliminar los modales Bootstrap y usar un sistema de formularios inline con confirmaciones nativas JavaScript.

---

## ✅ Cambios Principales Implementados

### 1. **Eliminación de Modales Bootstrap**
- ✓ Se eliminaron todos los modales Bootstrap
- ✓ El formulario ahora se muestra inline (en la misma página)
- ✓ Sigue el patrón de dispositivos.php

### 2. **Sistema de Visualización Condicional**
```php
<?php if ($accion === 'crear' || $accion === 'editar'): ?>
    <!-- FORMULARIO -->
<?php else: ?>
    <!-- TABLA Y FILTROS -->
<?php endif; ?>
```

**Lógica:**
- Sin parámetro `accion` → Muestra: **Estadísticas + Filtros + Tabla**
- `accion=crear` → Muestra: **Estadísticas + Formulario vacío** (tabla oculta)
- `accion=editar&id=X` → Muestra: **Estadísticas + Formulario con datos** (tabla oculta)
- `accion=eliminar&id=X` → Ejecuta eliminación con `confirm()` JS

### 3. **Confirmaciones Nativas JavaScript**
```html
<a href="?accion=eliminar&id=<?= $casa['id_casa'] ?>" 
   onclick="return confirm('¿Estás seguro de que deseas eliminar esta casa?');">
    Eliminar
</a>
```

### 4. **Flujo de Trabajo**
1. **Clic "Añadir Nueva Casa"** → `?accion=crear` → Muestra formulario
2. **Clic "Editar"** → `?accion=editar&id=5` → Muestra formulario con datos
3. **Clic "Eliminar"** → `confirm()` → Si OK: elimina y recarga
4. **Submit formulario** → Guarda → Redirección a `casas2.php`

---

## 🎨 Diseño Visual Mantenido

- ✓ Se mantiene **admin.css** intacto
- ✓ **Estadísticas** siempre visibles (Total, VIP, Precio Promedio)
- ✓ Cards con gradientes y sombras
- ✓ Tabla responsive con hover effects
- ✓ Botones con iconos Bootstrap Icons

---

## 🔧 Funcionalidades Conservadas

### Filtros
- Por provincia
- Por ciudad
- Capacidad mínima
- Precio máximo

### Paginación
- 8 casas por página
- Navegación numérica
- Mantiene filtros

### Validaciones
- Campos obligatorios (propietario, nombre, ubicación, etc.)
- Validaciones numéricas (capacidad >= 1, precio >= 0)
- Validaciones de archivos (imagen opcional)

### Gestión de Imágenes
- Upload de imagen principal
- Previsualización en tabla
- Manejo de errores

---

## 📊 Comparación: Antes vs Después

| CARACTERÍSTICA | ANTES | DESPUÉS |
|---|---|---|
| **Modales Bootstrap** | ✓ Usaba modales | ✗ Sin modales |
| **Formulario** | Dentro de modal | Inline en página |
| **Confirmación eliminar** | Modal Bootstrap | `confirm()` nativo |
| **Visibilidad tabla** | Siempre visible | Oculta al crear/editar |
| **Redirección POST** | Refresh con mensaje | `header()` a casas2.php |
| **Parámetros URL** | Sin parámetros GET | `?accion=crear/editar/eliminar&id=X` |
| **Diseño CSS** | admin.css | admin.css (mantenido) |
| **Estadísticas** | Siempre visibles | Siempre visibles |
| **Filtros** | Funcionales | Funcionales |
| **Paginación** | 8 por página | 8 por página |

---

## 🚀 Guía de Uso

### Instalación
1. Reemplaza el archivo `casas2.php` original con `casas2_modificado.php`
2. Renombra `casas2_modificado.php` a `casas2.php`
3. Asegúrate de tener:
   - `crudCasas.php`
   - `crudUbicacion.php`
   - `crudUsuarios.php`
   - `admin.css`
   - `includes/sessions.php`

### Uso Básico

#### Vista Principal
- **URL:** `casas2.php`
- **Muestra:** Estadísticas + Filtros + Tabla
- **Botón:** "Añadir Nueva Casa"

#### Crear Casa
1. Clic en "Añadir Nueva Casa"
2. URL: `casas2.php?accion=crear`
3. Tabla se oculta, aparece formulario vacío
4. Rellenar campos obligatorios (*)
5. Clic "Crear Casa" → Guarda y vuelve a tabla

#### Editar Casa
1. Clic "Editar" en tabla
2. URL: `casas2.php?accion=editar&id=5`
3. Tabla se oculta, formulario con datos cargados
4. Modificar campos
5. Clic "Actualizar Casa" → Guarda y vuelve a tabla

#### Eliminar Casa
1. Clic "Eliminar" en tabla
2. Aparece `confirm()`: "¿Estás seguro...?"
3. Si aceptas → Elimina y recarga página

#### Cancelar
- Clic "Cancelar" en formulario
- Vuelve a `casas2.php` (sin parámetros)
- Muestra tabla nuevamente

---

## ✔️ Validaciones Implementadas

### Campos Obligatorios (*)
- Propietario
- Nombre de la casa
- Comunidad Autónoma
- Provincia
- Ciudad
- Capacidad (mínimo 1)
- Precio por noche (mínimo €0)
- Baños (mínimo 1)
- Cocinas (mínimo 1)
- Neveras (mínimo 1)

### Manejo de Errores
- Alert rojo con lista de errores
- Formulario no se envía si hay errores
- Datos introducidos se mantienen

---

## 🔍 Estructura del Formulario

### Secciones Organizadas

1. **📌 INFORMACIÓN BÁSICA**
   - Propietario
   - Nombre de la casa

2. **📍 UBICACIÓN**
   - Comunidad Autónoma
   - Provincia
   - Ciudad

3. **💰 CAPACIDAD Y PRECIO**
   - Capacidad (personas)
   - Precio por noche (€)

4. **🚪 HABITACIONES Y BAÑOS**
   - Baños
   - Cocinas
   - Habitaciones individuales
   - Habitaciones familiares

5. **🔌 ELECTRODOMÉSTICOS**
   - Lavadoras, Secadoras, Lavavajillas
   - Hornos, Microondas
   - Neveras, Congeladores
   - Aparcamientos

6. **⭐ AMENIDADES**
   - Wi-Fi, Calefacción, Aire Acondicionado
   - Piscina, Bañera, Barbacoa, Chimenea
   - Adaptación Discapacitados
   - Jardín, Patio, Sala de Cine, Secador

7. **🖼️ IMAGEN PRINCIPAL**
   - Upload de imagen (opcional)

---

## 🧪 URLs para Testing

```
# Vista principal
http://tu-dominio/casas2.php

# Crear nueva casa
http://tu-dominio/casas2.php?accion=crear

# Editar casa ID 5
http://tu-dominio/casas2.php?accion=editar&id=5

# Eliminar casa ID 3
http://tu-dominio/casas2.php?accion=eliminar&id=3

# Filtrar por provincia 10
http://tu-dominio/casas2.php?provincia=10

# Paginación página 2
http://tu-dominio/casas2.php?pagina=2

# Filtros combinados
http://tu-dominio/casas2.php?provincia=10&ciudad=5&capacidad=4&precio=500
```

---

## 💡 Tips y Consejos

1. Las **estadísticas** se calculan con TODAS las casas (no las filtradas)
2. Los **filtros** afectan solo a la tabla, no a las estadísticas
3. Al crear/editar, puedes **cancelar** en cualquier momento
4. La **imagen** es opcional, pero recomendada
5. Los **checkboxes** NO requieren marcarse (opcionales)
6. El formulario tiene **scroll** si es muy largo (max-height: 70vh)
7. El botón "Cancelar" **no guarda** cambios

---

## 🎯 Diferencias con dispositivos.php

### Similitudes
- ✓ No usa modales
- ✓ Formulario inline
- ✓ Parámetros GET (accion, id)
- ✓ `confirm()` para eliminar
- ✓ Redirecciones después de operaciones

### Diferencias
- `casas2.php` tiene **32+ campos** vs 3 campos en `dispositivos.php`
- `casas2.php` mantiene **estadísticas** siempre visibles
- `casas2.php` tiene **filtros y paginación**
- `casas2.php` usa **admin.css** personalizado
- `casas2.php` gestiona **imágenes**
- `casas2.php` tiene **validaciones más complejas**

---

## 📝 Notas Técnicas

### Procesamiento PHP
```php
// Parámetros GET
$accion = $_GET['accion'] ?? null;
$id = $_GET['id'] ?? null;

// Eliminación
if ($accion === 'eliminar' && $id) {
    $casaObj->eliminarCasa($id);
    header("Location: casas2.php");
    exit();
}

// Procesamiento POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validaciones y guardado
    if (empty($errores)) {
        if ($accion === 'crear') {
            $casaObj->insertarCasa(...);
        } elseif ($accion === 'editar' && $id) {
            $casaObj->actualizarCasa(...);
        }
        header("Location: casas2.php");
        exit();
    }
}
```

### Visualización Condicional
```php
<?php if ($accion === 'crear' || $accion === 'editar'): ?>
    <!-- Formulario -->
    <div class="card">
        <form method="POST" enctype="multipart/form-data">
            <!-- Campos del formulario -->
        </form>
    </div>
<?php else: ?>
    <!-- Filtros y Tabla -->
    <div class="filters-card"><!-- Filtros --></div>
    <div class="table-responsive"><!-- Tabla --></div>
<?php endif; ?>
```

---

## ✨ Resultado Final

El archivo **casas2.php modificado** ahora:
- ✅ **NO usa modales** Bootstrap
- ✅ Muestra el **formulario inline** como dispositivos.php
- ✅ Usa **confirmaciones nativas** JavaScript
- ✅ **Oculta la tabla** al crear/editar
- ✅ **Mantiene el diseño visual** bonito con admin.css
- ✅ **Conserva todas las funcionalidades** (filtros, paginación, estadísticas)
- ✅ **Redirecciona correctamente** después de operaciones

---

**Fecha de modificación:** 1 de noviembre de 2025  
**Autor:** Asistente IA - Perplexity  
**Versión:** 1.0