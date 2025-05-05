<?php
//Conexion a la base de datos
include('../../database/conexion.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
</head>

<body>
    <?php
    if ($conexion) {
        echo "<script>alert('Conexión exitosa a la base de datos');</script>";
    } else {
        echo "<script>alert('Error al conectar a la base de datos');</script>";
    }
    ?>
</body>

</html>