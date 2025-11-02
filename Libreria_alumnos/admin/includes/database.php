<?php
class Connection {
    private $host = "localhost";
    private $user = "jesusclemente_root";
    private $pass = "jesusDAM02";
    private $dbname = "jesusclemente_libreria";

public function getConnection() {
    $conn = new mysqli($this->host, $this->user, $this->pass, $this->dbname);
    if ($conn->connect_error) {
        die("Error de conexion: " . $conn->connect_error);
    }

    $conn->set_charset("utf8");
    return $conn;
    }

    public function closeConnection($conn) {
        $conn->close();
    }
}
?>