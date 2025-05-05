<?php
//Conexion a la base de datos
$servername = "localhost";
$username = "root";
$password = "juanluna12345";
$dbname = "gestor_inventario";

$conexion = new mysqli($servername, $username, $password, $dbname);
$conexion->set_charset("utf8");

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}
?>