
<?php
// Obtener la ruta solicitada
$request_uri = $_SERVER['REQUEST_URI'];
$base_path = '/sistema-biblioteca/backend/api';

// Remover el base_path de la URI
$path = str_replace($base_path, '', $request_uri);

// Limpiar la ruta
$path = trim($path, '/');

// Dividir la ruta en segmentos
$segments = explode('/', $path);

// Si la primera segmento es 'admin', cargar el controlador de administradores
if ($segments[0] === 'admin') {
    require_once 'controllers/admin/AdminController.php';
    exit();
}

// Si la ruta está vacía o es solo '/'
if (empty($path) || $path === '/') {
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode(['message' => 'API funcionando correctamente']);
    exit();
}

// Si la ruta no existe, devolver error 404
http_response_code(404);
header("Content-Type: application/json; charset=UTF-8");
echo json_encode(['error' => 'Endpoint no encontrado']);