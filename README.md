# 📚 Sistema Biblioteca

Este es un proyecto para la gestión de una biblioteca, desarrollado en **PHP** con una arquitectura basada en el patrón MVC (Modelo-Vista-Controlador).

Incluye funcionalidades como:

- 📖 Registro, edición y eliminación de libros.
- 👤 Administración de usuarios lectores y préstamos.
- 📊 Panel de control exclusivo para el administrador.
- 🔍 Búsqueda rápida de libros disponibles.

---

## 🛠️ Requisitos previos

Asegúrate de tener instalados los siguientes programas:

- **PHP** 🌟 [(versión 7.4 o superior)](https://www.php.net/)
- **Composer** 🎵 [(si el proyecto usa dependencias)](https://getcomposer.org/)
- **MySQL** 🗃️ [(para la base de datos)](https://www.mysql.com/)
- **WAMP / XAMPP / Laragon** 💻 [(servidor local compatible con PHP)](https://www.apachefriends.org/)
- **Git** 🌳 [(para clonar el repositorio y colaborar)](https://git-scm.com/)

---

## ⚙️ Configuración del proyecto

Sigue estos pasos para instalar y ejecutar el sistema en tu entorno local:

### 1. Clonar el repositorio 📂

```bash
git clone https://github.com/usuario/sistema-biblioteca.git
cd sistema-biblioteca
```

### 2. Crear la base de datos 🗄️

- Accede a phpMyAdmin o MySQL.
- Crea una base de datos llamada `sistema_biblioteca`.
- Importa el archivo `database.sql` incluido en la carpeta del proyecto:

```bash
mysql -u root -p sistema_biblioteca < database.sql
```

### 3. Configurar la conexión a la base de datos 🔧

Edita el archivo ubicado en `config/Database.php`:

```php
<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sistema_biblioteca');
?>
```

### 4. Ejecutar el servidor local 🖥️

#### Opción A: Con XAMPP/WAMP

- Copia el proyecto dentro de la carpeta `htdocs` o `www`.
- Accede desde el navegador a:

```
http://localhost/sistema-biblioteca/public
```

#### Opción B: Usar servidor embebido de PHP

```bash
php -S localhost:8000 -t public/
```

### 5. Iniciar sesión 🔐

- Usuario: `admin`
- Contraseña: `admin123`

*(Puedes modificar estos datos directamente en la base de datos si lo deseas)*

---

## 🧪 Probar la API (Opcional)

Puedes utilizar herramientas como [Postman](https://www.postman.com/) 📦 o `curl` para probar las rutas disponibles.

---

## 📸 Capturas de pantalla

| Página de inicio | Dashboard admin |
|------------------|------------------|
| ![Inicio](public/images/captura1.png) | ![Admin](public/images/captura2.png) |

---

## 👥 ¿Cómo colaborar?

### 1. Crear una nueva rama 🌿

```bash
git checkout -b feature/tu-nueva-funcionalidad
```

### 2. Realiza tus cambios ✏️

Haz las modificaciones necesarias y guarda los archivos.

### 3. Confirma los cambios ✔️

```bash
git add .
git commit -m "Descripción clara de los cambios"
```

### 4. Sube tu rama 🚀

```bash
git push origin feature/tu-nueva-funcionalidad
```

### 5. Crea un Pull Request 🔄

- Ve al repositorio en GitHub.
- Crea un **Pull Request** hacia la rama principal (`main` o `master`).
- Describe qué hiciste y por qué.

---

## 📝 Notas importantes

- ⚠️ **No subas archivos sensibles**: `.env`, contraseñas, credenciales o configuraciones locales deben estar listadas en `.gitignore`.
- 🔄 **Mantén tu rama actualizada**:

```bash
git pull origin main
```

---

## 📌 Estructura del proyecto

```
sistema-biblioteca/
│
├── config/           # Configuración de base de datos y constantes
├── controllers/      # Lógica del sistema (MVC)
├── models/           # Consultas y conexión con la base de datos
├── views/            # Archivos HTML/PHP del frontend
├── public/           # Carpeta pública (acceso desde el navegador)
├── uploads/          # Carpeta para archivos subidos (si aplica)
├── database.sql      # Script SQL de la base de datos
└── index.php         # Punto de entrada principal
```

---

## 📞 Contacto

Si tienes dudas, sugerencias o detectas errores, no dudes en escribirnos:

- 📧 Email: bhyanxdev@gmail.com  
- 🧑‍💻 Autor: Bryan Smick Sánchez García

---

## 🌟 Licencia

Este proyecto es de uso libre para fines educativos y puede ser adaptado y mejorado por la comunidad.  
Si lo usas o lo modificas, ¡me encantaría ver tu versión! 😊

---

**¡Gracias por visitar este repositorio!** 🙌✨  
Recuerda dejar una ⭐ si te resultó útil.
