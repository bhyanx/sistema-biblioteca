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

        header("content-type: application/json; charset=UTF-8");
    }
}