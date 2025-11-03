# Guía de Implementación - CasasApp

## Estructura de Carpetas

```
proyecto/
├── login.php                    (Inicio de sesión con CSS/Bootstrap mejorado)
├── indexCasa.php               (Página principal pública)
├── styles.css                  (Estilos generales)
├── admin.css                   (Estilos administrativos)
├── imagenes/                   (Carpeta para guardar imágenes)
├── includes/
│   ├── sessions.php            (Manejo de sesiones)
│   ├── database.php            (Conexión a BD)
│   ├── crudUsuarios.php        (Operaciones CRUD de usuarios)
│   ├── logout.php              (Cerrar sesión)
│   └── footer.php              (Footer para incluir)
├── admin/
│   ├── index.php               (Dashboard para administradores)
│   ├── menu.php                (Menú solo para administradores)
│   ├── usuarios.php            (Gestión de usuarios CON validación PHP)
│   ├── casas2.php              (Gestión de casas)
│   └── reservas.php            (Gestión de reservas)
```

## Cambios Implementados

### 1. **login.php** ✅
- ✓ Nuevo diseño con CSS y Bootstrap
- ✓ Gradientes modernos
- ✓ Validación de campos vacíos en PHP
- ✓ Diseño responsive
- ✓ Icons de Bootstrap Icons
- ✓ Sin atributo `required` en HTML (validación solo PHP)

### 2. **menu.php** ✅
- ✓ Archivo separado para incluir en admin
- ✓ Navbar Bootstrap mejorada
- ✓ Solo visible para administradores
- ✓ Dropdown con nombre del usuario
- ✓ Enlaces de navegación con iconos

### 3. **footer.php** ✅
- ✓ Archivo separado para incluir
- ✓ Diseño consistente con admin.css
- ✓ Estilos integrados
- ✓ Responsive

### 4. **usuarios.php** ✅
- ✓ Validación SIN atributo `required` HTML
- ✓ Validación 100% en PHP (lado servidor)
- ✓ Mensajes de error específicos por campo
- ✓ Muestra errores de validación en alert
- ✓ Separación de formularios para:
  - Crear usuario (con campos de contraseña)
  - Editar usuario (sin contraseña)
  - Editar contraseña (solo password)
- ✓ Modales Bootstrap para formularios
- ✓ Tabla con estadísticas

## Uso de los Archivos

### En **admin/usuarios.php**:
```php
<?php
include("../includes/sessions.php");
include("../includes/crudUsuarios.php");
include("menu.php");
// ... resto del código
?>
```

### Al final de cada admin file:
```php
<?php include("../includes/footer.php"); ?>
```

## Validación PHP sin `required` HTML

**IMPORTANTE**: NO se usa `required` en los inputs, solo validación PHP:

```php
// Validación en PHP (lado servidor)
if (empty($username)) {
    $errores['username'] = "El username no puede estar vacío.";
}

// Mostrar error en HTML
<?php if (isset($errores['username'])): ?>
    <div class="invalid-feedback d-block">
        <?php echo $errores['username']; ?>
    </div>
<?php endif; ?>
```

## Funcionalidades por Acción

### Crear Usuario
- Validar todos los campos
- Validar que contraseña y confirmación coincidan
- Crear hash de contraseña

### Editar Usuario
- Validar todos los campos excepto contraseña
- Actualizar datos
- Mostrar datos cargados en el formulario

### Editar Contraseña
- Solo campo de contraseña y confirmación
- Validar que coincidan
- Crear nuevo hash

## Scripts Bootstrap Necesarios

Incluir al final del body:
```html
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
```

## Estilos CSS

Se usan variables CSS definidas en **admin.css**:
- `--primary: #0b5482`
- `--secondary: #4fd1c5`
- `--tertiary: #501563`
- `--accent: #ffd166`
- `--dark: #072d4b`
- `--danger: #f14b4b`

## Próximos Pasos

1. Copiar archivos a sus carpetas correspondientes
2. Incluir en **admin/index.php**:
   - `menu.php` al inicio
   - `footer.php` al final
3. Incluir en **admin/casas2.php** y **admin/reservas.php**:
   - `menu.php` al inicio
   - `footer.php` al final
4. Actualizar **indexCasa.php** si es necesario

## Notas Importantes

- ✓ **SIN JavaScript para validación** - Todo en PHP
- ✓ **SIN atributo `required`** - Validación solo servidor
- ✓ **Errores mostrados en la página** - Debajo de cada input
- ✓ **Bootstrap para UI** - Solo componentes necesarios
- ✓ **Archivos separados** - menu.php y footer.php reutilizables