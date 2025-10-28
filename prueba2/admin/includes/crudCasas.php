
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
    public function insertarCasa($id_propietario, $id_comunidad, $id_provincia, $id_ciudad, 
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
    }


    public function actualizarCasa($id_casa, $id_propietario, $id_comunidad, $id_provincia, 
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

}
?>

