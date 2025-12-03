<?php
// Configuración de conexión 
$host = 'localhost';
$usuario = 'root';     
$password = 'db';       
$base_datos = 'db_lvasesorias';
$puerto = 3306;

// Crear conexión
$conexion = new mysqli($host, $usuario, $password, $base_datos, $puerto);

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$conexion->set_charset("utf8");

?>
