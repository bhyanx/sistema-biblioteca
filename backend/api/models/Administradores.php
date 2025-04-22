<?php 

// MODELO DE USUARIOS ADMINISTRADORES

Class Administrador {
    private $conn;
    private $table_name = "administradores";

    public $id;
    public $nombre;
    public $email;
    public $contrasena;
    public $fecha_registro;

    public function __construct($db)
    {
        $this -> conn = $db;
    }

    //FUNCION PARA TRAER TODOS LOS ADMINISTRADORES
    public function leerAdministradores(){
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    //FUNCION PARA TRAER UN ADMINISTRADOR POR ID
    public function leerAdministradorPorId(){
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->nombre = $row['nombre'];
            $this->email = $row['email'];
            $this->contrasena = $row['contrasena'];
            $this->fecha_registro = $row['fecha_registro'];
            return true;
        }
        return false;
    }


    //FUNCION PARA CREAR USUARIO ADMINISTRADOR
    public function crearAdministrador(){
        $query = "INSERT INTO " . $this->table_name . " SET nombre = :nombre,
        email = :email,
        contrasena = :contrasena";

        $stmt = $this->conn->prepare($query);

        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->contrasena = htmlspecialchars(strip_tags($this->contrasena));

        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':contrasena', $this->contrasena);

        if ($stmt->execute()){
            return true;
        }
        return false;
    }

    //FUNCION PARA ACTUALIZAR USUARIO ADMINISTRADOR
    public function actualizarAdministrador(){
        //CONDICIONALES PARA VALIDAR LOS DATOS REQUERIDOS
        if(empty($this->nombre) || empty($this->email) || empty($this->contrasena)){
            throw new Exception("Nombre, email y contrasena son campos requeridos");
        }

        $query = "UPDATE " . $this->table_name . "
        SET nombre = :nombre,
        email = :email,
        contrasena = :contrasena
        WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->contrasena = htmlspecialchars(strip_tags($this->contrasena));

        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':contrasena', $this->contrasena);

        try {
            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    return [
                        'success' => true,
                        'message' => 'Administrador actualizado correctamente',
                        'rows_affected' => $stmt->rowCount()
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'No se encontraron registros para actualizar',
                        'rows_affected' => $stmt->rowCount()
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'Error al actualizar el administrador',
                'error' => $stmt->errorInfo()
            ];
        }catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage());
        }
    }

    public function borrarAdministrador(){
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
