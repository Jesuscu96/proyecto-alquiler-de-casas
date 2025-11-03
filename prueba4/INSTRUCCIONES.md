# CAMBIOS NECESARIOS EN index.php

## 1. Cambiar los botones "Ver detalles" a enlaces

En el archivo index.php (la versión del frontend), busca donde están los botones "Ver detalles" 
y cámbialos de esto:

```html
<button class="btn btn-primary">Ver detalles</button>
```

A esto:

```html
<a href="casa.php?id=<?php echo $casa['id_casa']; ?>" class="btn btn-primary">Ver detalles</a>
```

---

## 2. Estructura de archivos

Asegúrate de que los archivos estén en estas ubicaciones:

```
proyecto/
├── index.php (frontend)
├── login.php (nuevo)
├── casa.php (nuevo)
├── css/
│   ├── casa.css (nuevo)
│   ├── login.css (nuevo)
│   └── [otros archivos css existentes]
├── admin/
│   └── [archivos admin]
└── includes/
    ├── crudCasas.php
    ├── crudReservas.php
    ├── crudUsuarios.php
    ├── sessions.php
    └── [otros includes]
```

---

## 3. Requerimientos de las clases CRUD

Para que funcione correctamente, asegúrate de que tienes estos métodos en las clases:

### En crudCasas.php:
- `getCasaById($id)` - Obtiene una casa por ID
- `getAll()` - Obtiene todas las casas

### En crudReservas.php:
- `getAll()` - Obtiene todas las reservas
- `insertarReserva($id_usuario, $id_casa, $fecha_inicio, $fecha_fin, $estado, $precio_total)` - Inserta nueva reserva

### En crudUsuarios.php:
- `insertarUsuario($nombre, $username, $email, $clave, $rol)` - Inserta nuevo usuario
- `getAll()` - Obtiene todos los usuarios
- `comprobarCredenciales($usuario, $clave)` - Valida login

### En sessions.php:
- `comprobarSesion()` - Verifica si hay sesión activa
- `crearSesion($datos)` - Crea una nueva sesión

---

## 4. Funcionamiento de casa.php

- **GET con ID**: El usuario hace clic en "Ver detalles" y va a `casa.php?id=X`
- **Mostrar datos**: Se muestran todos los datos de la casa bonito
- **Mostrar fechas reservadas**: Se muestran todas las fechas ocupadas
- **Formulario de reserva**: Si NO está logueado, muestra botón "Ir al Login"
- **Si está logueado**: Muestra formulario con fechas de inicio y fin
- **POST de reserva**: Se procesa en el mismo archivo (casa.php)
- **Validación**: Comprueba que no haya solapamiento de fechas
- **Crear reserva**: Llama al método insertarReserva()

---

## 5. Funcionamiento de login.php

- **Por defecto**: Muestra el formulario de login
- **Link "Regístrate aquí"**: Cambia a `?registro=1` mostrando el formulario de registro
- **Registro**: Crea usuarios con rol 'cliente' automáticamente (no se muestra el rol al usuario)
- **Login exitoso**: Redirige a admin si es admin/superAdmin, o a index.php si es cliente
- **Validaciones**: Verifica emails únicos, usernames únicos, contraseñas mínimo 6 caracteres

---

## 6. Cambios en la carpeta admin

NO modificar nada. Solo si quieres mejorar los estilos:
- Puedes editar admin/css/styles.css si existe
- Los archivos CRUD funcionan igual para el admin

---

## 7. Variables de sesión disponibles

Dentro de casa.php y login.php, tienes acceso a:
- `$_SESSION['usuario']['id_usuario']` - ID del usuario
- `$_SESSION['usuario']['username']` - Nombre de usuario
- `$_SESSION['usuario']['rol']` - Rol del usuario (cliente, admin, superAdmin)

---

## RESUMEN DE ARCHIVOS CREADOS/MODIFICADOS

✅ CREAR (nuevos):
- casa.php
- login.php
- css/casa.css
- css/login.css

⚠️ MODIFICAR (existente):
- index.php - Cambiar botones "Ver detalles" a enlaces

✅ NO TOCAR:
- Archivos admin/
- Archivos includes/ (si ya están funcionando)
