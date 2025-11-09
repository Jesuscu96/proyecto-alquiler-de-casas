
<?php
require_once("database.php");

class Ubicacion {
    public function getAllComunidades() {
        $db = new Connection();
        $conn = $db->getConnection();

        $sql = "SELECT com.* FROM comunidades com ORDER BY com.nombre;";

        $result = $conn->query($sql);
        $db->closeConnection($conn);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    public function getAllProvincias() {
        $db = new Connection();
        $conn = $db->getConnection();

        $sql = "SELECT p.*, com.nombre AS comunidad FROM provincias p LEFT JOIN comunidades com ON com.id_comunidad = p.id_comunidad ORDER BY p.nombre;";

        $result = $conn->query($sql);
        $db->closeConnection($conn);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    public function getAllCiudades() {
        $db = new Connection();
        $conn = $db->getConnection();

        $sql = "SELECT ci.*, p.nombre AS provincia, com.nombre AS comunidad FROM ciudades ci LEFT JOIN provincias p ON p.id_provincia = ci.id_provincia LEFT JOIN comunidades com ON com.id_comunidad = p.id_comunidad ORDER BY ci.nombre;";

        $result = $conn->query($sql);
        $db->closeConnection($conn);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getProvinciasByComunidad($id_comunidad) {
        $db = new Connection();
        $conn = $db->getConnection();
        $sql = "SELECT * FROM provincias WHERE id_comunidad = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_comunidad);
        $stmt->execute();
        $result = $stmt->get_result();
        $db->closeConnection($conn);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getCiudadesByProvincia($id_provincia) {
        $db = new Connection();
        $conn = $db->getConnection();
        $sql = "SELECT * FROM ciudades WHERE id_provincia = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_provincia);
        $stmt->execute();
        $result = $stmt->get_result();
        $db->closeConnection($conn);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }


    
    /* public function insertarCasa($id_propietario, $id_comunidad, $id_provincia, $id_ciudad, 
    $nombre, $capacidad, $precio_noche, $disponible, $num_banos, $num_cocinas, $telefono) {

        $db = new Connection();
        $conn = $db->getConnection();

        $sql = "INSERT INTO casas_vacacioneles (id_propietario, id_comunidad, id_provincia, id_ciudad,
                nombre, capacidad, precio_noche, disponible, num_banos, num_cocinas, telefono)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiiisidiiii", $id_propietario, $id_comunidad, $id_provincia, $id_ciudad, 
        $nombre, $capacidad, $precio_noche, $disponible, $num_banos, $num_cocinas, $telefono);

        $stmt->execute();
        $db->closeConnection($conn);
    }

   public function actualizarCasa($id_casa, $id_propietario, $id_comunidad, $id_provincia, $id_ciudad,
    $nombre, $capacidad, $precio_noche, $disponible, $num_banos, $num_cocinas, $telefono) {
    
        $db = new Connection();
        $conn = $db->getConnection();

        $sql = "UPDATE casa 
                SET id_propietario = ?, id_comunidad = ?, id_provincia = ?, id_ciudad = ?, 
                    nombre = ?, capacidad = ?, precio_noche = ?, disponible = ?, 
                    num_banos = ?, num_cocinas = ?, telefono = ?
                WHERE id_casa = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiiisidiiiii", $id_propietario, $id_comunidad, $id_provincia, $id_ciudad,
            $nombre, $capacidad, $precio_noche, $disponible, $num_banos, $num_cocinas, $telefono, $id_casa);

        $stmt->execute();
        $db->closeConnection($conn);
    }


   
    public function eliminarCasa($id) {
        $db = new Connection();
        $conn = $db->getConnection();
        
        $sql = "DELETE FROM casa_vacacionles WHERE id_casa = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $db->closeConnection($conn);
    } */

}
?>

