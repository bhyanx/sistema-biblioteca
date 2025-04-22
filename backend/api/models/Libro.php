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
    public function leerLibroPorId(){
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->titulo = $row['titulo'];
            $this->autor = $row['autor'];
            $this->editorial = $row['editorial'];
            $this->anio_publicacion = $row['anio_publicacion'];
            $this->categoria_id = $row['categoria_id'];
            $this->estado_uso = $row['estado_uso'];
            $this->isbn = $row['isbn'];
            $this->copias_disponibles = $row['copias_disponibles'];
            $this->disponible = $row['disponible'];
            $this->imagen_portada = $row['imagen_portada'];
            $this->fecha_registro = $row['fecha_registro'];
            return true;
        }
        return false;
    }

    //FUNCION PARA CREAR LIBRO
    public function crearLibro(){
        $query = "INSERT INTO " . $this->table_name . " SET titulo = :titulo,
        autor = :autor,
        editorial = :editorial,
        anio_publicacion = :anio_publicacion,
        categoria_id = :categoria_id,
        estado_uso = :estado_uso,
        isbn = :isbn,
        copias_disponibles = :copias_disponibles,
        disponible = :disponible,
        imagen_portada = :imagen_portada,
        fecha_registro = :fecha_registro";

        $stmt = $this->conn->prepare($query);

        $this->titulo = htmlspecialchars(strip_tags($this->titulo));
        $this->autor = htmlspecialchars(strip_tags($this->autor));
        $this->editorial = htmlspecialchars(strip_tags($this->editorial));
        $this->anio_publicacion = htmlspecialchars(strip_tags($this->anio_publicacion));
        $this->categoria_id = htmlspecialchars(strip_tags($this->categoria_id));
        $this->estado_uso = htmlspecialchars(strip_tags($this->estado_uso));
        $this->isbn = htmlspecialchars(strip_tags($this->isbn));
        $this->copias_disponibles = htmlspecialchars(strip_tags($this->copias_disponibles));
        $this->disponible = htmlspecialchars(strip_tags($this->disponible));
        $this->imagen_portada = htmlspecialchars(strip_tags($this->imagen_portada));
        $this->fecha_registro = htmlspecialchars(strip_tags($this->fecha_registro));

        $stmt->bindParam(':titulo', $this->titulo);
        $stmt->bindParam(':autor', $this->autor);
        $stmt->bindParam(':editorial', $this->editorial);
        $stmt->bindParam(':anio_publicacion', $this->anio_publicacion);
        $stmt->bindParam(':categoria_id', $this->categoria_id);
        $stmt->bindParam(':estado_uso', $this->estado_uso);
        $stmt->bindParam(':isbn', $this->isbn);
        $stmt->bindParam(':copias_disponibles', $this->copias_disponibles);
        $stmt->bindParam(':disponible', $this->disponible);
        $stmt->bindParam(':imagen_portada', $this->imagen_portada);
        $stmt->bindParam(':fecha_registro', $this->fecha_registro);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    //FUNCION PARA ACTUALIZAR LIBRO
    public function actualizarLibro(){

        if(empty($this->titulo) || empty($this->autor) || empty($this->editorial) || empty($this->anio_publicacion) || empty($this->categoria_id) || empty($this->estado_uso) || empty($this->isbn) || empty($this->copias_disponibles) || empty($this->disponible) || empty($this->imagen_portada)) {
            throw new Exception("Todos los campos son requeridos");
        }

        $query = "UPDATE " . $this->table_name . "
        SET titulo = :titulo,
        autor = :autor,
        editorial = :editorial,
        anio_publicacion = :anio_publicacion,
        categoria_id = :categoria_id,
        estado_uso = :estado_uso,
        isbn = :isbn,
        copias_disponibles = :copias_disponibles,
        disponible = :disponible,
        imagen_portada = :imagen_portada,
        fecha_registro = :fecha_registro
        WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->titulo = htmlspecialchars(strip_tags($this->titulo));
        $this->autor = htmlspecialchars(strip_tags($this->autor));
        $this->editorial = htmlspecialchars(strip_tags($this->editorial));
        $this->anio_publicacion = htmlspecialchars(strip_tags($this->anio_publicacion));
        $this->categoria_id = htmlspecialchars(strip_tags($this->categoria_id));
        $this->estado_uso = htmlspecialchars(strip_tags($this->estado_uso));
        $this->isbn = htmlspecialchars(strip_tags($this->isbn));
        $this->copias_disponibles = htmlspecialchars(strip_tags($this->copias_disponibles));
        $this->disponible = htmlspecialchars(strip_tags($this->disponible));
        $this->imagen_portada = htmlspecialchars(strip_tags($this->imagen_portada));
        $this->fecha_registro = htmlspecialchars(strip_tags($this->fecha_registro));
        
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':titulo', $this->titulo);
        $stmt->bindParam(':autor', $this->autor);
        $stmt->bindParam(':editorial', $this->editorial);
        $stmt->bindParam(':anio_publicacion', $this->anio_publicacion);
        $stmt->bindParam(':categoria_id', $this->categoria_id);
        $stmt->bindParam(':estado_uso', $this->estado_uso);
        $stmt->bindParam(':isbn', $this->isbn);
        $stmt->bindParam(':copias_disponibles', $this->copias_disponibles);
        $stmt->bindParam(':disponible', $this->disponible);
        $stmt->bindParam(':imagen_portada', $this->imagen_portada);
        $stmt->bindParam(':fecha_registro', $this->fecha_registro);

        try {
            if ($stmt->execute()){
                if($stmt->rowCount() > 0){
                    return [
                        "success" => true,
                        "message" => "Libro actualizado correctamente",
                        "rows_affected" => $stmt->rowCount() 
                    ];
                }
            }

            return [
                "success" => false,
                "message" => "No se encontraron cambios para actualizar",
                "error" => $stmt->errorInfo()
            ];

        }catch (Exception $e) {
            throw new Exception("Error al actualizar el libro: " . $e->getMessage());
        }
    }

    //FUNCION PARA ELIMINAR LIBRO
    public function eliminarLibro(){
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