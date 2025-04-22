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
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY fecha_prestamo DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // FUNCION PARA TRAER UN PRESTAMO POR ID
    public function leerPrestamoPorId() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row){
            $this->libro_id = $row['libro_id'];
            $this->lector_id = $row['lector_id'];
            $this->fecha_prestamo = $row['fecha_prestamo'];
            $this->fecha_devolucion_esperada = $row['fecha_devolucion_esperada'];
            $this->fecha_devolucion_real = $row['fecha_devolucion_real'];
            $this->estado = $row['estado'];
            return true;
        }
        return false;
    }

    // FUNCION PARA CREAR UN NUEVO PRESTAMO
    public function crearPrestamo() {
        $query = "INSERT INTO " . $this->table_name . " SET libro_id = :libro_id, lector_id = :lector_id, fecha_prestamo = :fecha_prestamo, fecha_devolucion_esperada = :fecha_devolucion_esperada, estado = :estado";
        
        $stmt = $this->conn->prepare($query);

        $this->libro_id = htmlspecialchars(strip_tags($this->libro_id));
        $this->lector_id = htmlspecialchars(strip_tags($this->lector_id));
        $this->fecha_prestamo = htmlspecialchars(strip_tags($this->fecha_prestamo));
        $this->fecha_devolucion_esperada = htmlspecialchars(strip_tags($this->fecha_devolucion_esperada));
        $this->estado = htmlspecialchars(strip_tags($this->estado));

        $stmt->bindParam(':libro_id', $this->libro_id);
        $stmt->bindParam(':lector_id', $this->lector_id);
        $stmt->bindParam(':fecha_prestamo', $this->fecha_prestamo);
        $stmt->bindParam(':fecha_devolucion_esperada', $this->fecha_devolucion_esperada);
        $stmt->bindParam(':estado', $this->estado);

        if($stmt->execute()){
            return true;
        }
        return false;
    }

    // FUNCION PARA ACTUALIZAR UN PRESTAMO
    public function actualizarPrestamo() {

        if(empty($this->libro_id) || empty($this->lector_id) || empty($this->fecha_prestamo) || empty($this->fecha_devolucion_esperada) || empty($this->estado)) {
            throw new Exception("Todos los campos son obligatorios.");
        }

        $query = "UPDATE " . $this->table_name . " SET libro_id = :libro_id, lector_id = :lector_id, fecha_prestamo = :fecha_prestamo, fecha_devolucion_esperada = :fecha_devolucion_esperada, estado = :estado WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->libro_id = htmlspecialchars(strip_tags($this->libro_id));
        $this->lector_id = htmlspecialchars(strip_tags($this->lector_id));
        $this->fecha_prestamo = htmlspecialchars(strip_tags($this->fecha_prestamo));
        $this->fecha_devolucion_esperada = htmlspecialchars(strip_tags($this->fecha_devolucion_esperada));
        $this->estado = htmlspecialchars(strip_tags($this->estado));

        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':libro_id', $this->libro_id);
        $stmt->bindParam(':lector_id', $this->lector_id);
        $stmt->bindParam(':fecha_prestamo', $this->fecha_prestamo);
        $stmt->bindParam(':fecha_devolucion_esperada', $this->fecha_devolucion_esperada);
        $stmt->bindParam(':estado', $this->estado);

        try{
            if ($stmt->execute()) {
                if($stmt->rowCount() > 0){
                    return [
                        'success' => true,
                        'message' => 'Prestamo actualizado correctamente',
                        'rows_affected' => $stmt->rowCount()
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'No se pudo actualizar el prestamo',
                'error' => $stmt->errorInfo()
            ];

        }catch (Exception $e) {
            throw new Exception("Error al actualizar el prestamo: " . $e->getMessage());
        }
    }

    // FUNCION PARA ELIMINAR UN PRESTAMO
    public function eliminarPrestamo() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()){
            return true;
        }
        return false;
    }
}