<?php
// includes/db.php
$host = "localhost";
$usuario = "root";
$contraseña = "";
$base_datos = "tienda_online";

$conexion = new mysqli($host, $usuario, $contraseña, $base_datos);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$conexion->set_charset("utf8mb4");
?>
