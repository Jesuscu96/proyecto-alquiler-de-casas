
<?php
require_once("database.php");

class Usuarios {
   
    public function getAll() {
        $db = new Connection();
        $conn = $db->getConnection();
        
        $sql = "SELECT * FROM usuarios";
        
        $result = $conn->query($sql);
        $db->closeConnection($conn);
        //cuando devuelve un solo resultado
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    public function getUsuarioById($id) {
        $db = new Connection();
        $conn = $db->getConnection();
        
        $sql = "SELECT * FROM usuarios WHERE id_usuario = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $db->closeConnection($conn);
        //cuando devuelve un solo resultado
        return $result ? $result->fetch_assoc() : [];
    }
    public function insertarUsuario($username, $nombre, $apellidos, $edad, $email, $password, $rol, $telefono) {
        $db = new Connection();
        $conn = $db->getConnection();
        
        $sql = "INSERT INTO usuarios (username, nombre, apellidos, edad, email, password, rol, telefono)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt->bind_param("sssissss", $username, $nombre, $apellidos, $edad, $email, $hash, $rol, $telefono);

        $stmt->execute();

        $db->closeConnection($conn);
    }
    public function actualizarUsuario($id, $nombre, $apellidos, $email, $username) {
        $db = new Connection();
        $conn = $db->getConnection();
        
        $sql = "UPDATE usuarios SET nombre=?, apellidos=?, email=?, username=? WHERE id_usuario=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $nombre, $apellidos, $email, $username, $id);
        $stmt->execute();

        $db->closeConnection($conn);
    }
    public function actualizarUsuario($id, $username, $nombre, $apellidos, $aded, $email, $rol, $telefono) {
        $db = new Connection();
        $conn = $db->getConnection();
        
        $sql = "UPDATE usuarios SET username = ?, nombre = ?, apellidos = ?, edad = ?, email = ?,  rol = ?, telefono = ? WHERE id_usuario = ?";      
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssisssi", $username, $nombre, $apellidos, $edad, $email, $rol, $telefono, $id);
        $stmt->execute();

        $db->closeConnection($conn);
    }

    public function actualizarPassword($id, $password) {
        $db = new Connection();
        $conn = $db->getConnection();

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET password=? WHERE id_usuario=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $hash, $id);
        $stmt->execute();

        $db->closeConnection($conn);
    }
    public function eliminarUsuario($id) {
        $db = new Connection();
        $conn = $db->getConnection();
        
        $sql = "DELETE FROM usuarios WHERE id_usuario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $db->closeConnection($conn);
    }

}
?>

