<label>Otras imágenes</label>
<input type="file" name="imagenes[]" class="form-control" accept="image/*" multiple>
<?php

if (!empty($_FILES['imagenes']['name'][0])) {
    $carpeta = '../imagenes/';
    if (!is_dir($carpeta)) mkdir($carpeta, 0755, true);

    foreach ($_FILES['imagenes']['tmp_name'] as $index => $tmpName) {
        if ($_FILES['imagenes']['error'][$index] === UPLOAD_ERR_OK) {
            $nombreArchivo = basename($_FILES['imagenes']['name'][$index]);
            $rutaArchivo = $carpeta . uniqid('galeria_') . '_' . $nombreArchivo;
            if (move_uploaded_file($tmpName, $rutaArchivo)) { 
                $casaObj->insertarImagen($id_casa, $rutaArchivo);
            }
        }
    }
}
