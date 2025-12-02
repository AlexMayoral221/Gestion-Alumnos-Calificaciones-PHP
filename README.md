# 📚 Sistema de Gestión de Calificaciones
Plataforma simple para la administración de alumnos y sus notas parciales.

## Descripción
Este es un sistema web básico diseñado para profesores y administradores que necesitan llevar un registro de sus alumnos y las calificaciones obtenidas en tres parciales. El objetivo principal es proporcionar una interfaz sencilla para:

Registrar nuevos alumnos.
Editar los nombres de los alumnos y actualizar sus calificaciones de los tres parciales (P1, P2, P3).
Eliminar registros de alumnos.
Calcular el promedio final de cada alumno automáticamente.
El sistema utiliza la base de datos para asegurar la persistencia y la consistencia de los datos, evitando la duplicación de registros de calificaciones.
Tecnologías Clave

- Backend: 
PHP (Lenguaje de programación principal).

- Base de Datos: 
MySQL (Para almacenar alumnos y calificaciones).

- Estilos y Frontend: 
Tailwind CSS (Para un diseño limpio y responsivo).

## Instrucciones de Uso
Para poner a funcionar la aplicación en tu entorno local:
- Clonación: Clona o descarga el código fuente en el directorio principal de tu servidor web.
- Base de Datos (BD):
- Crea una base de datos MySQL (por ejemplo, llamada gestion_notas).
- Asegúrate de configurar correctamente las credenciales de conexión en el archivo bd.php.

Acceso: 
- Abre tu navegador y navega a la URL de la carpeta donde colocaste los archivos.

## Requisitos
Para poder ejecutar la plataforma en tu servidor local, necesitas:
Entorno Integrado: Un entorno de desarrollo local como XAMPP, WAMP o MAMP, que proporciona el servidor web (Apache) y la base de datos (MySQL).
PHP 
Base de Datos: MySQL o MariaDB.
Herramientas de Desarrollo: Un editor de código (ej. VS Code, Sublime Text) para configurar los archivos de conexión y realizar modificaciones.

El sistema presenta una interfaz limpia y responsive, adaptada a dispositivos móviles.
Muestra la tabla principal con el promedio calculado y botones de acción.
Muestra el formulario para modificar el nombre y las notas parciales de un alumno.

## Créditos
Este proyecto fue desarrollado utilizando PHP, MySQL y el framework Tailwind CSS.

## Autor: 
Alex Mayoral
