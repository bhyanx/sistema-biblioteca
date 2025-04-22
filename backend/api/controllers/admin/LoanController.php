<?php

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../models/Prestamo.php';

Class PrestamoController {
    private $db;
    private $prestamo;
    private $base_url;
    private $upload_dir;


/**
 * PrestamoController constructor.
 * Initializes the database connection and sets up the Prestamo model.
 * Sets the base URL for the application and configures response headers.
 */

    public function __construct(){
        $database = new Database();
        $db = $database->getConnection();
        $this->prestamo = new Prestamo($db);

        $this->base_url = 'http://localhost/sistema-biblioteca';

        header("content-type: application/json; charset=UTF-8");
    }

    public function handleRequest(){
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = explode('/', $uri);

        $requestMethod = $_SERVER["REQUEST_METHOD"];

        $id = null;
        if (isset($uri[6]) && is_numeric($uri[6])) {
            $id = (int) $uri[6];
        }

        switch ($requestMethod){
            case 'GET':
                if ($id) {
                    $this->leerPrestamoPorId($id);
                } else {
                    $this->leerPrestamos();
                }
                break;
            case 'POST':
                $this->crearPrestamo();
                break;
            case 'PUT':
                if ($id) {
                    $this->actualizarPrestamo($id);
                } else {
                    $this->notFoundResponse();
                }
                break;
            
            default:
                $this->notFoundResponse();
                break;
        }
    }

    private function leerPrestamos(){

        $stmt = $this->prestamo->leerPrestamos();
        $num = $stmt->rowCount();

        if($num > 0){
            $prestamos_arr = array();
            $prestamos_arr["prestamos"] = array();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);

                $prestamo_item = array(
                    "id" => $id,
                    "libro_id" => $libro_id,
                    "lector_id" => $lector_id,
                    "fecha_prestamo" => $fecha_prestamo,
                    "fecha_devolucion_esperada" => $fecha_devolucion_esperada,
                    "fecha_devolucion_real" => $fecha_devolucion_real,
                    "estado" => $estado
                );

                array_push($prestamos_arr["prestamos"], $prestamo_item);
            }

            http_response_code(200);
            echo json_encode($prestamos_arr);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "No se encontraron prestamos."));
        }
    }

    private function leerPrestamoPorId($id){

        $this->prestamo->id = $id;

        if ($this->prestamo->leerPrestamoPorId()) {
            $prestamo_arr = array(
                "id" => $this->prestamo->id,
                "libro_id" => $this->prestamo->libro_id,
                "lector_id" => $this->prestamo->lector_id,
                "fecha_prestamo" => $this->prestamo->fecha_prestamo,
                "fecha_devolucion_esperada" => $this->prestamo->fecha_devolucion_esperada,
                "fecha_devolucion_real" => $this->prestamo->fecha_devolucion_real,
                "estado" => $this->prestamo->estado,
            );

            http_response_code(200);
            echo json_encode($prestamo_arr);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Prestamo no encontrado."));
        }
    }

    private function crearPrestamo(){

        $data = json_decode(file_get_contents("php://input"));

        if(
            !empty($data->libro_id) &&
            !empty($data->lector_id) &&
            !empty($data->fecha_prestamo) &&
            !empty($data->fecha_devolucion_esperada) &&
            !empty($data->estado)
        ) {
            if (!filter_var($data->email, FILTER_VALIDATE_EMAIL))
            {
                http_response_code(400);
                echo json_encode(array("message" => "El formato del email es inválido."));
                return;
            }

            $this->prestamo->libro_id = $data->libro_id;
            $this->prestamo->lector_id = $data->lector_id;
            $this->prestamo->fecha_prestamo = date('Y-m-d H:i:s');
            $this->prestamo->fecha_devolucion_esperada = $data->fecha_devolucion_esperada;
            $this->prestamo->estado = $data->estado;

            if ($this->prestamo->crearPrestamo()){
                http_response_code(201);
                echo json_encode(array("message" => "Prestamo creado con éxito."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "No se pudo crear el prestamo."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Datos incompletos."));
        }
    }

    private function actualizarPrestamo($id){
        try{
            $rawInput = file_get_contents('php://input');
            $data = json_decode($rawInput);

            if ($data === null){
                throw new Exception("Error al decodificar JSON: " . json_last_error_msg());
            }

            $this->prestamo->id = $id;
            $this->prestamo->libro_id = $data->libro_id;
            $this->prestamo->lector_id = $data->lector_id;
            $this->prestamo->fecha_devolucion_esperada = $data->fecha_devolucion_esperada;
            $this->prestamo->fecha_devolucion_real = $data->fecha_devolucion_real;
            $this->prestamo->estado = $data->estado;

            $result = $this->prestamo->actualizarPrestamo();

            if ($result['success']){
                http_response_code(200);
                echo json_encode($result);
            } else {
                http_response_code(503);
                echo json_encode($result);
            }
        } catch (Exception $e) {
            error_log("Error al actualizar el prestamo: " .$e->getMessage());
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

$controller = new PrestamoController();
$controller->handleRequest();