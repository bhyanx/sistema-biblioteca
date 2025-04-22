<?php

// MODELO DE PRESTAMOS DE LIBROS

Class Prestamo {
    private $conn;
    private $table_name = "prestamos";

    public $id;
    public $libro_id;
    public $lector_id;
    public $fecha_prestamo;
    public $fecha_devolucion_esperada;
    public $fecha_devolucion_real;
    public $estado;

    public function __construct($db) {
        $this->conn = $db;
    }

    // FUNCION PARA TRAER TODOS LOS PRESTAMOS
    public function leerPrestamos() {
    }
}