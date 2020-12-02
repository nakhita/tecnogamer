<?php
    include("../conexion_bd.php");
  
    header("Content-Type: application/json; charset=UTF-8");

    $conexion = obtenerConexion();

    $statement=$conexion->prepare("SELECT c.id, c.nombre FROM categoria c");
    $statement->execute();
    if (!$statement) {
        echo 'Error al ejecutar la consulta';
    } else {
        $categorias = $statement->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($categorias);
    }
    $conexion = null;
