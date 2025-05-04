<?php
//Conexion a la base de datos ssdsds
$servername = "localhost";
$username = "root";
$password = "123456";
$dbname = "rukart";

$conexion = new mysqli($servername, $username, $password, $dbname);
$conexion->set_charset("utf8");

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}
?>