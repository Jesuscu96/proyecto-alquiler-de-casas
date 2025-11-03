<?php
require_once("database.php");

class Sessions {
    public function comprobarCredenciales($username, $password) {
        $db = new Connection();
        $conn = $db->getConnection();
        
        $sql = "SELECT * FROM usuarios WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $usuario = $result->fetch_assoc();
        $db->closeConnection($conn);
        
        if ($usuario && password_verify($password, $usuario['password'])) {
            return $usuario;
        }

        return null;
    }
    private function ensureSessionStarted() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }
    public function crearSesion($usuario) {
        $this->ensureSessionStarted();
        $_SESSION['usuario'] = $usuario;
    }
    
    public function comprobarSesion() {
        $this->ensureSessionStarted();
        return isset($_SESSION['usuario']);
    }
    
    public function cerrarSesion() {
        $this->ensureSessionStarted();
        session_destroy();
    }
    
}
?>