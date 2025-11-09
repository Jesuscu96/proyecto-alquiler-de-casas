
<?php
require_once("database.php");

class Casas {
    public function getAll() {
        $db = new Connection();
        $conn = $db->getConnection();

        $sql = "SELECT
                c.*,
                u.username AS propietario,
                com.nombre AS comunidad,
                p.nombre   AS provincia,
                ci.nombre  AS ciudad
                FROM casas_vacacionales c
                LEFT JOIN usuarios    u   ON u.id_usuario     = c.id_propietario
                LEFT JOIN comunidades com ON com.id_comunidad = c.id_comunidad
                LEFT JOIN provincias  p   ON p.id_provincia   = c.id_provincia
                LEFT JOIN ciudades    ci  ON ci.id_ciudad     = c.id_ciudad
                ORDER BY c.nombre";

        $result = $conn->query($sql);
        $db->closeConnection($conn);
        
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    /* public function getAll() {
        $db = new Connection();
        $conn = $db->getConnection();
        
        $sql = "SELECT * FROM casas_vacaionales";
        
        $result = $conn->query($sql);
        $db->closeConnection($conn);
        //cuando devuelve mas resultados
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    } */
    public function getCantidadCasas() {
        $db = new Connection();
        $conn = $db->getConnection();
        
        $sql = "SELECT COUNT(id_casa) AS cantidad_casas FROM casas_vacacionales";
        
        $result = $conn->query($sql);
        $db->closeConnection($conn);
        return $result ? $result->fetch_assoc()['cantidad_casas'] : 0;
    }
    public function getCantidadCasasVip() {
        $db = new Connection();
        $conn = $db->getConnection();
        
        $sql = "SELECT COUNT(id_casa) AS cantidad_vip 
                FROM casas_vacacionales 
                WHERE precio_noche > 2000";
        
        $result = $conn->query($sql);
        $db->closeConnection($conn);
        
        return $result ? $result->fetch_assoc()['cantidad_vip'] : 0;
    }

    public function getCasasVip() {
        $db = new Connection();
        $conn = $db->getConnection();
        
        $sql = "SELECT 
                c.*,
                u.username AS propietario,
                com.nombre AS comunidad,
                p.nombre   AS provincia,
                ci.nombre  AS ciudad
                FROM casas_vacacionales c
                LEFT JOIN usuarios    u   ON u.id_usuario     = c.id_propietario
                LEFT JOIN comunidades com ON com.id_comunidad = c.id_comunidad
                LEFT JOIN provincias  p   ON p.id_provincia   = c.id_provincia
                LEFT JOIN ciudades    ci  ON ci.id_ciudad     = c.id_ciudad
                ORDER BY c.precio_noche DESC
                LIMIT 3";

        
        $result = $conn->query($sql);
        $db->closeConnection($conn);
        
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    public function getPrecioPromedioCasas() {
        $db = new Connection();
        $conn = $db->getConnection();
        
        $sql = "SELECT AVG(precio_noche) AS precio_promedio 
                FROM casas_vacacionales";
        
        $result = $conn->query($sql);
        $db->closeConnection($conn);
        
        return $result ? round($result->fetch_assoc()['precio_promedio'], 2) : 0;
    }
    public function getCasaById($id) {
        $db = new Connection();
        $conn = $db->getConnection();
        
        $sql = "SELECT c.*, u.username AS propietario, com.nombre AS comunidad, 
        p.nombre AS provincia, ci.nombre AS ciudad
        FROM casas_vacacionales c
        LEFT JOIN usuarios u ON u.id_usuario = c.id_propietario
        LEFT JOIN comunidades com ON com.id_comunidad = c.id_comunidad
        LEFT JOIN provincias p ON p.id_provincia = c.id_provincia
        LEFT JOIN ciudades ci ON ci.id_ciudad = c.id_ciudad
        WHERE c.id_casa = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $db->closeConnection($conn);
        //cuando devuelve un solo resultado
        return $result ? $result->fetch_assoc() : [];
    }
    public function getCasasByIdUsuario($id_usuario) {
        $db = new Connection();
        $conn = $db->getConnection();
        
        $sql = "SELECT c.*, com.nombre AS comunidad, p.nombre AS provincia, 
        ci.nombre AS ciudad
        FROM casas_vacacionales c
        LEFT JOIN comunidades com ON com.id_comunidad = c.id_comunidad
        LEFT JOIN provincias p ON p.id_provincia = c.id_provincia
        LEFT JOIN ciudades ci ON ci.id_ciudad = c.id_ciudad
        WHERE c.id_propietario = ?";
                
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $db->closeConnection($conn);
        //cuando devuelve un solo resultado
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    public function getCantidadCasasByUsuario($id_usuario) {
        $db = new Connection();
        $conn = $db->getConnection();
        
        $sql = "SELECT COUNT(id_casa) AS total_casas
                FROM casas_vacacionales
                WHERE id_propietario = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $db->closeConnection($conn);
        
        return $result ? $result->fetch_assoc()['total_casas'] : 0;
    }

    public function insertarImagen($id_casa, $url) {
        $db = new Connection();
        $conn = $db->getConnection();

        $sql = "INSERT INTO imagenes (id_casa, url) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $id_casa, $url);
        $stmt->execute();

        $db->closeConnection($conn);
    }

    public function getImagenesByCasa($id_casa) {
        $db = new Connection();
        $conn = $db->getConnection();
        
        $sql = "SELECT * FROM `imagenes` WHERE id_casa = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_casa);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $db->closeConnection($conn);
        //cuando devuelve un solo resultado
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    /* public function insertarCasa($id_propietario, $id_comunidad, $id_provincia, $id_ciudad, 
        $nombre, $capacidad, $precio_noche, $num_banos, $num_cocinas) {

        $db = new Connection();
        $conn = $db->getConnection();

        $sql = "INSERT INTO casas_vacacionales 
                (id_propietario, id_comunidad, id_provincia, id_ciudad, nombre, capacidad, precio_noche, num_banos, num_cocinas) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiiisidii", 
            $id_propietario, $id_comunidad, $id_provincia, $id_ciudad, 
            $nombre, $capacidad, $precio_noche, $num_banos, $num_cocinas);

        $stmt->execute();
        $db->closeConnection($conn);
    } */
    
    public function insertarCasa(
    $id_propietario, $id_comunidad, $id_provincia, $id_ciudad,
    $nombre, $capacidad, $precio_noche,
    $num_banos, $num_cocinas, $num_hab_individuales, $num_hab_familiares,
    $num_aparcamientos, $num_lavadora, $num_secadora, $num_lavavajillas,
    $num_horno, $num_microondas, $num_nevera, $num_congelador,
    $tiene_wifi, $num_ascensores, $tiene_calefaccion, $tiene_aire_acondicionado,
    $tiene_piscina, $tiene_banera, $tiene_barbacoa, $tiene_chimenea,
    $tiene_adaptacion_discapacitados, $tiene_jardin, $tiene_patio, $tiene_sala_cine,
    $tiene_secador_pelo, $imagen_principal
    ) {
        $db = new Connection();
        $conn = $db->getConnection();

        $sql = "INSERT INTO casas_vacacionales (
            id_propietario, id_comunidad, id_provincia, id_ciudad, nombre, capacidad, precio_noche,
            num_banos, num_cocinas, num_hab_individuales, num_hab_familiares,
            num_aparcamientos, num_lavadora, num_secadora, num_lavavajillas,
            num_horno, num_microondas, num_nevera, num_congelador,
            tiene_wifi, num_ascensores, tiene_calefaccion, tiene_aire_acondicionado,
            tiene_piscina, tiene_banera, tiene_barbacoa, tiene_chimenea,
            tiene_adaptacion_discapacitados, tiene_jardin, tiene_patio, tiene_sala_cine,
            tiene_secador_pelo, imagen_principal
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?,
            ?, ?, ?, ?,
            ?, ?, ?, ?,
            ?, ?, ?, ?,
            ?, ?, ?, ?,
            ?, ?, ?, ?,
            ?, ?, ?, ?,
            ?, ?
        )";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param("iiiisidiiiiiiiiiiiiiiiiiiiiiiiiis",
            $id_propietario, $id_comunidad, $id_provincia, $id_ciudad, $nombre, $capacidad, $precio_noche,
            $num_banos, $num_cocinas, $num_hab_individuales, $num_hab_familiares,
            $num_aparcamientos, $num_lavadora, $num_secadora, $num_lavavajillas,
            $num_horno, $num_microondas, $num_nevera, $num_congelador,
            $tiene_wifi, $num_ascensores, $tiene_calefaccion, $tiene_aire_acondicionado,
            $tiene_piscina, $tiene_banera, $tiene_barbacoa, $tiene_chimenea,
            $tiene_adaptacion_discapacitados, $tiene_jardin, $tiene_patio, $tiene_sala_cine,
            $tiene_secador_pelo, $imagen_principal);

        $stmt->execute();
        $id_casa = $conn->insert_id; //H
        $db->closeConnection($conn);
        return $id_casa;
    }



   /*  public function actualizarCasa($id_casa, $id_propietario, $id_comunidad, $id_provincia, 
    $id_ciudad, $nombre, $capacidad, $precio_noche, $disponible, $num_banos, $num_cocinas) {

        $db = new Connection();
        $conn = $db->getConnection();

        $sql = "UPDATE casas_vacacionales 
                SET id_propietario = ?, id_comunidad = ?, id_provincia = ?, id_ciudad = ?, 
                    nombre = ?, capacidad = ?, precio_noche = ?, disponible = ?, 
                    num_banos = ?, num_cocinas = ?
                WHERE id_casa = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiiisidiiii", 
            $id_propietario, $id_comunidad, $id_provincia, $id_ciudad,
            $nombre, $capacidad, $precio_noche, $disponible,
            $num_banos, $num_cocinas, $id_casa);

        $stmt->execute();
        $db->closeConnection($conn);
    } */
    public function actualizarCasa(
        $id_casa, $id_propietario, $id_comunidad, $id_provincia, $id_ciudad,
        $nombre, $capacidad, $precio_noche,
        $num_banos, $num_cocinas, $num_hab_individuales, $num_hab_familiares,
        $num_aparcamientos, $num_lavadora, $num_secadora, $num_lavavajillas,
        $num_horno, $num_microondas, $num_nevera, $num_congelador,
        $tiene_wifi, $num_ascensores, $tiene_calefaccion, $tiene_aire_acondicionado,
        $tiene_piscina, $tiene_banera, $tiene_barbacoa, $tiene_chimenea,
        $tiene_adaptacion_discapacitados, $tiene_jardin, $tiene_patio, $tiene_sala_cine,
        $tiene_secador_pelo, $imagen_principal
    ) {
        $db = new Connection();
        $conn = $db->getConnection();

        $sql = "UPDATE casas_vacacionales SET
            id_propietario = ?, id_comunidad = ?, id_provincia = ?, id_ciudad = ?,
            nombre = ?, capacidad = ?, precio_noche = ?,
            num_banos = ?, num_cocinas = ?, num_hab_individuales = ?, num_hab_familiares = ?,
            num_aparcamientos = ?, num_lavadora = ?, num_secadora = ?, num_lavavajillas = ?,
            num_horno = ?, num_microondas = ?, num_nevera = ?, num_congelador = ?,
            tiene_wifi = ?, num_ascensores = ?, tiene_calefaccion = ?, tiene_aire_acondicionado = ?,
            tiene_piscina = ?, tiene_banera = ?, tiene_barbacoa = ?, tiene_chimenea = ?,
            tiene_adaptacion_discapacitados = ?, tiene_jardin = ?, tiene_patio = ?, tiene_sala_cine = ?,
            tiene_secador_pelo = ?, imagen_principal = ?
            WHERE id_casa = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "iiiisidiiiiiiiiiiiiiiiiiiiiiiiiisi",
            $id_propietario, $id_comunidad, $id_provincia, $id_ciudad,
            $nombre, $capacidad, $precio_noche,
            $num_banos, $num_cocinas, $num_hab_individuales, $num_hab_familiares,
            $num_aparcamientos, $num_lavadora, $num_secadora, $num_lavavajillas,
            $num_horno, $num_microondas, $num_nevera, $num_congelador,
            $tiene_wifi, $num_ascensores, $tiene_calefaccion, $tiene_aire_acondicionado,
            $tiene_piscina, $tiene_banera, $tiene_barbacoa, $tiene_chimenea,
            $tiene_adaptacion_discapacitados, $tiene_jardin, $tiene_patio, $tiene_sala_cine,
            $tiene_secador_pelo, $imagen_principal, $id_casa
        );

        $stmt->execute();
        $db->closeConnection($conn);
    }


   
    public function eliminarCasa($id) {
        $db = new Connection();
        $conn = $db->getConnection();
        
        $sql = "DELETE FROM casas_vacacionales WHERE id_casa = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $db->closeConnection($conn);
    }
    public function eliminarImagenes($id) {
        $db = new Connection();
        $conn = $db->getConnection();
        
        $sql = "DELETE FROM imagenes WHERE id_casa = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $db->closeConnection($conn);
    }

}
?>

