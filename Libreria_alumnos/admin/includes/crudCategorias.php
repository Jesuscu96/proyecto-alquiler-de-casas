<?php
require_once("database.php");

class Categorias {
    public function showCategorias() {
        try {
            $sqlConnection = new Connection();
            $mySQL = $sqlConnection->getConnection();
            
            $sql = "SELECT * FROM categorias";
            $result = $mySQL->query($sql);
            
            $sqlConnection->closeConnection($mySQL);

            return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

        } catch (Exception $e) {
            return [];
        }
    }
    public function getById($id) {
        $db = new Connection();
        $conn = $db->getConnection();
        
        $sql = "SELECT * FROM categorias WHERE id_categoria = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $db->closeConnection($conn);
        return $result->fetch_assoc();
    }
    
    public function insertarCategoria($nombre) {
        $db = new Connection();
        $conn = $db->getConnection();
        
        $sql = "INSERT INTO categorias (categoria) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $nombre);
        $stmt->execute();
        
        $db->closeConnection($conn);
    }
    
    //Actualizar una categorÃ­a
    public function actualizarCategoria($id, $nombre) {
        $db = new Connection();
        $conn = $db->getConnection();
        
        $sql = "UPDATE categorias SET categoria = ? WHERE id_categoria = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $nombre, $id);
        $stmt->execute();

        $db->closeConnection($conn);
    }

    //Eliminar una categorÃ­a
    public function eliminarCategoria($id) {
        $db = new Connection();
        $conn = $db->getConnection();
        
        $sql = "DELETE FROM categorias WHERE id_categoria = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $db->closeConnection($conn);
    }
}
?>