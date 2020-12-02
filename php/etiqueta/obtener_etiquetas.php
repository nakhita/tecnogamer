<?php
    include("../conexion_bd.php");
  
    header("Content-Type: application/json; charset=UTF-8");

    $conexion = obtenerConexion();

    $statement=$conexion->prepare("SELECT e.id, e.nombre, c.id AS idCategoria, c.nombre AS nombreCategoria FROM etiqueta e INNER JOIN categoria c ON e.id_categoria = c.id");
    $statement->execute();
    if (!$statement) {
        echo 'Error al ejecutar la consulta';
    } else {
        $etiquetas = $statement->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($etiquetas);
    } 
    $conexion = null;
