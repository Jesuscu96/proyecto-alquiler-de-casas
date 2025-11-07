Confirmado o Resuelto: ☑️ ✔️ 🟢 ✅

Cancelado: 🚫 🔴 ✖️ ❌

Pendiente: 🕒 🟡 ⌛ 

En casas poner botones de mas informacion con un alert confirm o modal ✅ Mejorar la visualizacion ⌛
Cambiar todos lo iconos 🟡 ⌛

En casas mejorar la visualizacios del formulario a mas pequeño ⌛

Asegurarme del guardado de las imagenes es correcto en casas.php⌛

Mirar la paginacion en todas los archivos 🟡 ⌛

El contar usuarios hacerlo con un crud ✅

El contar casas hacerlo en el crud ✅

Los roles de usuario con select y que carguen los datos al editar ✅

# Crear calle y patio, puerta en bbdd y sus archivos.php

Comprobar si solo los superAdmin pueden cambiar la contraseña de un superAdmin y cambiar el rol a SuperAdmin ⌛

Hacer que un usuario admin no pueda cambiar el rol de un superAdmin a uno inferior✅

Crear reservas.php ✅ y revisar reservas.php crudReserevas.php ⌛ 

Dar formato a las fechas en casa.php y reservas.php ✅

En casa.php para **RESERVAR** cambiar el **required** por if else de errores

Crear mas Usuarios y casas en bbdd ⌛

Mejor la estetica de login ⌛

Como insertar imagens de forma dinamica cuando un usuario cree en una casa  quitar de la direccion de los nombres bbdd⌛

Crear la Paginacion en todos los archivos ⌛

Eleminar los modals de admin.css ⌛

Crear filtros en todas las paginas  con botones y sea un crud como  casa = casaobj->getbyid o casa = casaobj->getbyidDesc con check de from ⌛

Mejorar la estetica general ⌛

Que un usuario Cliente cree una casa ⌛

Cambiar el style de casa.php a al achivo styles.css ⌛

En casas mirar si son camas o habitaciones que es mejor poner ⌛

Intentar mandar email ⌛


<label>Otras imágenes</label>
<input type="file" name="imagenes[]" class="form-control" accept="image/*" multiple>
<?php

if (!empty($_FILES['imagenes']['name'][0])) {
    $carpeta = '../imagenes/';
    if (!is_dir($carpeta)) mkdir($carpeta, 0755, true);

    foreach ($_FILES['imagenes']['tmp_name'] as $index => $tmpName) {
        if ($_FILES['imagenes']['error'][$index] === UPLOAD_ERR_OK) {
            $nombreArchivo = basename($_FILES['imagenes']['name'][$index]);
            $rutaArchivo = $carpeta . uniqid('galeria_') . $nombreArchivo;
            if (move_uploaded_file($tmpName, $rutaArchivo)) { 
                $casaObj->insertarImagen($id_casa, $rutaArchivo);
            }
        }
    }
}
?>