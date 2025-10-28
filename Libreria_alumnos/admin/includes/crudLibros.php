<?php
require_once("database.php");
class Libros {
    public function getPopulares($limite = 4) {
        $db = new Connection();
        $conn = $db->getConnection();
        
        $sql = "SELECT * FROM libros ORDER BY visitas DESC LIMIT ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $limite);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $db->closeConnection($conn);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    public function getRecientes($limite = 4) {
        $db = new Connection();
        $conn = $db->getConnection();
        
        $sql = "SELECT * FROM libros ORDER BY fecha DESC LIMIT ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $limite);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $db->closeConnection($conn);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    public function getByCategoria($id_categoria) {
        $db = new Connection();
        $conn = $db->getConnection();
        
        $sql = "SELECT * FROM libros WHERE id_categoria = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_categoria);
        $stmt->execute();
        $result = $stmt->get_result();

        $db->closeConnection($conn);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getLibroById($id) {
        $db = new Connection();
        $conn = $db->getConnection();
        
        $sql = "SELECT libros.*, categorias.categoria FROM libros
        LEFT JOIN categorias ON libros.id_categoria = categorias.id_categoria
        WHERE  libros.id_libro = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $db->closeConnection($conn);
        //cuando devuelve un solo resultado
        return $result ? $result->fetch_assoc() : [];
    }
    public function getAll() {
        $db = new Connection();
        $conn = $db->getConnection();
        
        $sql = "SELECT libros.*, categorias.categoria FROM libros
        LEFT JOIN categorias ON libros.id_categoria = categorias.id_categoria
        ORDER BY titulo";
        
        $result = $conn->query($sql);
        $db->closeConnection($conn);
        
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function sumarVisita($id) {
        $db = new Connection();
        $conn = $db->getConnection();
        
        $sql = "UPDATE libros SET visitas = visitas + 1 WHERE id_libro = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $db->closeConnection($conn);
    }
    public function insertarLibro($titulo, $autor, $id_categoria, $precio, $fecha, $portada) {
        $db = new Connection();
        $conn = $db->getConnection();
        
        $sql = "INSERT INTO libros (titulo, autor, id_categoria, precio, fecha, portada, visitas) VALUES (?, ?, ?, ?, ?, ?, 0)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssidss", $titulo, $autor, $id_categoria, $precio, $fecha, $portada);
        $stmt->execute();

        $db->closeConnection($conn);
    }
    public function actualizarLibro($id, $titulo, $autor, $id_categoria, $precio, $fecha, $portada) {
        $db = new Connection();
        $conn = $db->getConnection();
        
        $sql = "UPDATE libros SET titulo=?, autor=?, id_categoria=?, precio=?, fecha=?, portada=? WHERE id_libro=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssidssi", $titulo, $autor, $id_categoria, $precio, $fecha, $portada, $id);
        $stmt->execute();

        $db->closeConnection($conn);
    }
    public function eliminarLibro($id) {
        $db = new Connection();
        $conn = $db->getConnection();
        
        $sql = "DELETE FROM libros WHERE id_libro = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $db->closeConnection($conn);
    }
        
}
?>


