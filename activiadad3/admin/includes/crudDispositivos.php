
<?php
require_once("database.php");

class Dispositivos {
   
    public function getAll() {
        $db = new Connection();
        $conn = $db->getConnection();
        
        $sql = "SELECT * FROM dispositivos";
        
        $result = $conn->query($sql);
        $db->closeConnection($conn);
        //cuando devuelve un solo resultado
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    public function getDispositivoById($id) {
        $db = new Connection();
        $conn = $db->getConnection();
        
        $sql = "SELECT * FROM dispositivos WHERE id_dispositivo = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $db->closeConnection($conn);
        //cuando devuelve un solo resultado
        return $result ? $result->fetch_assoc() : [];
    }
    public function insertarDispositivo($marca, $modelo, $num_serie) {
        $db = new Connection();
        $conn = $db->getConnection();
        
        $sql = "INSERT INTO dispositivos (marca, modelo, num_serie)
        VALUES (?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt->bind_param("sss", $marca, $modelo, $num_serie);

        $stmt->execute();

        $db->closeConnection($conn);
    }
    public function actualizarDispositivo($id, $marca, $modelo, $num_serie) {
        $db = new Connection();
        $conn = $db->getConnection();
        
        $sql = "UPDATE dispositivos SET marca = ?, modelo = ?,num_serie = ? WHERE id_dispositivo = ?";      
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $marca, $modelo, $num_serie, $id);
        $stmt->execute();

        $db->closeConnection($conn);
    }


    public function eliminarDispositivo($id) {
        $db = new Connection();
        $conn = $db->getConnection();
        
        $sql = "DELETE FROM dispositivos WHERE id_dispositivo = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $db->closeConnection($conn);
    }

}
?>

