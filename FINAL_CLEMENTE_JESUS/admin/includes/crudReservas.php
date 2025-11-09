
<?php
require_once("database.php");

class Reservas {
   
    // public function getAll() {
    //     $db = new Connection();
    //     $conn = $db->getConnection();
        
    //     $sql = "SELECT * FROM reservas";
        
    //     $result = $conn->query($sql);
    //     $db->closeConnection($conn);
    //     //cuando devuelve un solo resultado
    //     return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    // }
    public function getAll() {
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

        $sql = "SELECT r.*, 
                    u.username AS usuario, 
                    c.nombre AS nombre_casa
                FROM reservas r
                JOIN usuarios u ON u.id_usuario = r.id_usuario
                JOIN casas_vacacionales c ON c.id_casa = r.id_casa
                WHERE r.id_reserva = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        $db->closeConnection($conn);

        return $result ? $result->fetch_assoc() : [];
    }


    /* public function getReservaById($id) {
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
    } */
    public function insertarReserva($id_usuario, $id_casa, $fecha_inicio, $fecha_fin, $total_precio, $estado) {
        $db = new Connection();
        $conn = $db->getConnection();

        $sql = "INSERT INTO reservas (id_usuario, id_casa, fecha_inicio, fecha_fin, total_precio, estado)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iissds", $id_usuario, $id_casa, $fecha_inicio, $fecha_fin, $total_precio, $estado);

        $stmt->execute();
        $db->closeConnection($conn);
    }
    public function actualizarReserva($id_reserva, $id_usuario, $id_casa, $fecha_inicio, $fecha_fin, $total_precio, $estado) {
        $db = new Connection();
        $conn = $db->getConnection();

        // 1️ Comprobar si hay conflicto con otras reservas confirmadas
        $sql_conflicto = "SELECT COUNT(*) AS conflictos
                        FROM reservas
                        WHERE id_casa = ?
                            AND id_reserva != ?
                            AND estado = 'confirmada'
                            AND (
                                (? BETWEEN fecha_inicio AND fecha_fin)
                            OR (? BETWEEN fecha_inicio AND fecha_fin)
                            OR (fecha_inicio BETWEEN ? AND ?)
                            )";

        $stmt_conf = $conn->prepare($sql_conflicto);
        $stmt_conf->bind_param("iissss", $id_casa, $id_reserva, $fecha_inicio, $fecha_fin, $fecha_inicio, $fecha_fin);
        $stmt_conf->execute();
        $conflictos = $stmt_conf->get_result()->fetch_assoc()['conflictos'];

        if ($conflictos > 0) {
            $db->closeConnection($conn);
            return false; //  Hay conflicto de fechas
        }

        // 2️ Si no hay conflicto, actualizar la reserva
        $sql = "UPDATE reservas 
                SET id_usuario = ?, 
                    id_casa = ?, 
                    fecha_inicio = ?, 
                    fecha_fin = ?, 
                    total_precio = ?, 
                    estado = ?
                WHERE id_reserva = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iissdsi", $id_usuario, $id_casa, $fecha_inicio, $fecha_fin, $total_precio, $estado, $id_reserva);
        $stmt->execute();

        $db->closeConnection($conn);

        return true; //  Actualización exitosa
    }

    /* public function actualizarReserva($id_reserva, $fecha_inicio, $fecha_fin, $total_precio, $estado) {
        $db = new Connection();
        $conn = $db->getConnection();

        $sql = "UPDATE reservas 
                SET fecha_inicio = ?, 
                    fecha_fin = ?, 
                    total_precio = ?, 
                    estado = ?
                WHERE id_reserva = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdsi",  $fecha_inicio, $fecha_fin, $total_precio, $estado, $id_reserva);

        $stmt->execute();

        $db->closeConnection($conn);
    } */
    public function getReservasPorUsuario($id_usuario) {
        $db = new Connection();
        $conn = $db->getConnection();

        $sql = "SELECT r.*, c.nombre AS nombre_casa, c.precio_noche
                FROM reservas r
                JOIN casas_vacacionales c ON c.id_casa = r.id_casa
                WHERE r.id_usuario = ?
                ORDER BY r.fecha_inicio DESC";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $result = $stmt->get_result();

        $db->closeConnection($conn);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    public function cancelarReserva($id_reserva, $id_usuario) {
        $db = new Connection();
        $conn = $db->getConnection();

        $sql = "UPDATE reservas
                SET estado = 'cancelada'
                WHERE id_reserva = ? AND id_usuario = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $id_reserva, $id_usuario);
        $stmt->execute();

        $db->closeConnection($conn);
    }
    public function getTotalReservas() {
        $db = new Connection();
        $conn = $db->getConnection();
        
        $sql = "SELECT COUNT(id_reserva) AS total_reservas 
                FROM reservas";
        
        $result = $conn->query($sql);
        $db->closeConnection($conn);
        
        return $result ? $result->fetch_assoc()['total_reservas'] : 0;
    }


    public function getCantidadReservasConfirmadas() {
        $db = new Connection();
        $conn = $db->getConnection();
        
        $sql = "SELECT COUNT(id_reserva) AS total_confirmadas 
                FROM reservas 
                WHERE estado = 'confirmada'";
        
        $result = $conn->query($sql);
        $db->closeConnection($conn);
        
        return $result ? $result->fetch_assoc()['total_confirmadas'] : 0;
    }

    public function getCantidadReservasCanceladas() {
        $db = new Connection();
        $conn = $db->getConnection();
        
        $sql = "SELECT COUNT(id_reserva) AS total_canceladas 
                FROM reservas 
                WHERE estado = 'cancelada'";
        
        $result = $conn->query($sql);
        $db->closeConnection($conn);
        
        return $result ? $result->fetch_assoc()['total_canceladas'] : 0;
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

