// GUIA MODELS 

<?php
class Product {
    private $conn;
    private $table_name = "productos";

    // Propiedades del objeto
    public $id;
    public $nombre;
    public $descripcion;
    public $precio;
    public $categoria;
    public $imagen;
    public $stock;
    public $fecha_creacion;
    public $fecha_actualizacion;

    public function __construct($db) {
        $this->conn = $db;
    }

    // READ all products
    public function read() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY fecha_creacion DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // READ single product
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->nombre = $row['nombre'];
            $this->descripcion = $row['descripcion'];
            $this->precio = $row['precio'];
            $this->categoria = $row['categoria'];
            $this->imagen = $row['imagen'];
            $this->stock = $row['stock'];
            $this->fecha_creacion = $row['fecha_creacion'];
            $this->fecha_actualizacion = $row['fecha_actualizacion'];
            return true;
        }
        return false;
    }

    // CREATE product
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET nombre = :nombre, 
                      descripcion = :descripcion, 
                      precio = :precio, 
                      categoria = :categoria, 
                      imagen = :imagen, 
                      stock = :stock";

        $stmt = $this->conn->prepare($query);

        // Sanitizar datos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->descripcion = htmlspecialchars(strip_tags($this->descripcion));
        $this->precio = htmlspecialchars(strip_tags($this->precio));
        $this->categoria = htmlspecialchars(strip_tags($this->categoria));
        $this->imagen = htmlspecialchars(strip_tags($this->imagen));
        $this->stock = htmlspecialchars(strip_tags($this->stock));

        // Vincular valores
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':descripcion', $this->descripcion);
        $stmt->bindParam(':precio', $this->precio);
        $stmt->bindParam(':categoria', $this->categoria);
        $stmt->bindParam(':imagen', $this->imagen);
        $stmt->bindParam(':stock', $this->stock);

        // Ejecutar consulta
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // UPDATE product
    public function update() {
        // Validate required fields
        if(empty($this->nombre) || empty($this->precio) || empty($this->stock)) {
            throw new Exception("Nombre, precio y stock son campos requeridos");
        }

        // Validate numeric fields
        if(!is_numeric($this->precio) || !is_numeric($this->stock)) {
            throw new Exception("Precio y stock deben ser valores numéricos");
        }

        $query = "UPDATE " . $this->table_name . "
                  SET nombre = :nombre,
                      descripcion = :descripcion,
                      precio = :precio,
                      categoria = :categoria,
                      imagen = :imagen,
                      stock = :stock,
                      fecha_actualizacion = CURRENT_TIMESTAMP
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Sanitizar datos
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->descripcion = htmlspecialchars(strip_tags($this->descripcion));
        $this->precio = htmlspecialchars(strip_tags($this->precio));
        $this->categoria = htmlspecialchars(strip_tags($this->categoria));
        $this->imagen = htmlspecialchars(strip_tags($this->imagen));
        $this->stock = htmlspecialchars(strip_tags($this->stock));

        // Vincular valores
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':descripcion', $this->descripcion);
        $stmt->bindParam(':precio', $this->precio);
        $stmt->bindParam(':categoria', $this->categoria);
        $stmt->bindParam(':imagen', $this->imagen);
        $stmt->bindParam(':stock', $this->stock);

        try {
            // Ejecutar consulta
            if($stmt->execute()) {
                // Verificar si se actualizó algún registro
                if($stmt->rowCount() > 0) {
                    return [
                        'success' => true,
                        'message' => 'Producto actualizado exitosamente',
                        'rows_affected' => $stmt->rowCount()
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'No se encontró el producto para actualizar',
                        'rows_affected' => 0
                    ];
                }
            }
            
            return [
                'success' => false,
                'message' => 'Error al actualizar el producto',
                'error' => $stmt->errorInfo()
            ];
        } catch (PDOException $e) {
            throw new Exception("Error en la base de datos: " . $e->getMessage());
        }
    }

    // DELETE product
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        // Sanitizar id
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Vincular id
        $stmt->bindParam(':id', $this->id);

        // Ejecutar consulta
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}




















require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Product.php';

class ProductController
{
    private $db;
    private $product;
    private $base_url;
    private $upload_dir;

    public function __construct()
    {
        $database = new Database();
        $db = $database->getConnection();
        $this->product = new Product($db);

        // Configurar rutas base
        $this->base_url = 'http://localhost/proyecto-crud';
        $this->upload_dir = __DIR__ . '/../../uploads/products/';

        // Crear directorio si no existe
        if (!file_exists($this->upload_dir)) {
            mkdir($this->upload_dir, 0777, true);
        }

        // Solo configurar el tipo de contenido
        header("Content-Type: application/json; charset=UTF-8");
    }

    private function handleImageUrl($imageUrl)
    {
        // Check if it's already a full URL
        if (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            return $imageUrl;
        }

        // Check if it's a relative path
        if (strpos($imageUrl, 'uploads/products/') !== false) {
            return $imageUrl;
        }

        // If it's just a filename, append the base URL
        if (!empty($imageUrl)) {
            return $this->base_url . '/backend/uploads/products/' . $imageUrl;
        }

        return null;
    }

    private function getFullImageUrl($imageName)
    {
        if (empty($imageName)) return null;
        return $this->handleImageUrl($imageName);
    }

    public function handleRequest()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = explode('/', $uri);

        $requestMethod = $_SERVER["REQUEST_METHOD"];

        // Si se proporciona un ID en la URL (para operaciones específicas)
        $id = null;
        if (isset($uri[5]) && is_numeric($uri[5])) {
            $id = (int) $uri[5];
        }

        switch ($requestMethod) {
            case 'GET':
                if ($id) {
                    $this->getProduct($id);
                } else {
                    $this->getAllProducts();
                }
                break;
            case 'POST':
                $this->createProduct();
                break;
            case 'PUT':
                if ($id) {
                    $this->updateProduct($id);
                } else {
                    $this->notFoundResponse();
                }
                break;
            case 'DELETE':
                if ($id) {
                    $this->deleteProduct($id);
                } else {
                    $this->notFoundResponse();
                }
                break;
            default:
                $this->notFoundResponse();
                break;
        }
    }

    private function getAllProducts()
    {
        $stmt = $this->product->read();
        $num = $stmt->rowCount();

        if ($num > 0) {
            $products_arr = array();
            $products_arr["records"] = array();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);

                $product_item = array(
                    "id" => $id,
                    "nombre" => $nombre,
                    "descripcion" => $descripcion,
                    "precio" => $precio,
                    "categoria" => $categoria,
                    "imagen" => $this->getFullImageUrl($imagen),
                    "stock" => $stock,
                    "fecha_creacion" => $fecha_creacion,
                    "fecha_actualizacion" => $fecha_actualizacion
                );

                array_push($products_arr["records"], $product_item);
            }

            http_response_code(200);
            echo json_encode($products_arr);
        } else {
            http_response_code(200);
            echo json_encode(array("records" => array(), "message" => "No se encontraron productos."));
        }
    }

    private function getProduct($id)
    {
        $this->product->id = $id;

        if ($this->product->readOne()) {
            $product_arr = array(
                "id" =>  $this->product->id,
                "nombre" => $this->product->nombre,
                "descripcion" => $this->product->descripcion,
                "precio" => $this->product->precio,
                "categoria" => $this->product->categoria,
                "imagen" => $this->getFullImageUrl($this->product->imagen),
                "stock" => $this->product->stock,
                "fecha_creacion" => $this->product->fecha_creacion,
                "fecha_actualizacion" => $this->product->fecha_actualizacion
            );

            http_response_code(200);
            echo json_encode($product_arr);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Producto no encontrado."));
        }
    }

    private function createProduct()
    {
        $data = json_decode(file_get_contents("php://input"));

        if (
            !empty($data->nombre) &&
            !empty($data->precio) &&
            !empty($data->categoria)
        ) {
            $this->product->nombre = $data->nombre;
            $this->product->descripcion = $data->descripcion ?? "";
            $this->product->precio = $data->precio;
            $this->product->categoria = $data->categoria;
            $this->product->imagen = $data->imagen ?? "";
            $this->product->stock = $data->stock ?? 0;

            if ($this->product->create()) {
                http_response_code(201);
                echo json_encode(array("message" => "Producto creado con éxito."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "No se pudo crear el producto."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "No se puede crear el producto. Datos incompletos."));
        }
    }

    private function updateProduct($id)
    {
        try {
            // Parse the JSON data
            $rawInput = file_get_contents('php://input');
            $data = json_decode($rawInput);

            if ($data === null) {
                throw new Exception('Invalid JSON data received');
            }

            // Handle image URL
            if (isset($data->imagen)) {
                $data->imagen = $this->handleImageUrl($data->imagen);
            }

            // Set product properties
            $this->product->id = $id;
            $this->product->nombre = $data->nombre ?? null;
            $this->product->descripcion = $data->descripcion ?? "";
            $this->product->precio = $data->precio ?? null;
            $this->product->categoria = $data->categoria ?? null;
            $this->product->imagen = $data->imagen ?? "";
            $this->product->stock = $data->stock ?? 0;

            // Update the product
            $result = $this->product->update();

            if ($result['success']) {
                http_response_code(200);
                echo json_encode($result);
            } else {
                http_response_code(400);
                echo json_encode($result);
            }
        } catch (Exception $e) {
            error_log("Error updating product: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    private function deleteProduct($id)
    {
        $this->product->id = $id;

        if ($this->product->delete()) {
            http_response_code(200);
            echo json_encode(array("message" => "Producto eliminado con éxito."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "No se pudo eliminar el producto."));
        }
    }

    private function notFoundResponse()
    {
        http_response_code(404);
        echo json_encode(array("message" => "Ruta no encontrada."));
    }
}

$controller = new ProductController();
$controller->handleRequest();