-- Crear base de datos
CREATE DATABASE IF NOT EXISTS biblioteca;
USE biblioteca;

-- Tabla de categorías
CREATE TABLE IF NOT EXISTS categorias (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(50) UNIQUE NOT NULL
);

-- Tabla de administradores
CREATE TABLE IF NOT EXISTS administradores (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  contrasena VARCHAR(255) NOT NULL,
  fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de lectores
CREATE TABLE IF NOT EXISTS lectores (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  dni VARCHAR(20) UNIQUE NOT NULL,
  telefono VARCHAR(20),
  direccion VARCHAR(255),
  email VARCHAR(100),
  fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de libros
CREATE TABLE IF NOT EXISTS libros (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(100) NOT NULL,
  autor VARCHAR(100) NOT NULL,
  editorial VARCHAR(100),
  anio_publicacion INT,
  categoria_id INT,
  estado_uso ENUM('nuevo', 'bueno', 'regular', 'deteriorado') DEFAULT 'bueno',
  isbn VARCHAR(20) UNIQUE,
  copias_disponibles INT NOT NULL DEFAULT 1,
  disponible BOOLEAN DEFAULT TRUE,
  imagen_portada VARCHAR(255),
  fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE SET NULL
);

-- Tabla de préstamos
CREATE TABLE IF NOT EXISTS prestamos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  libro_id INT NOT NULL,
  lector_id INT NOT NULL,
  fecha_prestamo DATE NOT NULL,
  fecha_devolucion_esperada DATE NOT NULL,
  fecha_devolucion_real DATE,
  estado ENUM('activo', 'devuelto', 'atrasado') DEFAULT 'activo',
  FOREIGN KEY (libro_id) REFERENCES libros(id) ON DELETE CASCADE,
  FOREIGN KEY (lector_id) REFERENCES lectores(id) ON DELETE CASCADE
);

-- Insertar categorías
INSERT INTO categorias (nombre) VALUES 
('Novela'), 
('Distopía'), 
('Fábula');

-- Insertar libros
INSERT INTO libros (titulo, autor, editorial, anio_publicacion, categoria_id, estado_uso, isbn, copias_disponibles, disponible, imagen_portada)
VALUES
('Cien años de soledad', 'Gabriel García Márquez', 'Editorial Sudamericana', 1967, 1, 'bueno', '978-0307474728', 3, TRUE, 'portadas/cien_anos.jpg'),
('1984', 'George Orwell', 'Secker & Warburg', 1949, 2, 'nuevo', '978-0451524935', 5, TRUE, 'portadas/1984.jpg'),
('El principito', 'Antoine de Saint-Exupéry', 'Reynal & Hitchcock', 1943, 3, 'regular', '978-0156012195', 0, FALSE, 'portadas/principito.jpg');

-- Insertar administrador predeterminado (TU USUARIO)
INSERT INTO administradores (nombre, email, contrasena)
VALUES ('Bryan Smick Sanchez Garcia', 'bhyanxdev@gmail.com', 'admin123');
