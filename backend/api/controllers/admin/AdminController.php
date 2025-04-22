<?php

use LDAP\Result;

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../models/Administradores.php';

class AdministradorController
{
    private $db;
    private $administrador;
    private $base_url;
    private $upload_dir;

    public function __construct()
    {
        $database = new Database();
        $db = $database->getConnection();
        $this->administrador = new Administrador($db);

        $this->base_url = 'http://localhost/sistema-biblioteca';

        header("content-type: application/json; charset=UTF-8");
    }

    public function handleRequest()
    {
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
                    $this->leerAdministradorPorId($id);
                } else {
                    $this->leerAdministradores();
                }
                break;
            case 'POST':
                $this->crearAdministrador();
                break;
            case 'PUT':
                if ($id) {
                    $this->actualizarAdministrador($id);
                } else {
                    $this->notFoundResponse();
                }
                break;

            default:
                $this->notFoundResponse();
                break;
        }
    }

    private function leerAdministradores()
    {
        $stmt = $this->administrador->leerAdministradores();
        $num = $stmt->rowCount();

        if ($num > 0) {
            $administradores_arr = array();
            $administradores_arr["usersAdmin"] = array();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);

                $administrador_item = array(
                    "id" => $id,
                    "nombre" => $nombre,
                    "email" => $email,
                    "contrasena" => $contrasena,
                    "fecha_registro" => $fecha_registro
                );

                array_push($administradores_arr["usersAdmin"], $administrador_item);
            }

            http_response_code(200);
            echo json_encode($administradores_arr);
        } else {
            http_response_code(404);
            echo json_encode(array("usersAdmin" => "No se encontraron administradores."));
        }
    }

    private function leerAdministradorPorId($id)
    {

        $this->administrador->id = $id;

        if ($this->administrador->leerAdministradorPorId()) {
            $admin_arr = array(
                "id" =>  $this->administrador->id,
                "nombre" => $this->administrador->nombre,
                "email" => $this->administrador->email,
                "contrasena" => $this->administrador->contrasena,
                "fecha_registro" => $this->administrador->fecha_registro
            );

            http_response_code(200);
            echo json_encode($admin_arr);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Administrador no encontrado."));
        }
    }

    private function crearAdministrador()
    {
        $data = json_decode(file_get_contents("php://input"));

        if (
            !empty($data->nombre) &&
            !empty($data->email) &&
            !empty($data->contrasena)
        ) {
            if (!filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
                http_response_code(400);
                echo json_encode(array("message" => "El formato del email es inválido."));
                return;
            }

            $this->administrador->nombre = $data->nombre;
            $this->administrador->email = $data->email;
            $this->administrador->contrasena = $data->contrasena;
            $this->administrador->fecha_registro = date('Y-m-d H:i:s');

            if ($this->administrador->crearAdministrador()) {
                http_response_code(201);
                echo json_encode(array("message" => "Administrador creado con éxito."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "No se pudo crear el administrador."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "No se puede crear el administrador. Datos incompletos."));
        }
    }

    private function actualizarAdministrador($id)
    {
        try {
            $rawInput = file_get_contents('php://input');
            $data = json_decode($rawInput);

            if ($data === null) {
                throw new Exception('Error al decodificar JSON: ' . json_last_error_msg());
            }

            $this->administrador->id = $id;
            $this->administrador->nombre = $data->nombre ?? null;
            $this->administrador->email = $data->email ?? null;
            $this->administrador->contrasena = $data->contrasena ?? null;

            $result = $this->administrador->actualizarAdministrador();

            if ($result['success']) {
                http_response_code(200);
                echo json_encode($result);
            } else {
                http_response_code(503);
                echo json_encode($result);
            }
        } catch (Exception $e) {
            error_log("Error al actualizar el administrador: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }



    private function notFoundResponse()
    {
        http_response_code(404);
        echo json_encode(array("message" => "Ruta no encontrada."));
    }
}

$controller = new AdministradorController();
$controller->handleRequest();
