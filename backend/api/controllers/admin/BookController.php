<?php

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../models/Libro.php';

class LibroController{
    private $db;
    private $libro;
    private $base_url;
    private $upload_dir;

    public function __construct(){
        $database = new Database();
        $db = $database->getConnection();
        $this->libro = new Libro($db);

        $this->base_url = 'http://localhost/sistema-biblioteca';
        $this->upload_dir = __DIR__ . '/../../uploads/portadas/';
        
        if (!file_exists($this->upload_dir)) {
            mkdir($this->upload_dir, 0777, true);
        }

        header("content-type: application/json; charset=UTF-8");
    }

    private function handleImageUrl($imageUrl){
        
        if (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            return $imageUrl;
        }

        if (strpos($imageUrl, 'uploads/portadas/') !== false) {
            return $imageUrl;
        }

        if (!empty($imageUrl)) {
            return $this->base_url . '/backend/uploads/portadas/' . $imageUrl;
        }

        return null;
    }

    private function getFullImageUrl($imageName){
        if (empty($imageName)) return null;
        return $this->handleImageUrl($imageName);
    }

    public function handleRequest(){
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = explode('/', $uri);

        $requestMethod = $_SERVER["REQUEST_METHOD"];

        $id = null;
        if (isset($uri[6]) && is_numeric($uri[6])) {
            $id = (int) $uri[6];
        }

        switch ($requestMethod) {
            case 'GET':
                if ($id) {
                    $this->leerLibroPorId($id);
                } else {
                    $this->leerLibros();
                }
                break;
            case 'POST':
                $this->crearLibro();
                break;
            case 'PUT':
                if ($id) {
                    $this->actualizarLibro($id);
                } else {
                    $this->notFoundResponse();
                }
                break;
            default:
                $this->notFoundResponse();
                break;
        }
    }

    private function leerLibros(){
        $stmt = $this->libro->leerLibros();
        $num = $stmt->rowCount();

        if ($num > 0) {
            $libros_arr = array();
            $libros_arr["libros"] = array();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);

                $libro_item = array(
                    "id" => $id,
                    "titulo" => $titulo,
                    "autor" => $autor,
                    "editorial" => $editorial,
                    "anio_publicacion" => $anio_publicacion,
                    "categoria_id" => $categoria_id,
                    "estado_uso" => $estado_uso,
                    "isbn" => $isbn,
                    "copias_disponibles" => $copias_disponibles,
                    "disponible" => $disponible,
                    "imagen_portada" => $imagen_portada,
                    "fecha_registro" => $fecha_registro
                );

                array_push($libros_arr["libros"], $libro_item);
            }

            http_response_code(200);
            echo json_encode($libros_arr);
        } else {
            http_response_code(404);
            echo json_encode(array("libros" => "No se encontraron libros."));
        }
    }

    private function leerLibroPorId($id){

        $this->libro->id = $id;

        if ($this->libro->leerLibroPorId()){
            $libros_arr = array(
                "id" => $this->libro->id,
                "titulo" => $this->libro->titulo,
                "autor" => $this->libro->autor,
                "editorial" => $this->libro->editorial,
                "anio_publicacion" => $this->libro->anio_publicacion,
                "categoria_id" => $this->libro->categoria_id,
                "estado_uso" => $this->libro->estado_uso,
                "isbn" => $this->libro->isbn,
                "copias_disponibles" => $this->libro->copias_disponibles,
                "disponible" => $this->libro->disponible,
                "imagen_portada" => $this->getFullImageUrl($this->libro->imagen_portada),
                "fecha_registro" => $this->libro->fecha_registro
            );

            http_response_code(200);
            echo json_encode($libros_arr);
        } else {
            http_response_code(404);
            echo json_encode(array("libro" => "No se encontró el libro."));
        }
    }

    private function crearLibro(){
        $data = json_decode(file_get_contents("php://input"));

        if (
            !empty($data->titulo) &&
            !empty($data->autor) &&
            !empty($data->editorial) &&
            !empty($data->anio_publicacion) &&
            !empty($data->categoria_id) &&
            !empty($data->estado_uso) &&
            !empty($data->isbn) &&
            !empty($data->copias_disponibles) &&
            !empty($data->disponible) &&
            !empty($data->imagen_portada) &&
            !empty($data->fecha_registro)
        ){
            $this->libro->titulo = $data->titulo;
            $this->libro->autor = $data->autor;
            $this->libro->editorial = $data->editorial;
            $this->libro->anio_publicacion = $data->anio_publicacion;
            $this->libro->categoria_id = $data->categoria_id;
            $this->libro->estado_uso = $data->estado_uso;
            $this->libro->isbn = $data->isbn;
            $this->libro->copias_disponibles = $data->copias_disponibles;
            $this->libro->disponible = $data->disponible;
            $this->libro->imagen_portada = $data->imagen_portada;
            $this->libro->fecha_registro = date('Y-m-d H:i:s');

            if ($this->libro->crearLibro()){
                http_response_code(201);
                echo json_encode(array("message" => "Libro creado."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "No se pudo crear el libro."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "No se puede crear el libro. Datos incompletos."));
        }
    }

    private function actualizarLibro($id){
        
        try {
            $rawInput = file_get_contents('php://input');
            $data = json_decode($rawInput);

            if ($data === null) {
                throw new Exception("Error al decodificar JSON: " . json_last_error_msg());
            }

            $this->libro->id = $id;
            $this->libro->titulo = $data->titulo;
            $this->libro->autor = $data->autor;
            $this->libro->editorial = $data->editorial;
            $this->libro->anio_publicacion = $data->anio_publicacion;
            $this->libro->categoria_id = $data->categoria_id;
            $this->libro->estado_uso = $data->estado_uso;
            $this->libro->isbn = $data->isbn;
            $this->libro->copias_disponibles = $data->copias_disponibles;
            $this->libro->disponible = $data->disponible;
            $this->libro->imagen_portada = $data->imagen_portada;
            $this->libro->fecha_registro = date('Y-m-d H:i:s');

            $result = $this->libro->actualizarLibro();

            if ($result['success']) {
                http_response_code(200);
                echo json_encode($result);
            } else {
                http_response_code(503);
                echo json_encode($result);
            }

        }catch (Exception $e){
            error_log("Error al actualizar el libro: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }


    public function notFoundResponse(){
        http_response_code(404);
        echo json_encode(array("message" => "Ruta no encontrada."));
    }
}

$controller = new LibroController();
$controller->handleRequest();