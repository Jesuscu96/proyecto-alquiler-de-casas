
<?php
require_once("database.php");

class Reservas {
   
    public function getAll() {
        $db = new Connection();
        $conn = $db->getConnection();
        
        $sql = "SELECT * FROM reservas";
        
        $result = $conn->query($sql);
        $db->closeConnection($conn);
        //cuando devuelve un solo resultado
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    public function getReservasConDetalles() {
        $db = new Connection();
        $conn = $db->getConnection();

        $sql = "SELECT r.*, 
                u.username AS usuario, 
                u.nombre AS nombre_usuario,
                c.nombre AS nombre_casa,
                c.capacidad,
                c.precio_noche,
                com.nombre AS comunidad,
                p.nombre AS provincia,
                ci.nombre AS ciudad
            FROM reservas r
            JOIN usuarios u ON u.id_usuario = r.id_usuario
            JOIN casas_vacacionales c ON c.id_casa = r.id_casa
            JOIN comunidades com ON com.id_comunidad = c.id_comunidad
            JOIN provincias p ON p.id_provincia = c.id_provincia
            JOIN ciudades ci ON ci.id_ciudad = c.id_ciudad
            ORDER BY r.fecha_inicio DESC";

        $result = $conn->query($sql);
        $db->closeConnection($conn);

        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getReservaById($id) {
        $db = new Connection();
        $conn = $db->getConnection();
        
        $sql = "SELECT * FROM reservas WHERE id_reserva = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $db->closeConnection($conn);
        //cuando devuelve un solo resultado
        return $result ? $result->fetch_assoc() : [];
    }
    public function insertarReserva($id_usuario, $id_casa, $fecha_inicio, $fecha_fin, $total_precio, $estado = 'pendiente') {
        $db = new Connection();
        $conn = $db->getConnection();

        $sql = "INSERT INTO reservas (id_usuario, id_casa, fecha_inicio, fecha_fin, total_precio, estado)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iissds", $id_usuario, $id_casa, $fecha_inicio, $fecha_fin, $total_precio, $estado);

        $stmt->execute();
        $db->closeConnection($conn);
    }
    public function actualizarUsuario($id, $username, $nombre, $apellidos, $edad, $email, $rol, $telefono) {
        $db = new Connection();
        $conn = $db->getConnection();

        $sql = "UPDATE usuarios SET username = ?, nombre = ?, apellidos = ?, edad = ?, email = ?, rol = ?, telefono = ? WHERE id_usuario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssisssi", $username, $nombre, $apellidos, $edad, $email, $rol, $telefono, $id);

        $stmt->execute();

        $db->closeConnection($conn);
    }

    
    public function eliminarReserva($id) {
        $db = new Connection();
        $conn = $db->getConnection();
        
        $sql = "DELETE FROM reservas WHERE id_reserva = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $db->closeConnection($conn);
    }

}
?>

