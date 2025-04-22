<?php

// CONEXION DE MI BASE DE DATOS LOCAL gestion_biblioteca
class Database{
    private $host = 'localhost';
    private $db_name = 'gestion_biblioteca';
    private $username = 'root';
    private $password = '';
    private $conn;

    public function getConnection(){
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "Error al conextar a la base de datos: " .$exception->getMessage();
        }
        
        return $this->conn;
    }
}