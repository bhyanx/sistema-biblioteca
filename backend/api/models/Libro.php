<?php

// MODELO DE LIBROS 

Class Libro{
    private $conn;
    private $table_name = "libros";

    public $id;
    public $titulo;
    public $autor;
    public $editorial;
    public $anio_publicacion;
    public $categoria_id;
    public $estado_uso;
    public $isbn;
    public $copias_disponibles;
    public $disponible;
    public $imagen_portada;
    public $fecha_registro;
    
    public function __construct($db)
    {
        $this -> conn = $db;
    }
    
    //FUNCION PARA TRAER TODOS LOS LIBROS
    public function leerLibros(){
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY fecha_registro DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    //FUNCION PARA TRAER UN LIBRO POR ID
    
    
}